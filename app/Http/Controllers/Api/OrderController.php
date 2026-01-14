<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\ProductSupplierPrice;
use App\Models\ProductUserSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Lista pedidos do usuário
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $query = Order::forUser($user->id);
        
        if ($status = $request->input('status')) {
            $query->status($status);
        }
        
        if ($supplierId = $request->input('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }
        
        $orders = $query->recent()
            ->with(['supplier:id,name', 'items.product:id,name,brand,unit'])
            ->paginate($request->input('per_page', 20));
        
        return response()->json([
            'orders' => $orders->getCollection()->map(fn($o) => array_merge(
                $o->toSyncArray(),
                ['items' => $o->items->map(fn($i) => $i->toSyncArray())]
            )),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /**
     * Cria pedido a partir do carrinho
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);
        
        $user = Auth::user();
        
        // Verifica fornecedor
        $supplier = Supplier::forUser($user->id)->find($data['supplier_id']);
        if (!$supplier) {
            return response()->json(['error' => 'Fornecedor não encontrado'], 404);
        }
        
        return DB::transaction(function () use ($data, $user, $supplier) {
            // Cria pedido
            $order = Order::create([
                'user_id' => $user->id,
                'supplier_id' => $supplier->id,
                'status' => Order::STATUS_DRAFT,
                'notes' => $data['notes'] ?? null,
                'generated_at' => now(),
            ]);
            
            // Adiciona itens
            foreach ($data['items'] as $itemData) {
                $product = Product::find($itemData['product_id']);
                
                // Busca preço do fornecedor
                $quote = ProductSupplierPrice::where('supplier_id', $supplier->id)
                    ->where('product_id', $product->id)
                    ->first();
                
                // Busca preço de venda do usuário
                $settings = ProductUserSetting::where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->first();
                
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $itemData['quantity'],
                    'unit_cost_snapshot' => $quote?->cost_price ?? 0,
                    'unit_sale_snapshot' => $settings?->sale_price,
                ]);
            }
            
            SyncController::touchUserVersion($user->id);
            
            $order->load(['items.product:id,name,brand,unit', 'supplier:id,name']);
            
            return response()->json([
                'success' => true,
                'order' => array_merge(
                    $order->toSyncArray(),
                    ['items' => $order->items->map(fn($i) => $i->toSyncArray())]
                ),
            ], 201);
        });
    }

    /**
     * Detalhes do pedido
     */
    public function show(Order $order): JsonResponse
    {
        $user = Auth::user();
        
        if ($order->user_id !== $user->id) {
            return response()->json(['error' => 'Sem permissão'], 403);
        }
        
        $order->load(['items.product:id,name,brand,unit', 'supplier']);
        
        return response()->json([
            'order' => array_merge(
                $order->toSyncArray(),
                [
                    'items' => $order->items->map(fn($i) => $i->toSyncArray()),
                    'whatsapp_text' => $order->toWhatsappText(),
                ]
            ),
        ]);
    }

    /**
     * Atualiza status do pedido
     */
    public function update(Request $request, Order $order): JsonResponse
    {
        $user = Auth::user();
        
        if ($order->user_id !== $user->id) {
            return response()->json(['error' => 'Sem permissão'], 403);
        }
        
        $data = $request->validate([
            'status' => 'sometimes|in:draft,sent,completed,cancelled',
            'notes' => 'nullable|string',
        ]);
        
        if (isset($data['status'])) {
            match($data['status']) {
                'sent' => $order->markAsSent(),
                'completed' => $order->markAsCompleted(),
                'cancelled' => $order->cancel(),
                default => $order->update(['status' => $data['status']]),
            };
        }
        
        if (isset($data['notes'])) {
            $order->update(['notes' => $data['notes']]);
        }
        
        SyncController::touchUserVersion($user->id);
        
        return response()->json([
            'success' => true,
            'order' => $order->fresh()->toSyncArray(),
        ]);
    }

    /**
     * Clona pedido (para recompra)
     */
    public function clone(Order $order): JsonResponse
    {
        $user = Auth::user();
        
        if ($order->user_id !== $user->id) {
            return response()->json(['error' => 'Sem permissão'], 403);
        }
        
        $items = $order->cloneItems();
        
        return response()->json([
            'success' => true,
            'items' => $items,
        ]);
    }

    /**
     * Gera texto para WhatsApp
     */
    public function whatsapp(Order $order): JsonResponse
    {
        $user = Auth::user();
        
        if ($order->user_id !== $user->id) {
            return response()->json(['error' => 'Sem permissão'], 403);
        }
        
        $order->load(['items.product', 'supplier']);
        
        $text = $order->toWhatsappText();
        $link = $order->supplier->whatsapp_link;
        
        if ($link) {
            $link .= '?text=' . urlencode($text);
        }
        
        return response()->json([
            'text' => $text,
            'whatsapp_link' => $link,
        ]);
    }
}
