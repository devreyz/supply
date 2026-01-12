@extends('layouts.app')

@section('title', 'Cotação Inteligente - ZePocket Gôndola')

@push('styles')
<style>
    body { 
        font-family: 'Inter', sans-serif; 
        background-color: #f0f2f5; 
    }
    
    .dvh-screen {
        height: 100dvh;
    }
    
    .bento-card {
        background: white;
        border-radius: 1.25rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        display: flex;
        flex-direction: column;
    }
    
    .custom-scroll::-webkit-scrollbar { width: 6px; }
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { 
        background-color: #cbd5e1; 
        border-radius: 20px; 
    }
    
    .animate-fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @media (max-width: 768px) {
        .mobile-tabs {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e5e7eb;
            z-index: 100;
        }
        
        .mobile-tab-content {
            padding-bottom: 70px;
        }
    }
</style>
@endpush

@section('content')
<div class="dvh-screen overflow-hidden text-gray-800 flex flex-col md:flex-row p-2 md:p-4 gap-4">

    <!-- Abas Mobile -->
    <div class="md:hidden mobile-tabs flex justify-around items-center h-16">
        <button id="tab1-mobile" class="flex flex-col items-center justify-center p-2 text-blue-600">
            <i class="ph ph-plus-circle text-xl"></i>
            <span class="text-xs font-medium mt-1">Novo</span>
        </button>
        <button id="tab2-mobile" class="flex flex-col items-center justify-center p-2 text-gray-500">
            <i class="ph ph-list-bullets text-xl"></i>
            <span class="text-xs font-medium mt-1">Produtos</span>
        </button>
        <button id="tab3-mobile" class="flex flex-col items-center justify-center p-2 text-gray-500">
            <i class="ph ph-table text-xl"></i>
            <span class="text-xs font-medium mt-1">Seleção</span>
        </button>
    </div>

    <!-- Conteúdo Principal -->
    <div class="flex-1 flex flex-col md:flex-row gap-4 overflow-hidden mobile-tab-content">
        
        <!-- Painel Esquerdo (Lançamento) -->
        <aside id="panel-lancamento" class="flex flex-col gap-4 w-full md:w-[400px] shrink-0 h-full overflow-y-auto custom-scroll pb-2 md:pb-0">
            
            <div class="bento-card p-5 bg-gray-900 text-white border-none shrink-0">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold">Lançamento Rápido</h1>
                        <p class="text-xs text-gray-400">Adicione cotações rapidamente</p>
                    </div>
                    <a href="{{ route('filament.admin.pages.dashboard') }}" class="bg-gray-700 p-2 rounded-lg hover:bg-gray-600 transition">
                        <i class="ph ph-gear text-xl"></i>
                    </a>
                </div>
            </div>

            <div class="bento-card p-5 flex-1 relative overflow-visible">
                <form id="addForm" action="{{ route('quotes.store-quick') }}" method="POST" class="flex flex-col h-full gap-4">
                    @csrf
                    
                    <div class="relative group z-50">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 block">Produto</label>
                        <div class="relative">
                            <input type="text" id="prodInput" name="product_search" autocomplete="off" 
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-medium"
                                placeholder="Buscar ou cadastrar...">
                            <i class="ph ph-magnifying-glass absolute left-3 top-3.5 text-gray-400 text-lg"></i>
                            
                            <div id="suggestionsBox" class="hidden absolute top-full left-0 w-full bg-white border border-gray-200 rounded-xl shadow-xl mt-1 max-h-64 overflow-y-auto custom-scroll z-50">
                            </div>
                        </div>
                        <input type="hidden" name="product_id" id="product_id">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 block">EAN / Código</label>
                            <input type="text" id="eanInput" name="ean" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none" placeholder="Opcional">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 block">Unidade</label>
                            <select id="unitInput" name="unit" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none">
                                <option value="UN">Unidade</option>
                                <option value="CX">Caixa</option>
                                <option value="KG">Kg</option>
                                <option value="FD">Fardo</option>
                                <option value="LT">Litro</option>
                            </select>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-xs font-bold text-blue-800 uppercase tracking-wider">Dados da Cotação</label>
                            <span class="text-[10px] bg-blue-200 text-blue-800 px-2 py-0.5 rounded-full font-bold">HOJE</span>
                        </div>
                        
                        <div class="space-y-3">
                            <select id="supplierInput" name="supplier_id" required class="w-full bg-white border border-blue-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="" disabled selected>Selecione o Fornecedor...</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-500 text-sm font-bold">R$</span>
                                <input type="number" step="0.01" id="priceInput" name="price" required
                                    class="w-full bg-white border border-blue-200 rounded-xl px-3 py-2 pl-9 text-lg font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-400" 
                                    placeholder="0,00">
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white rounded-xl py-3.5 font-bold shadow-lg shadow-black/10 transition flex items-center justify-center gap-2">
                            <i class="ph ph-plus-circle text-xl"></i>
                            Registrar Cotação
                        </button>
                    </div>
                </form>
            </div>
        </aside>

        <!-- Painel Central -->
        <main class="flex-1 flex flex-col gap-4 h-full overflow-hidden">
            
            <!-- Tabs Desktop -->
            <div class="hidden md:flex bento-card p-2 md:p-3 flex-row items-center gap-3 shrink-0">
                <button id="tab1-desktop" class="bg-black text-white px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap">
                    <i class="ph ph-plus-circle mr-1"></i> Novo
                </button>
                <button id="tab2-desktop" class="bg-gray-100 text-gray-600 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap">
                    <i class="ph ph-list-bullets mr-1"></i> Produtos
                </button>
                <button id="tab3-desktop" class="bg-gray-100 text-gray-600 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap">
                    <i class="ph ph-table mr-1"></i> Comparativo
                </button>
                <div class="h-6 w-px bg-gray-300 mx-1"></div>
                <span class="text-xs text-gray-500 whitespace-nowrap px-2">Total: <b id="totalCount">{{ $totalProducts }}</b> produtos cotados</span>
            </div>

            <!-- Conteúdo das Abas -->
            <div class="flex-1 overflow-hidden">
                <div id="tab1-content" class="h-full overflow-y-auto custom-scroll pr-1">
                    <div id="productsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 pb-4">
                        @forelse($products as $product)
                            <div class="bento-card p-4 group hover:border-blue-400 transition h-full animate-fade-in">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center gap-3">
                                        @if($product->photo_path)
                                            <img src="{{ Storage::url($product->photo_path) }}" class="w-10 h-10 rounded-lg object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-lg">
                                                {{ substr($product->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-bold text-gray-900 leading-tight truncate" title="{{ $product->name }}">{{ $product->name }}</h3>
                                            <p class="text-xs text-gray-500">{{ $product->unit }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 rounded-lg p-2 space-y-1.5 mb-3 flex-1">
                                    <div class="text-center text-xs text-gray-500">Nenhuma cotação ainda</div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12 text-gray-400">
                                <i class="ph ph-package text-4xl mb-3"></i>
                                <p class="font-medium">Nenhum produto cadastrado</p>
                                <p class="text-sm mt-1">Acesse o painel administrativo para cadastrar produtos</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Estado da aplicação
const appState = {
    currentTab: 'tab1',
    products: @json($products)
};

// Busca de produtos
document.getElementById('prodInput').addEventListener('input', function(e) {
    const query = e.target.value;
    const suggestionsBox = document.getElementById('suggestionsBox');
    
    if (query.length < 2) {
        suggestionsBox.classList.add('hidden');
        return;
    }
    
    fetch(`{{ route('products.search') }}?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(products => {
            if (products.length === 0) {
                suggestionsBox.innerHTML = '<div class="p-3 text-sm text-gray-500">Nenhum produto encontrado.</div>';
            } else {
                suggestionsBox.innerHTML = products.map(p => `
                    <div class="p-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0 flex justify-between items-center group" 
                         onclick='selectProduct(${JSON.stringify(p)})'>
                        <div>
                            <div class="font-bold text-sm text-gray-800 group-hover:text-blue-700">${p.name}</div>
                            <div class="text-[10px] text-gray-400">${p.ean || 'Sem EAN'} • ${p.unit}</div>
                        </div>
                        <i class="ph ph-caret-right text-gray-300"></i>
                    </div>
                `).join('');
            }
            suggestionsBox.classList.remove('hidden');
        });
});

function selectProduct(product) {
    document.getElementById('prodInput').value = product.name;
    document.getElementById('product_id').value = product.id;
    document.getElementById('eanInput').value = product.ean || '';
    document.getElementById('unitInput').value = product.unit;
    document.getElementById('suggestionsBox').classList.add('hidden');
    document.getElementById('priceInput').focus();
}

// Gerenciamento de tabs
function switchTab(tabId) {
    appState.currentTab = tabId;
    
    document.querySelectorAll('[id$="-content"]').forEach(el => el.classList.add('hidden'));
    document.getElementById(`${tabId}-content`).classList.remove('hidden');
    
    document.querySelectorAll('[id^="tab"][id$="-desktop"]').forEach(btn => {
        if (btn.id === `${tabId}-desktop`) {
            btn.classList.remove('bg-gray-100', 'text-gray-600');
            btn.classList.add('bg-black', 'text-white');
        } else {
            btn.classList.remove('bg-black', 'text-white');
            btn.classList.add('bg-gray-100', 'text-gray-600');
        }
    });
    
    document.querySelectorAll('[id^="tab"][id$="-mobile"]').forEach(btn => {
        if (btn.id === `${tabId}-mobile`) {
            btn.classList.remove('text-gray-500');
            btn.classList.add('text-blue-600');
        } else {
            btn.classList.remove('text-blue-600');
            btn.classList.add('text-gray-500');
        }
    });
}

document.getElementById('tab1-desktop')?.addEventListener('click', () => switchTab('tab1'));
document.getElementById('tab2-desktop')?.addEventListener('click', () => switchTab('tab2'));
document.getElementById('tab3-desktop')?.addEventListener('click', () => switchTab('tab3'));

document.getElementById('tab1-mobile')?.addEventListener('click', () => switchTab('tab1'));
document.getElementById('tab2-mobile')?.addEventListener('click', () => switchTab('tab2'));
document.getElementById('tab3-mobile')?.addEventListener('click', () => switchTab('tab3'));
</script>
@endpush
