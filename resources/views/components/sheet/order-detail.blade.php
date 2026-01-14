{{--
    📦 ZePocket - Sheet de Detalhes do Pedido
--}}
<div id="sheet-order-detail" class="bottom-sheet" data-sheet="order-detail">
    <div class="sheet-backdrop" data-close-sheet></div>
    <div class="sheet-container max-h-[85vh]">
        <div class="sheet-content">
            <div class="sheet-header">
                <div class="sheet-handle"></div>
                <h2 class="sheet-title flex items-center justify-between">
                    <span>Pedido #<span id="order-detail-id">0</span></span>
                    <span id="order-detail-status" class="text-xs px-2 py-1 rounded-lg bg-slate-200 text-slate-600">Status</span>
                </h2>
            </div>
            <div class="sheet-body space-y-4 pb-8 overflow-y-auto">
                {{-- Fornecedor --}}
                <div class="bg-slate-50 p-4 rounded-xl">
                    <p class="text-[10px] text-slate-400 uppercase font-bold mb-1">Fornecedor</p>
                    <p id="order-detail-supplier" class="font-bold text-slate-800">-</p>
                    <p id="order-detail-whatsapp" class="text-xs text-slate-500 mt-1">-</p>
                </div>

                {{-- Itens --}}
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Itens do Pedido</p>
                    <div id="order-detail-items" class="space-y-2 max-h-[30vh] overflow-y-auto">
                        {{-- Itens serão renderizados via JS --}}
                    </div>
                </div>

                {{-- Totais --}}
                <div class="bg-slate-900 text-white p-4 rounded-xl">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm opacity-70">Total do Pedido</span>
                        <span id="order-detail-total" class="text-xl font-black">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-white/10">
                        <span class="text-sm opacity-70">Lucro Esperado</span>
                        <span id="order-detail-profit" class="text-lg font-bold text-emerald-400">R$ 0,00</span>
                    </div>
                </div>

                {{-- Data --}}
                <div class="text-center text-xs text-slate-400">
                    <p>Criado em <span id="order-detail-date">-</span></p>
                </div>

                {{-- Ações --}}
                <div class="grid grid-cols-2 gap-2">
                    <button data-action="clone-order" 
                            class="bg-slate-100 hover:bg-slate-200 text-slate-700 py-3 rounded-xl font-bold text-sm transition flex items-center justify-center gap-2">
                        <i data-lucide="copy" class="w-4 h-4"></i> Repetir Pedido
                    </button>
                    <button data-action="send-order-whatsapp" 
                            class="bg-green-500 hover:bg-green-600 text-white py-3 rounded-xl font-bold text-sm transition flex items-center justify-center gap-2">
                        <i data-lucide="send" class="w-4 h-4"></i> WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
