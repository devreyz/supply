{{--
    🏢 ZePocket - Sheet de Adicionar/Editar Fornecedor
--}}
<div class="bottom-sheet" id="add-supplier">
    <div class="grabber-handle">
      <div class="grabber-bar"></div>
    </div>
    <div class="sheet-header">
      <h2 class="sheet-title">
        <span id="supplier-sheet-title">Novo Fornecedor</span>
    </h2>
    </div>
    <div class="sheet-body">
         <input type="hidden" id="supplier-edit-id" value="">
        
        <div>
            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">Nome da Empresa *</label>
            <input type="text" id="supplier-name" 
                    placeholder="Ex: Distribuidora ABC"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary">
        </div>

        <div>
            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">WhatsApp</label>
            <div class="relative">
                <i data-lucide="phone" class="absolute left-4 top-3.5 text-slate-400 w-5 h-5"></i>
                <input type="tel" id="supplier-whatsapp" 
                        placeholder="11999998888"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-12 outline-none focus:ring-2 focus:ring-primary">
            </div>
            <p class="text-[10px] text-slate-500 mt-1">Usado para enviar pedidos via WhatsApp</p>
        </div>

        <div>
            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">Anotações</label>
            <textarea id="supplier-notes" rows="3" 
                        placeholder="Observações sobre o fornecedor..."
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary resize-none"></textarea>
        </div>

        <div class="flex items-center gap-3 bg-slate-50 p-4 rounded-xl">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="supplier-active" checked class="sr-only peer">
                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
            </label>
            <div>
                <p class="font-bold text-sm text-slate-700">Fornecedor Ativo</p>
                <p class="text-[10px] text-slate-500">Aparecer nas opções de cotação</p>
            </div>
        </div>

        <button data-action="save-supplier" 
                class="w-full bg-slate-900 hover:bg-black text-white font-bold py-3.5 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2">
            <i data-lucide="check" class="w-5 h-5"></i> Salvar Fornecedor
        </button>
    </div>
</div>
