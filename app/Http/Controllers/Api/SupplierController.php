<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    /**
     * Lista fornecedores do usuário
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $suppliers = Supplier::forUser($user->id)
            ->when($request->input('active_only'), fn($q) => $q->active())
            ->orderBy('name')
            ->get()
            ->map(fn($s) => $s->toSyncArray());
        
        return response()->json(['suppliers' => $suppliers]);
    }

    /**
     * Cria fornecedor
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);
        
        $user = Auth::user();
        
        $supplier = Supplier::create([
            'user_id' => $user->id,
            ...$data,
            'is_active' => true,
        ]);
        
        SyncController::touchUserVersion($user->id);
        
        return response()->json([
            'success' => true,
            'supplier' => $supplier->toSyncArray(),
        ], 201);
    }

    /**
     * Atualiza fornecedor
     */
    public function update(Request $request, Supplier $supplier): JsonResponse
    {
        $user = Auth::user();
        
        // Verifica propriedade
        if ($supplier->user_id !== $user->id) {
            return response()->json(['error' => 'Sem permissão'], 403);
        }
        
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);
        
        $supplier->update($data);
        
        SyncController::touchUserVersion($user->id);
        
        return response()->json([
            'success' => true,
            'supplier' => $supplier->fresh()->toSyncArray(),
        ]);
    }

    /**
     * Remove fornecedor (soft delete)
     */
    public function destroy(Supplier $supplier): JsonResponse
    {
        $user = Auth::user();
        
        if ($supplier->user_id !== $user->id) {
            return response()->json(['error' => 'Sem permissão'], 403);
        }
        
        $supplier->delete();
        
        SyncController::touchUserVersion($user->id);
        
        return response()->json(['success' => true]);
    }

    /**
     * Detalhes do fornecedor com produtos cotados
     */
    public function show(Supplier $supplier): JsonResponse
    {
        $user = Auth::user();
        
        if ($supplier->user_id !== $user->id) {
            return response()->json(['error' => 'Sem permissão'], 403);
        }
        
        $supplier->load(['productPrices.product:id,name,brand,unit']);
        
        $quotes = $supplier->productPrices->map(fn($q) => $q->toSyncArray());
        
        return response()->json([
            'supplier' => $supplier->toSyncArray(),
            'quotes' => $quotes,
        ]);
    }
}
