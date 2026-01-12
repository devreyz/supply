{{-- 
    Widget: Convênios
    Lista de convênios aceitos
--}}
<div class="bento-widget bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-100" onclick="app.openSheet('drawer-convenios')">
    <div class="flex items-center gap-3">
        <div class="w-12 h-12 rounded-xl bg-indigo-500 text-white flex items-center justify-center">
            <i data-lucide="credit-card" class="w-6 h-6"></i>
        </div>
        <div class="flex-1">
            <h4 class="font-semibold text-slate-900">Convênios</h4>
            <p class="text-sm text-slate-500">Veja os planos aceitos</p>
        </div>
        <i data-lucide="chevron-right" class="w-5 h-5 text-indigo-400"></i>
    </div>
    
    <div class="mt-3 flex items-center gap-2 flex-wrap">
        <span class="px-2 py-1 bg-white rounded-full text-xs text-slate-600 shadow-sm">Unimed</span>
        <span class="px-2 py-1 bg-white rounded-full text-xs text-slate-600 shadow-sm">Bradesco</span>
        <span class="px-2 py-1 bg-white rounded-full text-xs text-slate-600 shadow-sm">SulAmérica</span>
        <span class="px-2 py-1 bg-white rounded-full text-xs text-slate-500 font-medium">+20</span>
    </div>
</div>
