{{--
    🏠 ZePocket - Home/Dashboard
    Hierarquia: ROOT (primeira página)
--}}
<section class="page active" id="page-home" data-level="home">
    {{-- Header --}}
    <header class="app-header flex justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-primary to-primary/70 rounded-xl flex items-center justify-center shadow-lg shadow-primary/30">
                <i data-lucide="package" class="w-5 h-5 text-white"></i>
            </div>
            <div>
                <h1 class="text-lg font-black tracking-tight">ZePocket</h1>
                <p class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Gestor de Compras</p>
            </div>
        </div>
        <div class="header-actions">
            <div id="sync-indicator" class="flex items-center gap-1 text-xs text-slate-400 mr-2">
                <span id="sync-status-icon" class="w-2 h-2 rounded-full bg-slate-300"></span>
                <span id="sync-status-text">Offline</span>
            </div>
            <button class="icon-btn" data-go="zepocket-settings">
                <i data-lucide="settings" class="w-6 h-6"></i>
            </button>
        </div>
    </header>

    <main class="page-content pb-4">
        {{-- Quick Actions --}}
        <div class="px-4 pt-4">
            <div class="grid grid-cols-2 gap-3 mb-6">
                <button data-go="catalog" 
                        class="bento-card p-4 text-left hover:bg-slate-50 active:scale-95 transition-all">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-2">
                        <i data-lucide="search" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <h3 class="font-bold text-slate-800">Catálogo</h3>
                    <p class="text-xs text-slate-500 mt-1">Montar pedido</p>
                </button>

                <button data-go="quote" 
                        class="bento-card p-4 text-left hover:bg-slate-50 active:scale-95 transition-all">
                    <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mb-2">
                        <i data-lucide="receipt" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                    <h3 class="font-bold text-slate-800">Cotar</h3>
                    <p class="text-xs text-slate-500 mt-1">Registrar preço</p>
                </button>
            </div>
        </div>

        {{-- Card de Resumo --}}
        <div class="px-4 mb-6">
            <div class="bento-card p-5 bg-gradient-to-br from-slate-800 to-slate-900 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold opacity-80">Resumo do Mês</h3>
                    <span class="text-xs bg-white/10 px-2 py-1 rounded-lg font-bold" id="summary-month"></span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider opacity-60 mb-1">Total Compras</p>
                        <p id="summary-total" class="text-xl font-black">R$ 0,00</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider opacity-60 mb-1">Lucro Esperado</p>
                        <p id="summary-profit" class="text-xl font-black text-emerald-400">R$ 0,00</p>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-white/10">
                    <span class="text-xs opacity-60"><span id="summary-orders-count">0</span> pedidos realizados</span>
                    <button data-go="orders" class="text-xs font-bold bg-white/10 hover:bg-white/20 px-3 py-1 rounded-lg transition">
                        Ver Histórico →
                    </button>
                </div>
            </div>
        </div>

        {{-- Menu Grid --}}
        <div class="px-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Gerenciar</h3>
            <div class="grid grid-cols-3 gap-2">
                <button data-go="products" 
                        class="bento-card p-4 flex flex-col items-center text-center hover:bg-slate-50 active:scale-95 transition-all">
                    <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center mb-2">
                        <i data-lucide="package" class="w-5 h-5 text-violet-600"></i>
                    </div>
                    <span class="font-bold text-xs text-slate-800">Produtos</span>
                    <span id="home-count-products" class="text-[10px] text-slate-400">0</span>
                </button>

                <button data-go="suppliers" 
                        class="bento-card p-4 flex flex-col items-center text-center hover:bg-slate-50 active:scale-95 transition-all">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mb-2">
                        <i data-lucide="building-2" class="w-5 h-5 text-orange-600"></i>
                    </div>
                    <span class="font-bold text-xs text-slate-800">Fornecedores</span>
                    <span id="home-count-suppliers" class="text-[10px] text-slate-400">0</span>
                </button>

                <button data-go="orders" 
                        class="bento-card p-4 flex flex-col items-center text-center hover:bg-slate-50 active:scale-95 transition-all">
                    <div class="w-10 h-10 bg-cyan-100 rounded-xl flex items-center justify-center mb-2">
                        <i data-lucide="history" class="w-5 h-5 text-cyan-600"></i>
                    </div>
                    <span class="font-bold text-xs text-slate-800">Histórico</span>
                    <span id="home-count-orders" class="text-[10px] text-slate-400">0</span>
                </button>
            </div>
        </div>

        {{-- Últimas Cotações --}}
        <div class="px-4 mt-6">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Últimas Cotações</h3>
                <button data-action="show-all-quotes" class="text-xs text-primary font-bold">Ver Todas</button>
            </div>
            <div id="home-recent-quotes" class="space-y-2">
                {{-- Cotações recentes serão renderizadas via JS --}}
                <div class="text-center py-8 text-slate-400 text-sm">
                    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                    <p>Nenhuma cotação recente</p>
                </div>
            </div>
        </div>
    </main>

    {{-- Bottom Navigation --}}
    <nav class="bottom-nav">
        <button class="nav-item active" data-go="zepocket">
            <i data-lucide="home" class="w-6 h-6"></i>
            <span class="text-[10px] font-bold mt-1">Início</span>
        </button>
        <button class="nav-item" data-go="catalog">
            <i data-lucide="shopping-cart" class="w-6 h-6"></i>
            <span class="text-[10px] font-bold mt-1">Comprar</span>
        </button>
        <button class="nav-item nav-item-center" data-go="quote">
            <div class="w-14 h-14 bg-primary rounded-full flex items-center justify-center shadow-lg shadow-primary/30 -mt-5">
                <i data-lucide="plus" class="w-7 h-7 text-white"></i>
            </div>
            <span class="text-[10px] font-bold mt-1">Cotar</span>
        </button>
        <button class="nav-item" data-go="products">
            <i data-lucide="package" class="w-6 h-6"></i>
            <span class="text-[10px] font-bold mt-1">Produtos</span>
        </button>
        <button class="nav-item" data-go="suppliers">
            <i data-lucide="building-2" class="w-6 h-6"></i>
            <span class="text-[10px] font-bold mt-1">Fornec.</span>
        </button>
    </nav>
</section>
