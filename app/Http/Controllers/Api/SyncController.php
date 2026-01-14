<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductUserSetting;
use App\Models\ProductSupplierPrice;
use App\Models\Supplier;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SyncController extends Controller
{
    /**
     * Verifica se há atualizações pendentes
     * GET /api/sync/check
     */
    public function check(Request $request): JsonResponse
    {
        $user = Auth::user();
        $lastSync = $request->input('last_sync');
        
        $needsUpdate = false;
        $serverVersion = $user->last_db_version;
        
        if ($lastSync && $serverVersion) {
            $lastSyncDate = Carbon::parse($lastSync);
            $serverDate = Carbon::parse($serverVersion);
            $needsUpdate = $serverDate->greaterThan($lastSyncDate);
        } else if ($serverVersion) {
            $needsUpdate = true;
        }
        
        return response()->json([
            'needs_update' => $needsUpdate,
            'server_version' => $serverVersion?->toIso8601String(),
            'last_sync' => $lastSync,
        ]);
    }

    /**
     * Baixa todos os dados do usuário para sincronização
     * GET /api/sync/pull
     */
    public function pull(Request $request): JsonResponse
    {
        $user = Auth::user();
        $userId = $user->id;
        
        // Produtos com configurações do usuário e melhor preço
        $products = Product::query()
            ->where(function($q) use ($userId) {
                $q->where('is_global', true)
                  ->orWhere('created_by', $userId)
                  ->orWhereHas('userSettings', fn($q) => $q->where('user_id', $userId))
                  ->orWhereHas('supplierPrices.supplier', fn($q) => $q->where('user_id', $userId));
            })
            ->with(['userSettings' => fn($q) => $q->where('user_id', $userId)])
            ->get()
            ->map(fn($p) => $p->toSyncArray($userId));
        
        // Fornecedores do usuário
        $suppliers = Supplier::forUser($userId)
            ->get()
            ->map(fn($s) => $s->toSyncArray());
        
        // Cotações (preços) vinculadas aos fornecedores do usuário
        $quotes = ProductSupplierPrice::whereHas('supplier', fn($q) => $q->where('user_id', $userId))
            ->with(['supplier:id,name', 'product:id,name'])
            ->get()
            ->map(fn($q) => $q->toSyncArray());
        
        // Pedidos recentes (últimos 90 dias)
        $orders = Order::forUser($userId)
            ->where('created_at', '>=', now()->subDays(90))
            ->with(['items.product:id,name,brand,unit', 'supplier:id,name'])
            ->recent()
            ->get()
            ->map(fn($o) => array_merge($o->toSyncArray(), [
                'items' => $o->items->map(fn($i) => $i->toSyncArray()),
            ]));
        
        // Atualiza timestamp de última sincronização
        $syncTimestamp = now();
        
        return response()->json([
            'products' => $products,
            'suppliers' => $suppliers,
            'quotes' => $quotes,
            'orders' => $orders,
            'sync_timestamp' => $syncTimestamp->toIso8601String(),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * Envia alterações locais para o servidor
     * POST /api/sync/push
     */
    public function push(Request $request): JsonResponse
    {
        $user = Auth::user();
        $userId = $user->id;
        $data = $request->validate([
            'changes' => 'required|array',
            'changes.*.type' => 'required|string|in:product,supplier,quote,order,product_setting',
            'changes.*.action' => 'required|string|in:create,update,delete',
            'changes.*.data' => 'required|array',
            'changes.*.local_id' => 'nullable|string',
        ]);

        $results = [];
        $hasChanges = false;

        foreach ($data['changes'] as $change) {
            try {
                $result = match($change['type']) {
                    'product' => $this->handleProductChange($change, $userId),
                    'supplier' => $this->handleSupplierChange($change, $userId),
                    'quote' => $this->handleQuoteChange($change, $userId),
                    'order' => $this->handleOrderChange($change, $userId),
                    'product_setting' => $this->handleProductSettingChange($change, $userId),
                    default => ['success' => false, 'error' => 'Tipo desconhecido'],
                };
                
                $results[] = array_merge(['local_id' => $change['local_id'] ?? null], $result);
                if ($result['success']) $hasChanges = true;
                
            } catch (\Exception $e) {
                $results[] = [
                    'local_id' => $change['local_id'] ?? null,
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        // Atualiza versão do banco do usuário se houve mudanças
        if ($hasChanges) {
            $user->last_db_version = now();
            $user->save();
        }

        return response()->json([
            'success' => true,
            'results' => $results,
            'server_version' => $user->last_db_version?->toIso8601String(),
        ]);
    }

    /**
     * Handler para mudanças de produto
     */
    protected function handleProductChange(array $change, int $userId): array
    {
        $data = $change['data'];
        $action = $change['action'];

        if ($action === 'create') {
            $product = Product::create([
                'name' => $data['name'],
                'brand' => $data['brand'] ?? null,
                'unit' => $data['unit'] ?? 'UN',
                'ean' => $data['ean'] ?? null,
                'image_url' => $data['image_url'] ?? null,
                'is_global' => false, // Novos produtos aguardam moderação
                'created_by' => $userId,
            ]);
            
            return ['success' => true, 'server_id' => $product->id, 'data' => $product->toSyncArray($userId)];
        }

        if ($action === 'update' && isset($data['id'])) {
            $product = Product::find($data['id']);
            if (!$product) return ['success' => false, 'error' => 'Produto não encontrado'];
            
            // Só permite editar se criou ou se é admin
            if ($product->created_by !== $userId && !$product->is_global) {
                return ['success' => false, 'error' => 'Sem permissão'];
            }
            
            $product->update([
                'name' => $data['name'] ?? $product->name,
                'brand' => $data['brand'] ?? $product->brand,
                'unit' => $data['unit'] ?? $product->unit,
                'ean' => $data['ean'] ?? $product->ean,
            ]);
            
            return ['success' => true, 'server_id' => $product->id, 'data' => $product->fresh()->toSyncArray($userId)];
        }

        return ['success' => false, 'error' => 'Ação inválida'];
    }

    /**
     * Handler para mudanças de fornecedor
     */
    protected function handleSupplierChange(array $change, int $userId): array
    {
        $data = $change['data'];
        $action = $change['action'];

        if ($action === 'create') {
            $supplier = Supplier::create([
                'user_id' => $userId,
                'name' => $data['name'],
                'contact_name' => $data['contact_name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'whatsapp' => $data['whatsapp'] ?? null,
                'notes' => $data['notes'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);
            
            return ['success' => true, 'server_id' => $supplier->id, 'data' => $supplier->toSyncArray()];
        }

        if ($action === 'update' && isset($data['id'])) {
            $supplier = Supplier::forUser($userId)->find($data['id']);
            if (!$supplier) return ['success' => false, 'error' => 'Fornecedor não encontrado'];
            
            $supplier->update($data);
            return ['success' => true, 'server_id' => $supplier->id, 'data' => $supplier->fresh()->toSyncArray()];
        }

        if ($action === 'delete' && isset($data['id'])) {
            $supplier = Supplier::forUser($userId)->find($data['id']);
            if ($supplier) $supplier->delete();
            return ['success' => true];
        }

        return ['success' => false, 'error' => 'Ação inválida'];
    }

    /**
     * Handler para mudanças de cotação (preço)
     */
    protected function handleQuoteChange(array $change, int $userId): array
    {
        $data = $change['data'];
        $action = $change['action'];

        // Verifica se o fornecedor pertence ao usuário
        $supplier = Supplier::forUser($userId)->find($data['supplier_id']);
        if (!$supplier) return ['success' => false, 'error' => 'Fornecedor não encontrado'];

        if ($action === 'create' || $action === 'update') {
            $existingQuote = ProductSupplierPrice::where('supplier_id', $data['supplier_id'])
                ->where('product_id', $data['product_id'])
                ->first();

            if ($existingQuote) {
                // Guarda preço anterior para variação
                $existingQuote->update([
                    'previous_price' => $existingQuote->cost_price,
                    'cost_price' => $data['cost_price'],
                    'last_quoted_at' => now(),
                ]);
                $quote = $existingQuote->fresh();
            } else {
                $quote = ProductSupplierPrice::create([
                    'supplier_id' => $data['supplier_id'],
                    'product_id' => $data['product_id'],
                    'cost_price' => $data['cost_price'],
                    'last_quoted_at' => now(),
                ]);
            }
            
            return ['success' => true, 'server_id' => $quote->id, 'data' => $quote->toSyncArray()];
        }

        if ($action === 'delete' && isset($data['id'])) {
            $quote = ProductSupplierPrice::whereHas('supplier', fn($q) => $q->where('user_id', $userId))
                ->find($data['id']);
            if ($quote) $quote->delete();
            return ['success' => true];
        }

        return ['success' => false, 'error' => 'Ação inválida'];
    }

    /**
     * Handler para mudanças de pedido
     */
    protected function handleOrderChange(array $change, int $userId): array
    {
        $data = $change['data'];
        $action = $change['action'];

        if ($action === 'create') {
            // Verifica fornecedor
            $supplier = Supplier::forUser($userId)->find($data['supplier_id']);
            if (!$supplier) return ['success' => false, 'error' => 'Fornecedor não encontrado'];

            $order = Order::create([
                'user_id' => $userId,
                'supplier_id' => $data['supplier_id'],
                'status' => $data['status'] ?? 'draft',
                'notes' => $data['notes'] ?? null,
                'generated_at' => now(),
            ]);

            // Adiciona itens
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $order->items()->create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_cost_snapshot' => $item['unit_cost_snapshot'],
                        'unit_sale_snapshot' => $item['unit_sale_snapshot'] ?? null,
                    ]);
                }
            }

            return ['success' => true, 'server_id' => $order->id, 'data' => array_merge(
                $order->fresh()->toSyncArray(),
                ['items' => $order->items->map(fn($i) => $i->toSyncArray())]
            )];
        }

        if ($action === 'update' && isset($data['id'])) {
            $order = Order::forUser($userId)->find($data['id']);
            if (!$order) return ['success' => false, 'error' => 'Pedido não encontrado'];
            
            $order->update([
                'status' => $data['status'] ?? $order->status,
                'notes' => $data['notes'] ?? $order->notes,
            ]);
            
            // Marca timestamps conforme status
            if ($data['status'] === 'sent' && !$order->sent_at) {
                $order->markAsSent();
            } else if ($data['status'] === 'completed' && !$order->completed_at) {
                $order->markAsCompleted();
            }
            
            return ['success' => true, 'server_id' => $order->id, 'data' => $order->fresh()->toSyncArray()];
        }

        return ['success' => false, 'error' => 'Ação inválida'];
    }

    /**
     * Handler para configurações de produto do usuário
     */
    protected function handleProductSettingChange(array $change, int $userId): array
    {
        $data = $change['data'];

        $setting = ProductUserSetting::updateOrCreate(
            [
                'user_id' => $userId,
                'product_id' => $data['product_id'],
            ],
            [
                'sale_price' => $data['sale_price'] ?? null,
                'min_stock' => $data['min_stock'] ?? null,
                'current_stock' => $data['current_stock'] ?? null,
            ]
        );

        return ['success' => true, 'server_id' => $setting->id];
    }

    /**
     * Marca o banco do usuário como atualizado
     * Chamado automaticamente após operações de escrita
     */
    public static function touchUserVersion(int $userId): void
    {
        \App\Models\User::where('id', $userId)->update([
            'last_db_version' => now(),
        ]);
    }
}
