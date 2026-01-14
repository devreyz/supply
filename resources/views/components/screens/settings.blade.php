{{--
    ⚙️ ZePocket - Configurações
    Hierarquia: PRIMARY (da home)
--}}
<section class="page" id="page-zepocket-settings" data-level="primary">
    {{-- Header --}}
    <header class="app-header">
        <button class="icon-btn" data-back>
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </button>
        <h1 class="header-title">Configurações</h1>
    </header>

    <main class="page-content no-bottom-nav px-4 py-4 space-y-4">
        {{-- Sincronização --}}
        <div class="bento-card p-4">
            <h3 class="font-bold text-slate-800 mb-3 flex items-center gap-2">
                <i data-lucide="cloud" class="w-5 h-5 text-primary"></i>
                Sincronização
            </h3>
            <p class="text-xs text-slate-500 mb-3">Última sincronização: <span id="last-sync-time">Nunca</span></p>
            <button data-action="sync-data" 
                    class="w-full bg-primary hover:bg-primary/90 text-white py-2.5 rounded-xl font-bold text-sm transition flex items-center justify-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i> Sincronizar Agora
            </button>
        </div>

        {{-- Backup --}}
        <div class="bento-card p-4">
            <h3 class="font-bold text-slate-800 mb-3 flex items-center gap-2">
                <i data-lucide="hard-drive" class="w-5 h-5 text-amber-500"></i>
                Backup & Restauração
            </h3>
            <p class="text-xs text-slate-500 mb-3">
                Exporte seus dados para um arquivo JSON ou restaure de um backup anterior.
            </p>
            <div class="grid grid-cols-2 gap-2">
                <button data-action="export-backup" 
                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-xl font-bold text-sm transition flex items-center justify-center gap-2">
                    <i data-lucide="download" class="w-4 h-4"></i> Exportar
                </button>
                <label class="bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-xl font-bold text-sm transition flex items-center justify-center gap-2 cursor-pointer">
                    <i data-lucide="upload" class="w-4 h-4"></i> Restaurar
                    <input type="file" id="import-backup-input" accept=".json" class="hidden">
                </label>
            </div>
        </div>

        {{-- Dados Locais --}}
        <div class="bento-card p-4">
            <h3 class="font-bold text-slate-800 mb-3 flex items-center gap-2">
                <i data-lucide="database" class="w-5 h-5 text-emerald-500"></i>
                Dados Locais
            </h3>
            <div class="grid grid-cols-2 gap-3 text-center">
                <div class="bg-slate-50 p-3 rounded-xl">
                    <p id="count-products" class="text-2xl font-black text-slate-800">0</p>
                    <p class="text-[10px] text-slate-500 uppercase font-bold">Produtos</p>
                </div>
                <div class="bg-slate-50 p-3 rounded-xl">
                    <p id="count-suppliers" class="text-2xl font-black text-slate-800">0</p>
                    <p class="text-[10px] text-slate-500 uppercase font-bold">Fornecedores</p>
                </div>
                <div class="bg-slate-50 p-3 rounded-xl">
                    <p id="count-quotes" class="text-2xl font-black text-slate-800">0</p>
                    <p class="text-[10px] text-slate-500 uppercase font-bold">Cotações</p>
                </div>
                <div class="bg-slate-50 p-3 rounded-xl">
                    <p id="count-orders" class="text-2xl font-black text-slate-800">0</p>
                    <p class="text-[10px] text-slate-500 uppercase font-bold">Pedidos</p>
                </div>
            </div>
        </div>

        {{-- Limpar Dados --}}
        <div class="bento-card p-4 border-red-200">
            <h3 class="font-bold text-red-600 mb-3 flex items-center gap-2">
                <i data-lucide="trash-2" class="w-5 h-5"></i>
                Zona de Perigo
            </h3>
            <p class="text-xs text-slate-500 mb-3">
                Limpar todos os dados locais. Esta ação não pode ser desfeita.
            </p>
            <button data-action="clear-data" 
                    class="w-full bg-red-500 hover:bg-red-600 text-white py-2.5 rounded-xl font-bold text-sm transition">
                Limpar Todos os Dados
            </button>
        </div>

        {{-- Versão --}}
        <div class="text-center text-xs text-slate-400 py-4">
            <p>ZePocket v1.0.0</p>
            <p class="mt-1">Powered by Laravel + SPA Framework</p>
        </div>
    </main>
</section>
