{{--
    🏢 ZePocket - Lista de Fornecedores
    Hierarquia: PRIMARY (da home)
--}}
<section class="page" id="page-suppliers" data-level="primary">
    {{-- Header --}}
    <header class="app-header">
        <button class="icon-btn" data-back>
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </button>
        <h1 class="header-title">Fornecedores</h1>
        <button class="icon-btn" data-sheet="add-supplier">
            <i data-lucide="plus" class="w-6 h-6"></i>
        </button>
    </header>

    <main class="page-content no-bottom-nav px-4 py-4">
        {{-- Novo Fornecedor Rápido --}}
        <div class="bento-card p-4 bg-slate-900 text-white mb-4">
            <h3 class="font-bold mb-2 flex items-center gap-2">
                <i data-lucide="building-2" class="w-5 h-5"></i>
                Novo Distribuidor
            </h3>
            <div class="flex gap-2">
                <input type="text" id="new-supplier-name" 
                       placeholder="Nome da empresa"
                       class="flex-1 bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 outline-none focus:border-primary">
                <button data-action="quick-add-supplier" 
                        class="bg-primary hover:bg-primary/90 px-4 py-2 rounded-lg font-bold text-sm transition">
                    Add
                </button>
            </div>
        </div>

        {{-- Lista de Fornecedores --}}
        <div id="suppliers-list" class="space-y-2 pb-20">
            {{-- Fornecedores serão renderizados via JS --}}
        </div>
    </main>
</section>
