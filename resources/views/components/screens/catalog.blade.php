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

    <script>
        // --- 1. DADOS & ESTADO ---
        const DB_KEY = 'zepocket_db_v2';
        let db = JSON.parse(localStorage.getItem(DB_KEY));

        // Estado Local do Carrinho (Não persiste no localStorage principal para não sujar o banco, mas poderia)
        // Estrutura: { productId: "ID", supplierId: 1, qty: 1, price: 10.00 }
        let cart = []; 

        if (!db) {
            // Mock para teste visual se estiver vazio
            db = { products: [], suppliers: [], quotes: [] };
        }

        // --- 2. LÓGICA DE NEGÓCIO ---

        function getBestQuote(productId) {
            const quotes = db.quotes.filter(q => q.productId === productId);
            if (quotes.length === 0) return null;
            return quotes.sort((a, b) => a.price - b.price)[0];
        }

        function getQuote(productId, supplierId) {
            return db.quotes.find(q => q.productId === productId && q.supplierId == supplierId);
        }

        function toggleCart(productId) {
            const index = cart.findIndex(c => c.productId === productId);
            
            if (index > -1) {
                // Remover
                cart.splice(index, 1);
                showToast("Item removido");
            } else {
                // Adicionar (Padrão: Menor Preço)
                const bestQuote = getBestQuote(productId);
                
                if (!bestQuote) {
                    alert("Este produto não tem preço cadastrado em nenhum fornecedor!");
                    return;
                }

                cart.push({
                    productId: productId,
                    supplierId: bestQuote.supplierId,
                    qty: 1,
                    price: bestQuote.price // Salva snapshot do preço
                });
                showToast("Adicionado (Melhor Preço)");
            }
            updateUI();
        }

        function updateCartItem(productId, field, value) {
            const item = cart.find(c => c.productId === productId);
            if (!item) return;

            if (field === 'qty') {
                item.qty = parseInt(value) || 1;
            } 
            else if (field === 'supplierId') {
                // Troca de fornecedor: precisa atualizar o preço
                const newSupplierId = parseInt(value);
                const newQuote = getQuote(productId, newSupplierId);
                
                if (newQuote) {
                    item.supplierId = newSupplierId;
                    item.price = newQuote.price;
                }
            }
            updateUI();
        }

        // --- 3. RENDERIZAÇÃO ---

        function updateUI() {
            renderCatalog(); // Para atualizar status dos botões
            renderCart();
            renderExport();
            
            // Atualiza Header
            document.getElementById('headerTotalItems').innerText = cart.length;
            const badge = document.getElementById('badgeCart');
            cart.length > 0 ? badge.classList.remove('hidden') : badge.classList.add('hidden');
        }

        // TAB 1: Catálogo
        function renderCatalog(filter = '') {
            const list = document.getElementById('catalogList');
            // Só limpa se não for re-renderização interna (para manter foco no input se necessário, mas aqui simplificamos)
            if(document.activeElement.id !== 'searchInput') list.innerHTML = ''; 
            else if(filter === '') list.innerHTML = ''; // Reset forçado

            if(document.activeElement.id === 'searchInput') list.innerHTML = '';

            const term = filter.toLowerCase();
            const filtered = db.products.filter(p => p.name.toLowerCase().includes(term));

            if (filtered.length === 0) {
                list.innerHTML = '<div class="text-center text-slate-400 py-10">Nada encontrado.</div>';
                return;
            }

            let html = '';
            filtered.forEach(p => {
                const bestQuote = getBestQuote(p.id);
                const isInCart = cart.find(c => c.productId === p.id);
                const supplierName = bestQuote ? db.suppliers.find(s => s.id === bestQuote.supplierId)?.name : '--';
                const priceDisplay = bestQuote ? `R$ ${bestQuote.price.toFixed(2)}` : 'Sem cotação';

                html += `
                    <div class="bento-card p-3 flex items-center justify-between ${isInCart ? 'border-emerald-500 ring-1 ring-emerald-500 bg-emerald-50/30' : ''}">
                        <div class="flex-1">
                            <h3 class="font-bold text-slate-800 text-sm">${p.name}</h3>
                            <div class="flex gap-2 text-xs text-slate-500 mt-0.5">
                                <span class="bg-slate-100 px-1.5 rounded">${p.brand}</span>
                                <span class="bg-slate-100 px-1.5 rounded">${p.unit}</span>
                            </div>
                            <div class="mt-2 text-xs">
                                <span class="text-slate-400">Melhor oferta:</span> 
                                <span class="font-bold text-emerald-600">${priceDisplay}</span>
                                <span class="text-[10px] text-slate-400">(${supplierName})</span>
                            </div>
                        </div>
                        
                        <button onclick="toggleCart('${p.id}')" class="ml-3 w-10 h-10 rounded-xl flex items-center justify-center transition-all ${bestQuote ? '' : 'opacity-50 pointer-events-none'} ${isInCart ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400 hover:bg-emerald-500 hover:text-white'}">
                            <i class="ph ${isInCart ? 'ph-check font-bold text-xl' : 'ph-plus text-xl'}"></i>
                        </button>
                    </div>
                `;
            });
            list.innerHTML = html;
        }

        // TAB 2: Carrinho
        function renderCart() {
            const list = document.getElementById('cartList');
            const footer = document.getElementById('cartFooter');
            const totalEl = document.getElementById('cartTotalValue');
            
            list.innerHTML = '';
            
            if (cart.length === 0) {
                list.innerHTML = `
                    <div class="text-center py-20">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                            <i class="ph ph-shopping-cart text-3xl"></i>
                        </div>
                        <h3 class="text-slate-500 font-medium">Seu carrinho está vazio</h3>
                        <p class="text-xs text-slate-400 mt-1">Adicione itens na aba Catálogo</p>
                    </div>`;
                footer.classList.add('hidden');
                return;
            }

            footer.classList.remove('hidden');
            let grandTotal = 0;

            cart.forEach(item => {
                const product = db.products.find(p => p.id === item.productId);
                const subtotal = item.price * item.qty;
                grandTotal += subtotal;

                // Buscar todos os fornecedores que cotaram este produto para popular o select
                const availableQuotes = db.quotes.filter(q => q.productId === item.productId);

                const options = availableQuotes.map(q => {
                    const s = db.suppliers.find(sup => sup.id === q.supplierId);
                    return `<option value="${s.id}" ${s.id === item.supplierId ? 'selected' : ''}>
                        ${s.name} (R$ ${q.price.toFixed(2)})
                    </option>`;
                }).join('');

                list.innerHTML += `
                    <div class="bento-card p-4">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="font-bold text-slate-800 text-sm">${product.name}</h3>
                            <button onclick="toggleCart('${item.productId}')" class="text-red-400 hover:text-red-600 p-1">
                                <i class="ph ph-trash"></i>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100">
                            
                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase">Fornecedor</label>
                                <select onchange="updateCartItem('${item.productId}', 'supplierId', this.value)" class="w-full bg-white border border-slate-200 text-xs font-medium p-2 rounded-lg outline-none focus:border-emerald-500">
                                    ${options}
                                </select>
                            </div>

                            <div class="flex gap-3">
                                <div class="flex-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase">Qtd</label>
                                    <div class="flex items-center">
                                        <button onclick="updateCartItem('${item.productId}', 'qty', ${item.qty - 1})" class="w-8 h-8 bg-white border border-slate-200 rounded-l-lg hover:bg-slate-100">-</button>
                                        <input type="number" readonly value="${item.qty}" class="w-full h-8 text-center bg-white border-y border-slate-200 text-sm font-bold text-slate-700">
                                        <button onclick="updateCartItem('${item.productId}', 'qty', ${item.qty + 1})" class="w-8 h-8 bg-white border border-slate-200 rounded-r-lg hover:bg-slate-100">+</button>
                                    </div>
                                </div>
                                
                                <div class="flex-1 text-right">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase">Subtotal</label>
                                    <div class="h-8 flex items-center justify-end text-emerald-600 font-bold text-sm">
                                        R$ ${subtotal.toFixed(2)}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            totalEl.innerText = `R$ ${grandTotal.toFixed(2)}`;
        }

        // TAB 3: Exportar
        function renderExport() {
            const list = document.getElementById('exportList');
            list.innerHTML = '';

            if (cart.length === 0) {
                list.innerHTML = '<div class="text-center text-slate-400 text-sm">Nenhum pedido para exportar.</div>';
                return;
            }

            // Agrupar por Fornecedor
            const groups = {};
            cart.forEach(item => {
                if (!groups[item.supplierId]) groups[item.supplierId] = { items: [], total: 0 };
                groups[item.supplierId].items.push(item);
                groups[item.supplierId].total += (item.price * item.qty);
            });

            // Renderizar Cards de Exportação
            for (const [supplierId, data] of Object.entries(groups)) {
                const supplier = db.suppliers.find(s => s.id == supplierId);
                const itemCount = data.items.length;
                
                list.innerHTML += `
                    <div class="bento-card p-5">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="font-bold text-lg text-slate-800">${supplier.name}</h3>
                                <p class="text-xs text-slate-500">${itemCount} itens neste pedido</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-slate-400 font-bold uppercase">Valor Total</p>
                                <p class="text-xl font-bold text-emerald-600">R$ ${data.total.toFixed(2)}</p>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button onclick="downloadPDF(${supplierId})" class="flex-1 bg-slate-800 hover:bg-black text-white py-2.5 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition">
                                <i class="ph ph-file-pdf text-lg"></i> Baixar PDF
                            </button>
                            <button onclick="copyToClipboard(${supplierId})" class="px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold text-sm transition" title="Copiar Texto">
                                <i class="ph ph-copy text-lg"></i>
                            </button>
                        </div>
                    </div>
                `;
            }
        }

        // --- 4. EXPORTAÇÃO (PDF & Texto) ---

        async function downloadPDF(supplierId) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            const supplier = db.suppliers.find(s => s.id == supplierId);
            const items = cart.filter(c => c.supplierId == supplierId);
            
            // Cabeçalho
            doc.setFontSize(18);
            doc.text("Pedido de Compra", 14, 20);
            
            doc.setFontSize(12);
            doc.text(`Fornecedor: ${supplier.name}`, 14, 30);
            doc.text(`Data: ${new Date().toLocaleDateString()}`, 14, 36);

            // Tabela
            const tableData = items.map(item => {
                const p = db.products.find(x => x.id === item.productId);
                return [
                    p.name,
                    p.brand,
                    p.unit,
                    item.qty,
                    `R$ ${item.price.toFixed(2)}`,
                    `R$ ${(item.price * item.qty).toFixed(2)}`
                ];
            });

            let grandTotal = items.reduce((sum, i) => sum + (i.price * i.qty), 0);

            doc.autoTable({
                startY: 45,
                head: [['Produto', 'Marca', 'Un.', 'Qtd', 'Unit.', 'Total']],
                body: tableData,
                theme: 'grid',
                headStyles: { fillColor: [16, 185, 129] }, // Emerald color
                foot: [['', '', '', '', 'TOTAL:', `R$ ${grandTotal.toFixed(2)}`]]
            });

            // Salvar
            doc.save(`Pedido_${supplier.name.replace(/\s/g, '_')}.pdf`);
            showToast("PDF Gerado!");
        }

        function copyToClipboard(supplierId) {
            const supplier = db.suppliers.find(s => s.id == supplierId);
            const items = cart.filter(c => c.supplierId == supplierId);
            let text = `*PEDIDO DE COMPRA - ${supplier.name}*\n`;
            text += `Data: ${new Date().toLocaleDateString()}\n----------------\n`;
            
            let total = 0;
            items.forEach(item => {
                const p = db.products.find(x => x.id === item.productId);
                const sub = item.price * item.qty;
                total += sub;
                text += `[${item.qty}x] ${p.name} (${p.unit}) - R$ ${sub.toFixed(2)}\n`;
            });
            
            text += `----------------\n*TOTAL: R$ ${total.toFixed(2)}*`;

            navigator.clipboard.writeText(text).then(() => {
                showToast("Copiado para área de transferência!");
            });
        }

        // --- 5. UI UTILITÁRIOS ---

        function switchTab(tab) {
            ['catalogo', 'carrinho', 'exportar'].forEach(t => {
                document.getElementById(`view-${t}`).classList.add('hidden');
                document.getElementById(`btn-${t}`).className = 'inactive-tab py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex flex-col items-center gap-1 relative';
            });

            document.getElementById(`view-${tab}`).classList.remove('hidden');
            const activeBtn = document.getElementById(`btn-${tab}`);
            activeBtn.className = 'active-tab py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex flex-col items-center gap-1 relative';
            
            // Re-render
            if (tab === 'catalogo') renderCatalog();
            if (tab === 'carrinho') renderCart();
            if (tab === 'exportar') renderExport();
        }

        function showToast(msg) {
            const t = document.getElementById('toast');
            t.innerText = msg;
            t.classList.remove('opacity-0');
            t.classList.add('translate-y-0');
            setTimeout(() => {
                t.classList.add('opacity-0');
                t.classList.remove('translate-y-0');
            }, 2000);
        }

        // Init
        renderCatalog();

    </script>
    </main>
</section>
