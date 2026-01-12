<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    /**
     * Exibe a interface principal de cotações (Bento UI)
     */
    public function index()
    {
        $companyId = auth()->user()->current_company_id;

        $products = Product::where('company_id', $companyId)
            ->with(['codes', 'category'])
            ->active()
            ->latest()
            ->get();

        $suppliers = Supplier::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        $totalProducts = $products->count();

        return view('quotes.index', compact('products', 'suppliers', 'totalProducts'));
    }

    /**
     * Armazena uma cotação rápida
     */
    public function storeQuick(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'product_search' => 'required_without:product_id|string',
            'supplier_id' => 'required|exists:suppliers,id',
            'price' => 'required|numeric|min:0',
            'ean' => 'nullable|string',
            'unit' => 'required|string',
        ]);

        $companyId = auth()->user()->current_company_id;

        // Se não tem product_id, cria um novo produto
        if (!$request->product_id) {
            $product = Product::create([
                'company_id' => $companyId,
                'name' => $request->product_search,
                'unit' => $request->unit,
                'is_active' => true,
            ]);

            // Se tem EAN, cria o código
            if ($request->ean) {
                $product->codes()->create([
                    'code' => $request->ean,
                    'type' => 'ean',
                ]);
            }

            $productId = $product->id;
        } else {
            $productId = $request->product_id;
        }

        // Cria ou encontra uma cotação ativa para hoje
        $quote = \App\Models\Quote::firstOrCreate(
            [
                'company_id' => $companyId,
                'status' => 'open',
                'created_at' => now()->toDateString(),
            ],
            [
                'created_by' => auth()->id(),
                'title' => 'Cotação ' . now()->format('d/m/Y'),
                'status' => 'open',
            ]
        );

        // Adiciona o item à cotação se ainda não existe
        $quoteItem = $quote->items()->firstOrCreate(
            ['product_id' => $productId],
            ['quantity' => 1]
        );

        // Cria ou atualiza a resposta do fornecedor
        $quoteResponse = $quote->responses()->firstOrCreate(
            ['supplier_id' => $request->supplier_id],
            ['status' => 'submitted', 'submitted_at' => now()]
        );

        // Cria ou atualiza o item da resposta
        $quoteResponse->items()->updateOrCreate(
            ['quote_item_id' => $quoteItem->id],
            ['unit_price' => $request->price]
        );

        // Recalcula o total da resposta
        $quoteResponse->calculateTotal();

        return redirect()->back()->with('success', 'Cotação registrada com sucesso!');
    }
}
