{{--
    📦 ZePocket - Catálogo de Produtos
    Hierarquia: PRIMARY (da home)
--}}
<section class="page" id="page-catalog" data-level="primary">
    {{-- Header --}}
    <header class="app-header">
        <button class="icon-btn" data-back>
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </button>
        <h1 class="header-title"></h1>
        <div class="header-actions">
            <button class="icon-btn relative" data-go="cart">
                <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                <span id="catalog-cart-badge" class="badge badge-error absolute -top-1 -right-1 min-w-4.5 h-4.5 text-[10px] hidden">0</span>
            </button>
            <div class="bg-slate-100 rounded-full px-3 py-1 text-xs font-bold text-slate-600">
                <span id="headerTotalItems">0</span> itens
            </div>
        </div>
    </header>

    <main id="app" class="page-content pt-16">
     
   

        <div class="grid grid-cols-3 gap-2 mb-4 sticky top-16 z-40 bg-[#F8FAFC] py-2">
            <button onclick="switchTab('catalogo')" id="btn-catalogo" class="active-tab py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex flex-col items-center gap-1">
                <i class="ph ph-list-magnifying-glass text-lg"></i> CATÁLOGO
            </button>
            <button onclick="switchTab('carrinho')" id="btn-carrinho" class="inactive-tab py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex flex-col items-center gap-1 relative">
                <i class="ph ph-shopping-cart text-lg"></i> MEU PEDIDO
                <div id="badgeCart" class="hidden absolute top-1 right-2 w-2 h-2 bg-red-500 rounded-full"></div>
            </button>
            <button onclick="switchTab('exportar')" id="btn-exportar" class="inactive-tab py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex flex-col items-center gap-1">
                <i class="ph ph-file-pdf text-lg"></i> EXPORTAR
            </button>
        </div>

        <div id="view-catalogo" class="space-y-4 mt-16 fade-in">
            <div class="relative">
                <i class="ph ph-magnifying-glass absolute left-3 top-3.5 text-slate-400"></i>
                <input id="searchInput" type="text" onkeyup="renderCatalog(this.value)" placeholder="Buscar para adicionar..." class="w-full bg-white border border-slate-200 rounded-xl pl-10 p-3 shadow-sm outline-none focus:border-emerald-500">
            </div>

            <div id="catalogList" class="space-y-3 pb-20">
            </div>
        </div>

        <div id="view-carrinho" class="hidden space-y-4 mt-16 fade-in pb-24">
            <div id="cartList" class="space-y-3">
            </div>
            
            <div id="cartFooter" class="hidden fixed bottom-4 left-4 right-4 max-w-lg mx-auto bg-slate-900 text-white p-4 rounded-2xl shadow-2xl flex justify-between items-center z-50">
                <div>
                    <p class="text-xs text-slate-400">Total Estimado</p>
                    <p class="text-xl font-bold" id="cartTotalValue">R$ 0,00</p>
                </div>
                <button onclick="switchTab('exportar')" class="bg-emerald-500 hover:bg-emerald-400 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition flex items-center gap-2">
                    Finalizar <i class="ph ph-arrow-right"></i>
                </button>
            </div>
        </div>

        <div id="view-exportar" class="hidden space-y-6 mt-16 fade-in pb-20">
            
            <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl flex gap-3 items-start">
                <i class="ph ph-info text-blue-600 text-xl"></i>
                <div>
                    <h3 class="font-bold text-blue-800 text-sm">Pronto para gerar!</h3>
                    <p class="text-xs text-blue-600 mt-1">Seus produtos foram agrupados automaticamente por fornecedor. Baixe os PDFs individuais abaixo.</p>
                </div>
            </div>

            <div id="exportList" class="space-y-4">
            </div>
        </div>

    <div id="toast" class="fixed bottom-24 left-1/2 transform -translate-x-1/2 bg-slate-900 text-white px-4 py-2 rounded-full text-xs font-bold shadow-xl opacity-0 transition-opacity pointer-events-none z-50">
        Item atualizado
    </div>

    </main>
</section>
