{{--
    📋 ZePocket - Lista de Produtos
    Hierarquia: PRIMARY (da home)
--}}
<section class="page" id="page-products" data-level="primary">
    {{-- Header --}}
    <header class="app-header">
        <button class="icon-btn" data-back>
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </button>
        <h1 class="header-title">Meus Produtos</h1>
        <button class="icon-btn" data-sheet="add-product">
            <i data-lucide="plus" class="w-6 h-6"></i>
        </button>
    </header>

    <main class="page-content no-bottom-nav px-4 py-4">
        {{-- Busca --}}
        <div class="sticky top-0 z-10 bg-background pb-4">
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-3.5 text-slate-400 w-5 h-5"></i>
                <input type="text" id="products-search" 
                       placeholder="Filtrar produtos..."
                       class="w-full bg-white border border-slate-200 rounded-xl pl-10 p-3 shadow-sm outline-none focus:border-primary">
            </div>
        </div>

        {{-- Lista de Produtos --}}
        <div id="products-list" class="space-y-3 pb-20">
            {{-- Produtos serão renderizados via JS --}}
        </div>
    </main>
</section>
