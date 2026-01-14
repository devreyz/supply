<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductUserSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Lista produtos (com filtros)
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = Product::query();
        
        // Busca
        if ($search = $request->input('search')) {
            $query->search($search);
        }
        
        // Filtro por global/próprios
        if ($request->input('only_mine')) {
            $query->where('created_by', $user->id);
        }
        
        // Inclui configurações do usuário
        $query->with(['userSettings' => fn($q) => $q->where('user_id', $user->id)]);
        
        $products = $query->orderBy('name')->paginate($request->input('per_page', 50));
        
        return response()->json([
            'products' => $products->getCollection()->map(fn($p) => $p->toSyncArray($user->id)),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Busca global de produtos (para autocomplete)
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2']);
        
        $user = Auth::user();
        $products = Product::search($request->input('q'))
            ->where('is_global', true)
            ->orWhere('created_by', $user->id)
            ->limit(20)
            ->get()
            ->map(fn($p) => $p->toSyncArray($user->id));
        
        return response()->json(['products' => $products]);
    }

    /**
     * Cria novo produto
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'unit' => 'required|string|max:50',
            'ean' => 'nullable|string|max:20|unique:products,ean',
            'image_url' => 'nullable|url',
            'sale_price' => 'nullable|numeric|min:0',
        ]);
        
        $user = Auth::user();
        
        $product = Product::create([
            'name' => $data['name'],
            'brand' => $data['brand'] ?? null,
            'unit' => $data['unit'],
            'ean' => $data['ean'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'is_global' => false,
            'created_by' => $user->id,
        ]);
        
        // Cria configuração do usuário se enviou sale_price
        if (isset($data['sale_price'])) {
            ProductUserSetting::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'sale_price' => $data['sale_price'],
            ]);
        }
        
        SyncController::touchUserVersion($user->id);
        
        return response()->json([
            'success' => true,
            'product' => $product->fresh()->toSyncArray($user->id),
        ], 201);
    }

    /**
     * Atualiza produto
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'brand' => 'nullable|string|max:255',
            'unit' => 'sometimes|string|max:50',
            'ean' => 'nullable|string|max:20|unique:products,ean,' . $product->id,
            'sale_price' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
        ]);
        
        // Atualiza produto se é dono ou se é global
        if ($product->created_by === $user->id || !$product->is_global) {
            $product->update([
                'name' => $data['name'] ?? $product->name,
                'brand' => $data['brand'] ?? $product->brand,
                'unit' => $data['unit'] ?? $product->unit,
                'ean' => $data['ean'] ?? $product->ean,
            ]);
        }
        
        // Atualiza configurações do usuário
        if (isset($data['sale_price']) || isset($data['min_stock'])) {
            ProductUserSetting::updateOrCreate(
                ['user_id' => $user->id, 'product_id' => $product->id],
                [
                    'sale_price' => $data['sale_price'] ?? null,
                    'min_stock' => $data['min_stock'] ?? null,
                ]
            );
        }
        
        SyncController::touchUserVersion($user->id);
        
        return response()->json([
            'success' => true,
            'product' => $product->fresh()->toSyncArray($user->id),
        ]);
    }

    /**
     * Detalhes do produto com cotações
     */
    public function show(Product $product): JsonResponse
    {
        $user = Auth::user();
        
        $product->load([
            'userSettings' => fn($q) => $q->where('user_id', $user->id),
            'supplierPrices' => fn($q) => $q->whereHas('supplier', fn($s) => $s->where('user_id', $user->id)),
            'supplierPrices.supplier:id,name',
        ]);
        
        $quotes = $product->supplierPrices->map(fn($q) => $q->toSyncArray());
        
        return response()->json([
            'product' => $product->toSyncArray($user->id),
            'quotes' => $quotes,
        ]);
    }
}
