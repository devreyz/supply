{{--
    📊 ZePocket - Sheet de Comparativo de Preços
--}}
<div id="sheet-price-compare" class="bottom-sheet" data-sheet="price-compare">
    <div class="sheet-backdrop" data-close-sheet></div>
    <div class="sheet-container max-h-[80vh]">
        <div class="sheet-content">
            <div class="sheet-header">
                <div class="sheet-handle"></div>
                <h2 class="sheet-title">Comparativo de Preços</h2>
            </div>
            <div class="sheet-body space-y-4 pb-8 overflow-y-auto">
                {{-- Produto --}}
                <div class="text-center border-b border-dashed border-slate-200 pb-4">
                    <h3 id="compare-product-name" class="font-bold text-lg text-slate-800">Produto</h3>
                    <p id="compare-product-brand" class="text-xs text-slate-500">Marca</p>
                </div>

                {{-- Lista de Preços por Fornecedor --}}
                <div id="compare-prices-list" class="space-y-3">
                    {{-- Preços serão renderizados via JS --}}
                </div>

                {{-- Melhor Opção --}}
                <div id="compare-best-option" class="hidden bg-emerald-50 border border-emerald-200 p-4 rounded-xl">
                    <div class="flex items-center gap-2 text-emerald-700 font-bold text-sm mb-2">
                        <i data-lucide="trophy" class="w-5 h-5"></i>
                        Melhor Preço
                    </div>
                    <p id="compare-best-supplier" class="font-black text-emerald-800">-</p>
                    <p id="compare-best-price" class="text-2xl font-black text-emerald-600">R$ 0,00</p>
                    <p id="compare-best-savings" class="text-xs text-emerald-600 mt-1">-</p>
                </div>

                {{-- Ação --}}
                <button data-action="add-best-to-cart" 
                        class="w-full bg-slate-900 hover:bg-black text-white font-bold py-3.5 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="shopping-cart" class="w-5 h-5"></i> Adicionar Melhor ao Carrinho
                </button>
            </div>
        </div>
    </div>
</div>
