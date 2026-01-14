<x-spa-layout>
    {{-- =================== PÁGINAS ZEPOCKET =================== --}}
    
    {{-- Home / Dashboard --}}
    <x-screens.home />
    
    {{-- Catálogo (com abas: catálogo, carrinho, exportar) --}}
    <x-screens.catalog />
    
    {{-- Cotação --}}
    <x-screens.quote />
    
    {{-- Produtos --}}
    <x-screens.products />
    
    {{-- Fornecedores --}}
    <x-screens.suppliers />
    
    {{-- Histórico de Pedidos --}}
    <x-screens.orders />
    
    {{-- Configurações --}}
    <x-screens.settings />


    {{-- =================== SHEETS =================== --}}
    
    {{-- Adicionar/Editar Produto --}}
    <x-sheet.add-product />
    
    {{-- Adicionar/Editar Fornecedor --}}
    <x-sheet.add-supplier />
    
    {{-- Detalhes do Pedido --}}
    <x-sheet.order-detail />
    
    {{-- Comparativo de Preços --}}
    <x-sheet.price-compare />


    {{-- =================== BACKDROP =================== --}}
    <div id="backdrop"></div>

    {{-- =================== TOAST CONTAINER =================== --}}
    <div id="toast-container" class="toast-container toast-bottom-right"></div>

    {{-- =================== LOADING OVERLAY =================== --}}
    <div id="loading-overlay">
        <div class="loading-spinner"></div>
        <p class="loading-text">Carregando...</p>
    </div>
</x-spa-layout>
