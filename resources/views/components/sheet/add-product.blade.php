{{--
    📝 ZePocket - Sheet de Adicionar/Editar Produto
--}}
<div id="sheet-add-product" class="bottom-sheet" data-sheet="add-product">
    <div class="sheet-backdrop" data-close-sheet></div>
    <div class="sheet-container">
        <div class="sheet-content">
            <div class="sheet-header">
                <div class="sheet-handle"></div>
                <h2 class="sheet-title">
                    <span id="product-sheet-title">Novo Produto</span>
                </h2>
            </div>
            <div class="sheet-body space-y-4 pb-8">
                <input type="hidden" id="product-edit-id" value="">
                
                <div>
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">Nome do Produto *</label>
                    <input type="text" id="product-name" 
                           placeholder="Ex: Coca-Cola 2L"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">Marca</label>
                        <input type="text" id="product-brand" 
                               placeholder="Ex: Coca-Cola"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">Unidade</label>
                        <select id="product-unit" 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary">
                            <option value="UN">UN - Unidade</option>
                            <option value="KG">KG - Quilo</option>
                            <option value="CX">CX - Caixa</option>
                            <option value="PCT">PCT - Pacote</option>
                            <option value="FD">FD - Fardo</option>
                            <option value="L">L - Litro</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">Código de Barras (EAN)</label>
                    <input type="text" id="product-ean" 
                           placeholder="7891234567890"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-mono outline-none focus:ring-2 focus:ring-primary">
                </div>

                <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-200">
                    <h4 class="text-xs font-bold text-emerald-700 mb-3 flex items-center gap-2">
                        <i data-lucide="tag" class="w-4 h-4"></i> Meu Preço de Venda
                    </h4>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-emerald-600 font-bold">R$</span>
                        <input type="number" step="0.01" id="product-sale-price" 
                               placeholder="0,00"
                               class="w-full bg-white border border-emerald-200 rounded-xl py-3 pl-12 text-lg font-bold text-emerald-700 outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <p class="text-[10px] text-emerald-600 mt-2">Este preço é usado para calcular sua margem de lucro</p>
                </div>

                <button data-action="save-product" 
                        class="w-full bg-slate-900 hover:bg-black text-white font-bold py-3.5 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-5 h-5"></i> Salvar Produto
                </button>
            </div>
        </div>
    </div>
</div>
