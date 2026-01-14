{{--
    📜 ZePocket - Histórico de Pedidos
    Hierarquia: PRIMARY (da home)
--}}
<section class="page" id="page-orders" data-level="primary">
    {{-- Header --}}
    <header class="app-header">
        <button class="icon-btn" data-back>
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </button>
        <h1 class="header-title">Histórico</h1>
        <button class="icon-btn" data-action="force-sync">
            <i data-lucide="refresh-cw" class="w-6 h-6"></i>
        </button>
    </header>

    <main class="page-content no-bottom-nav px-4 py-4">
        {{-- Filtros --}}
        <div class="flex gap-2 mb-4 overflow-x-auto pb-2">
            <button data-action="filter-orders" data-status="all" 
                    class="order-filter-btn active px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap">
                Todos
            </button>
            <button data-action="filter-orders" data-status="draft" 
                    class="order-filter-btn px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap">
                Rascunho
            </button>
            <button data-action="filter-orders" data-status="sent" 
                    class="order-filter-btn px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap">
                Enviados
            </button>
            <button data-action="filter-orders" data-status="completed" 
                    class="order-filter-btn px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap">
                Concluídos
            </button>
        </div>

        {{-- Lista de Pedidos --}}
        <div id="orders-list" class="space-y-3 pb-20">
            {{-- Pedidos serão renderizados via JS --}}
        </div>
    </main>
</section>
