<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Busca produtos por nome ou código
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        $companyId = auth()->user()->current_company_id;

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where('company_id', $companyId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhereHas('codes', function ($codeQuery) use ($query) {
                        $codeQuery->where('code', 'like', "%{$query}%");
                    });
            })
            ->with('codes')
            ->active()
            ->limit(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'ean' => $product->codes->where('type', 'ean')->first()?->code,
                    'unit' => $product->unit,
                ];
            });

        return response()->json($products);
    }
}
