<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductSupplierPrice;
use App\Models\ProductUserSetting;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class QuoteController extends Controller
{
    /**
     * Salva/atualiza cotação (preço de custo)
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'product_id' => 'required|exists:products,id',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
        ]);
        
        $user = Auth::user();
        
        // Verifica se fornecedor pertence ao usuário
        $supplier = Supplier::forUser($user->id)->find($data['supplier_id']);
        if (!$supplier) {
            return response()->json(['error' => 'Fornecedor não encontrado'], 404);
        }
        
        // Busca cotação existente
        $existingQuote = ProductSupplierPrice::where('supplier_id', $data['supplier_id'])
            ->where('product_id', $data['product_id'])
            ->first();
        
        if ($existingQuote) {
            // Atualiza mantendo histórico de preço anterior
            $existingQuote->update([
                'previous_price' => $existingQuote->cost_price,
                'cost_price' => $data['cost_price'],
                'last_quoted_at' => now(),
            ]);
            $quote = $existingQuote->fresh();
        } else {
            // Cria nova cotação
            $quote = ProductSupplierPrice::create([
                'supplier_id' => $data['supplier_id'],
                'product_id' => $data['product_id'],
                'cost_price' => $data['cost_price'],
                'last_quoted_at' => now(),
            ]);
        }
        
        // Atualiza preço de venda se enviado
        if (isset($data['sale_price'])) {
            ProductUserSetting::updateOrCreate(
                ['user_id' => $user->id, 'product_id' => $data['product_id']],
                ['sale_price' => $data['sale_price']]
            );
        }
        
        SyncController::touchUserVersion($user->id);
        
        // Carrega relacionamentos para resposta
        $quote->load(['supplier:id,name', 'product:id,name,brand,unit']);
        
        return response()->json([
            'success' => true,
            'quote' => $quote->toSyncArray(),
        ]);
    }

    /**
     * Lista cotações do usuário (por produto ou fornecedor)
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $query = ProductSupplierPrice::whereHas('supplier', fn($q) => $q->where('user_id', $user->id));
        
        if ($productId = $request->input('product_id')) {
            $query->where('product_id', $productId);
        }
        
        if ($supplierId = $request->input('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }
        
        $quotes = $query->with(['supplier:id,name', 'product:id,name,brand,unit'])
            ->orderBy('last_quoted_at', 'desc')
            ->get()
            ->map(fn($q) => $q->toSyncArray());
        
        return response()->json(['quotes' => $quotes]);
    }

    /**
     * Remove cotação
     */
    public function destroy(ProductSupplierPrice $quote): JsonResponse
    {
        $user = Auth::user();
        
        // Verifica se fornecedor pertence ao usuário
        if ($quote->supplier->user_id !== $user->id) {
            return response()->json(['error' => 'Sem permissão'], 403);
        }
        
        $quote->delete();
        
        SyncController::touchUserVersion($user->id);
        
        return response()->json(['success' => true]);
    }
}
