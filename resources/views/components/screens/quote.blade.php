{{--
    💰 ZePocket - Cotação / Registro de Preços
    Hierarquia: PRIMARY (da home)
--}}
<section class="page" id="page-quote" data-level="primary">
    {{-- Header --}}
    <header class="app-header">
        <button class="icon-btn" data-back>
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </button>
        <h1 class="header-title">Cotar Produto</h1>
        <button class="icon-btn" data-action="reset-quote">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
    </header>

    <main class="page-content no-bottom-nav px-4 py-4">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; padding-bottom: 80px; }
        .bento-card {
            background: white;
            border-radius: 1rem;
            border: 1px solid #E2E8F0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .active-tab { background-color: #0F172A; color: white; }
        .inactive-tab { background-color: white; color: #64748B; border: 1px solid #E2E8F0; }
        .fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="text-slate-800">

    <div class="fixed top-0 left-0 right-0 bg-white/90 backdrop-blur-md border-b border-slate-200 z-50 px-4 py-3">
        <div class="flex justify-between items-center max-w-lg mx-auto">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold shadow-lg shadow-blue-500/30">Z.</div>
                <h1 class="font-bold text-lg tracking-tight"> <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100 uppercase">Gestão v2.5</span></h1>
            </div>
            <button onclick="resetData()" class="p-2 text-slate-400 hover:text-red-500 transition"><i class="ph ph-trash"></i></button>
        </div>
    </div>

    <main class="max-w-lg mx-auto pt-20 px-4 space-y-4" id="app">
        
        <div class="grid grid-cols-3 gap-2 mb-4">
            <button onclick="switchTab('cotar')" id="btn-cotar" class="active-tab py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm">
                COTAR
            </button>
            <button onclick="switchTab('produtos')" id="btn-produtos" class="inactive-tab py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm">
                PRODUTOS
            </button>
            <button onclick="switchTab('fornecedores')" id="btn-fornecedores" class="inactive-tab py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm">
                FORNECEDORES
            </button>
        </div>

        <div id="view-cotar" class="space-y-4 fade-in">
            
            <div class="bento-card p-5 relative overflow-visible">
                <div class="mb-4 relative">
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">Buscar Produto</label>
                    <div class="relative group">
                        <i class="ph ph-magnifying-glass absolute left-3 top-3.5 text-slate-400 text-lg"></i>
                        <input type="text" id="searchInput" 
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-10 pr-10 text-sm font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                            placeholder="Nome, Marca ou EAN..." autocomplete="off" oninput="handleSearch(this.value)">
                        <button onclick="clearSelection()" id="clearBtn" class="hidden absolute right-3 top-3 text-slate-400 hover:text-slate-600">
                            <i class="ph ph-x-circle-fill text-xl"></i>
                        </button>
                    </div>
                    <div id="suggestionsList" class="hidden absolute top-full left-0 right-0 bg-white border border-slate-100 rounded-xl shadow-2xl mt-2 max-h-60 overflow-y-auto z-50"></div>
                </div>

                <div id="quoteFormArea" class="hidden space-y-4 pt-2 border-t border-dashed border-slate-200">
                    
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 id="selectedName" class="font-bold text-slate-800 text-lg leading-tight">Nome do Produto</h3>
                            <div class="flex gap-2 mt-1">
                                <span id="selectedBrand" class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Marca</span>
                                <span id="selectedUnit" class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Unidade</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span id="selectedEan" class="text-[10px] font-mono text-slate-400 block bg-slate-50 px-1 rounded">EAN</span>
                        </div>
                    </div>

                    <div id="newProductInputs" class="hidden space-y-3 bg-blue-50/50 p-3 rounded-xl border border-blue-100">
                        <p class="text-xs font-bold text-blue-600 mb-2 flex items-center gap-1"><i class="ph ph-plus-circle"></i> Cadastrando Novo Item</p>
                        <input type="text" id="newProdName" class="w-full bg-white border-blue-200 rounded-lg text-sm px-3 py-2 outline-none" placeholder="Nome do Produto">
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" id="newProdBrand" class="w-full bg-white border-blue-200 rounded-lg text-sm px-3 py-2 outline-none" placeholder="Marca">
                            <input type="text" id="newProdUnit" class="w-full bg-white border-blue-200 rounded-lg text-sm px-3 py-2 outline-none" placeholder="UN, KG, CX">
                        </div>
                        <input type="number" id="newProdEan" class="w-full bg-white border-blue-200 rounded-lg text-sm px-3 py-2 outline-none font-mono" placeholder="Código de Barras (Opcional)">
                    </div>

                    <div>
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">Fornecedor</label>
                        <select id="quoteSupplier" onchange="checkPriceHistory()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-3 text-sm font-medium outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Selecione...</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="flex justify-between mb-1">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Custo (R$)</label>
                                <span id="priceHint" class="text-[10px] text-emerald-600 font-bold hidden animate-pulse">Histórico!</span>
                            </div>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-slate-400 font-bold">R$</span>
                                <input type="number" id="quotePrice" step="0.01" oninput="calculateMargin()" class="w-full bg-white border border-slate-200 rounded-xl py-3 pl-9 text-lg font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500" placeholder="0,00">
                            </div>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">Venda (R$)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-slate-400 font-bold">R$</span>
                                <input type="number" id="quoteSalePrice" step="0.01" oninput="calculateMargin()" class="w-full bg-white border border-slate-200 rounded-xl py-3 pl-9 text-lg font-bold text-blue-600 outline-none focus:ring-2 focus:ring-blue-500" placeholder="0,00">
                            </div>
                        </div>
                    </div>

                    <div id="marginCard" class="hidden bg-slate-100 rounded-xl p-3 flex justify-between items-center transition-colors duration-300">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider opacity-60">Margem de Lucro</p>
                            <p id="marginPercent" class="text-xl font-black">0%</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold uppercase tracking-wider opacity-60">Lucro Unitário</p>
                            <p id="profitValue" class="text-lg font-bold">R$ 0,00</p>
                        </div>
                    </div>

                    <button onclick="saveQuote()" class="w-full bg-slate-900 hover:bg-black text-white font-bold py-3.5 rounded-xl shadow-lg shadow-slate-900/20 transition transform active:scale-95 flex items-center justify-center gap-2">
                        <i class="ph ph-check-circle text-lg"></i> Confirmar
                    </button>

                </div>
            </div>
        </div>

        <div id="view-produtos" class="hidden space-y-4 fade-in">
             <div class="sticky top-16 z-10 bg-inherit pb-2">
                <div class="relative">
                    <i class="ph ph-magnifying-glass absolute left-3 top-3.5 text-slate-400"></i>
                    <input type="text" onkeyup="renderProductsList(this.value)" placeholder="Filtrar sua lista..." class="w-full bg-white border border-slate-200 rounded-xl pl-10 p-3 shadow-sm outline-none focus:border-blue-500">
                </div>
            </div>
            <div id="productsGrid" class="space-y-3 pb-20"></div>
        </div>

        <div id="view-fornecedores" class="hidden space-y-4 fade-in">
            <div class="bento-card p-4 bg-slate-900 text-white">
                <h3 class="font-bold mb-2">Novo Distribuidor</h3>
                <div class="flex gap-2">
                    <input type="text" id="newSupplierName" class="flex-1 bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white placeholder-slate-500 outline-none focus:border-blue-500" placeholder="Nome da empresa">
                    <button onclick="addSupplier()" class="bg-blue-600 px-4 py-2 rounded-lg font-bold text-sm hover:bg-blue-500">Add</button>
                </div>
            </div>
            <div id="suppliersGrid" class="space-y-2"></div>
        </div>

    </main>

    <div id="toast" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-slate-900 text-white px-6 py-3 rounded-full text-sm font-bold shadow-2xl opacity-0 transition-opacity pointer-events-none z-50 flex items-center gap-2">
        <i class="ph ph-check-circle text-emerald-400 text-lg"></i> <span id="toastMsg">Salvo!</span>
    </div>

    <script>
        // --- 1. CONFIGURAÇÃO ---
        const DB_KEY = 'zepocket_db_v2_5';
        let currentProduct = null;

        const initialDB = {
            products: [
                { id: 'SYS-1', ean: '7891000100103', name: 'Café Pilão Tradicional', brand: 'Pilão', unit: '500g', salePrice: 24.90 },
                { id: 'SYS-2', ean: '7894900011517', name: 'Coca-Cola Original', brand: 'Coca-Cola', unit: '2L', salePrice: 12.00 }
            ],
            suppliers: [
                { id: 1, name: 'Atacadão Norte' },
                { id: 2, name: 'Martins Atacado' }
            ],
            quotes: [
                { productId: 'SYS-1', supplierId: 1, price: 18.90, date: new Date().toISOString() },
                { productId: 'SYS-1', supplierId: 2, price: 19.50, date: new Date().toISOString() }
            ]
        };

        let db = JSON.parse(localStorage.getItem(DB_KEY)) || initialDB;

        function saveDB() {
            localStorage.setItem(DB_KEY, JSON.stringify(db));
        }

        function resetData() {
            if(confirm("Reiniciar todo o banco de dados?")) {
                localStorage.removeItem(DB_KEY);
                location.reload();
            }
        }

        // --- 2. LÓGICA DE MARGEM ---
        function calculateMargin() {
            const cost = parseFloat(document.getElementById('quotePrice').value);
            const sale = parseFloat(document.getElementById('quoteSalePrice').value);
            const card = document.getElementById('marginCard');
            
            if (isNaN(cost) || isNaN(sale) || cost <= 0) {
                card.classList.add('hidden');
                return;
            }

            card.classList.remove('hidden');
            
            const profit = sale - cost;
            // Cálculo de Markup: (Lucro / Venda) * 100
            const margin = sale > 0 ? (profit / sale) * 100 : 0;

            document.getElementById('profitValue').innerText = `R$ ${profit.toFixed(2)}`;
            document.getElementById('marginPercent').innerText = `${margin.toFixed(1)}%`;

            // Cores Dinâmicas
            if (profit > 0) {
                card.className = "bg-emerald-100 text-emerald-800 rounded-xl p-3 flex justify-between items-center transition-colors duration-300";
            } else if (profit < 0) {
                card.className = "bg-red-100 text-red-800 rounded-xl p-3 flex justify-between items-center transition-colors duration-300";
            } else {
                card.className = "bg-slate-100 text-slate-800 rounded-xl p-3 flex justify-between items-center transition-colors duration-300";
            }
        }

        // --- 3. BUSCA & SELEÇÃO ---
        function handleSearch(query) {
            const list = document.getElementById('suggestionsList');
            const clearBtn = document.getElementById('clearBtn');
            query.length > 0 ? clearBtn.classList.remove('hidden') : clearBtn.classList.add('hidden');

            if (query.length < 2) { list.classList.add('hidden'); return; }

            const term = query.toLowerCase();
            const matches = db.products.filter(p => 
                p.name.toLowerCase().includes(term) || 
                p.brand.toLowerCase().includes(term) || 
                (p.ean && p.ean.includes(term))
            );

            let html = matches.map(p => `
                <div onclick="selectProduct('${p.id}')" class="p-3 border-b border-slate-100 hover:bg-blue-50 cursor-pointer flex justify-between items-center group transition">
                    <div>
                        <div class="font-bold text-slate-700 text-sm group-hover:text-blue-700">${p.name}</div>
                        <div class="text-[10px] text-slate-500">${p.brand} • ${p.unit}</div>
                    </div>
                    ${p.ean ? `<span class="text-[9px] bg-slate-100 text-slate-400 px-1 rounded font-mono">#${p.ean.slice(-4)}</span>` : ''}
                </div>
            `).join('');

            html += `
                <div onclick="startNewProduct('${query}')" class="p-3 bg-blue-50/50 hover:bg-blue-100 cursor-pointer flex items-center gap-2 text-blue-700 font-bold text-sm border-t border-blue-100">
                    <i class="ph ph-plus-circle text-lg"></i> Cadastrar "${query}"
                </div>
            `;

            list.innerHTML = html;
            list.classList.remove('hidden');
        }

        function selectProduct(id) {
            currentProduct = db.products.find(p => p.id === id);
            
            // UI Updates
            document.getElementById('suggestionsList').classList.add('hidden');
            document.getElementById('searchInput').value = currentProduct.name;
            document.getElementById('selectedName').innerText = currentProduct.name;
            document.getElementById('selectedBrand').innerText = currentProduct.brand;
            document.getElementById('selectedUnit').innerText = currentProduct.unit;
            document.getElementById('selectedEan').innerText = currentProduct.ean || 'Sem EAN';
            
            // Preenche o Preço de Venda Atual
            document.getElementById('quoteSalePrice').value = currentProduct.salePrice ? currentProduct.salePrice.toFixed(2) : '';

            document.getElementById('newProductInputs').classList.add('hidden');
            document.getElementById('quoteFormArea').classList.remove('hidden');
            document.getElementById('marginCard').classList.add('hidden');

            renderSuppliersSelect();
            document.getElementById('quoteSupplier').focus();
        }

        function startNewProduct(queryName) {
            currentProduct = null;
            document.getElementById('suggestionsList').classList.add('hidden');
            document.getElementById('searchInput').value = queryName;
            document.getElementById('quoteFormArea').classList.remove('hidden');
            document.getElementById('newProductInputs').classList.remove('hidden');
            document.getElementById('newProdName').value = queryName;
            document.getElementById('quoteSalePrice').value = ''; 
            document.getElementById('selectedName').innerText = "Novo Produto";
            document.getElementById('selectedBrand').classList.add('hidden');
            document.getElementById('selectedUnit').classList.add('hidden');
            document.getElementById('selectedEan').classList.add('hidden');
            document.getElementById('marginCard').classList.add('hidden');

            renderSuppliersSelect();
        }

        function clearSelection() {
            document.getElementById('searchInput').value = '';
            document.getElementById('suggestionsList').classList.add('hidden');
            document.getElementById('quoteFormArea').classList.add('hidden');
            document.getElementById('clearBtn').classList.add('hidden');
            document.getElementById('quotePrice').value = '';
            document.getElementById('quoteSalePrice').value = '';
            currentProduct = null;
        }

        function checkPriceHistory() {
            const supplierId = parseInt(document.getElementById('quoteSupplier').value);
            const priceInput = document.getElementById('quotePrice');
            const hint = document.getElementById('priceHint');

            hint.classList.add('hidden');
            priceInput.value = '';

            if (!currentProduct || !supplierId) return;

            const existingQuote = db.quotes.find(q => q.productId === currentProduct.id && q.supplierId === supplierId);

            if (existingQuote) {
                priceInput.value = existingQuote.price.toFixed(2);
                hint.classList.remove('hidden');
                calculateMargin(); // Recalcula a margem se já tiver preço
            }
        }

        // --- 4. SALVAR ---
        function saveQuote() {
            const supplierId = parseInt(document.getElementById('quoteSupplier').value);
            const price = parseFloat(document.getElementById('quotePrice').value);
            const salePrice = parseFloat(document.getElementById('quoteSalePrice').value);

            if (!supplierId || isNaN(price)) {
                alert("Fornecedor e Custo são obrigatórios.");
                return;
            }

            // A. Salvar/Atualizar Produto
            if (!currentProduct) {
                const name = document.getElementById('newProdName').value;
                const brand = document.getElementById('newProdBrand').value;
                const unit = document.getElementById('newProdUnit').value;
                const ean = document.getElementById('newProdEan').value;

                if(!name || !brand || !unit) { alert("Preencha Nome, Marca e Unidade."); return; }

                currentProduct = {
                    id: 'SYS-' + Date.now(),
                    name, brand, unit,
                    ean: ean || null,
                    salePrice: isNaN(salePrice) ? 0 : salePrice
                };
                db.products.push(currentProduct);
            } else {
                // Atualiza o preço de venda no produto existente
                currentProduct.salePrice = isNaN(salePrice) ? 0 : salePrice;
                // Atualiza no array do DB
                const idx = db.products.findIndex(p => p.id === currentProduct.id);
                if(idx !== -1) db.products[idx] = currentProduct;
            }

            // B. Salvar Cotação
            db.quotes = db.quotes.filter(q => !(q.productId === currentProduct.id && q.supplierId === supplierId));
            db.quotes.push({
                productId: currentProduct.id,
                supplierId: supplierId,
                price: price,
                date: new Date().toISOString()
            });

            saveDB();
            showToast("Salvo com sucesso!");
            clearSelection();
        }

        // --- 5. RENDERIZAÇÃO ---
        function switchTab(tab) {
            ['cotar', 'produtos', 'fornecedores'].forEach(t => {
                document.getElementById(`view-${t}`).classList.add('hidden');
                document.getElementById(`btn-${t}`).className = 'inactive-tab py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm';
            });
            document.getElementById(`view-${tab}`).classList.remove('hidden');
            document.getElementById(`btn-${tab}`).className = 'active-tab py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm';

            if(tab === 'produtos') renderProductsList();
            if(tab === 'fornecedores') renderSuppliersList();
        }

        function renderSuppliersSelect() {
            const sel = document.getElementById('quoteSupplier');
            sel.innerHTML = '<option value="">Selecione...</option>' + 
                db.suppliers.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
        }

        function renderProductsList(filter = '') {
            const container = document.getElementById('productsGrid');
            container.innerHTML = '';
            const term = filter.toLowerCase();
            const filtered = db.products.filter(p => p.name.toLowerCase().includes(term));

            if(filtered.length === 0) {
                container.innerHTML = '<div class="text-center text-slate-400 py-10">Nada encontrado.</div>';
                return;
            }

            filtered.forEach(p => {
                const pQuotes = db.quotes.filter(q => q.productId === p.id).sort((a,b) => a.price - b.price);
                
                let priceHtml = '';
                if (pQuotes.length > 0) {
                    const best = pQuotes[0];
                    const sup = db.suppliers.find(s => s.id === best.supplierId);
                    
                    // Cálculo de Lucro no Card
                    let profitHtml = '';
                    if(p.salePrice > 0) {
                        const profit = p.salePrice - best.price;
                        const color = profit >= 0 ? 'text-emerald-600' : 'text-red-500';
                        profitHtml = `<span class="text-[10px] font-bold ${color}">Lucro: R$ ${profit.toFixed(2)}</span>`;
                    }

                    priceHtml = `
                        <div class="mt-3 bg-emerald-50 border border-emerald-100 rounded-lg p-2.5 flex justify-between items-center">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-emerald-600 uppercase">Custo (Min)</span>
                                <span class="text-xs font-bold text-slate-700 truncate w-32">${sup ? sup.name : '?'}</span>
                            </div>
                            <div class="text-right">
                                <span class="block text-lg font-bold text-emerald-700">R$ ${best.price.toFixed(2)}</span>
                                ${profitHtml}
                            </div>
                        </div>
                    `;
                } else {
                    priceHtml = '<div class="mt-3 text-xs text-slate-400 italic">Sem cotações</div>';
                }

                // Linha do Preço de Venda
                const saleHtml = p.salePrice > 0 
                    ? `<div class="mb-1 text-xs font-bold text-blue-600">Venda: R$ ${p.salePrice.toFixed(2)}</div>` 
                    : `<div class="mb-1 text-xs text-slate-400">Venda não definida</div>`;

                container.innerHTML += `
                    <div class="bento-card p-4 hover:border-blue-300 transition-colors">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-bold text-slate-800 text-sm leading-tight">${p.name}</h3>
                                <div class="flex gap-2 mt-1">
                                    <span class="text-[10px] bg-slate-100 px-1.5 py-0.5 rounded text-slate-500">${p.brand}</span>
                                    <span class="text-[10px] bg-slate-100 px-1.5 py-0.5 rounded text-slate-500 font-bold">${p.unit}</span>
                                </div>
                            </div>
                            ${p.ean ? `<span class="text-[9px] font-mono text-slate-400 bg-slate-50 px-1 rounded">#${p.ean.slice(-4)}</span>` : ''}
                        </div>
                        ${saleHtml}
                        ${priceHtml}
                    </div>
                `;
            });
        }

        function addSupplier() {
            const input = document.getElementById('newSupplierName');
            if(input.value) {
                db.suppliers.push({ id: Date.now(), name: input.value });
                saveDB();
                renderSuppliersList();
                input.value = '';
                showToast("Fornecedor Adicionado");
            }
        }

        function renderSuppliersList() {
            const div = document.getElementById('suppliersGrid');
            div.innerHTML = db.suppliers.map(s => `
                <div class="bg-white border border-slate-200 p-3 rounded-lg flex justify-between items-center shadow-sm">
                    <span class="font-bold text-sm text-slate-700">${s.name}</span>
                    <button class="text-slate-400 hover:text-red-500"><i class="ph ph-trash"></i></button>
                </div>
            `).join('');
        }

        function showToast(msg) {
            const t = document.getElementById('toast');
            document.getElementById('toastMsg').innerText = msg;
            t.classList.remove('opacity-0');
            t.classList.add('translate-y-0');
            setTimeout(() => {
                t.classList.add('opacity-0');
                t.classList.remove('translate-y-0');
            }, 2000);
        }
    </script>
    </main>
</section>
