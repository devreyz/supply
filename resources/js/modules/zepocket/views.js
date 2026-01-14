/**
 * ZePocket Views Controller
 * Conecta os componentes Blade com os módulos ZePocket
 */

import { ZePocketUI } from "./ui.js";

class ZePocketViews {
    constructor(app = null) {
        this.app = app || window.zepocket;
        this.ui = new ZePocketUI(this.app);
        this.currentQuoteProduct = null;
        this.currentOrderDetail = null;
        this.currentCompareProduct = null;
        this.init();
    }

    init() {
        document.addEventListener("DOMContentLoaded", () => {
            this.bindEvents();
            this.updateCartBadge();
        });
    }

    bindEvents() {
        // Delegação de eventos de clique
        document.addEventListener("click", (e) => {
            const el = e.target.closest("[data-action]");
            if (!el) return;

            const action = el.dataset.action;
            const id = el.dataset.id;
            const value = el.dataset.value;

            this.handleAction(action, id, value, el);
        });

        // Eventos de Input (Buscas e Cálculos)
        const inputs = {
            "catalog-search": (e) => this.searchCatalog(e.target.value),
            "products-search": (e) => this.filterProductsList(e.target.value),
            "quote-search": (e) => this.handleQuoteSearch(e.target.value),
            "quote-cost-price": () => this.calculateMargin(),
            "quote-sale-price": () => this.calculateMargin(),
            "new-supplier-name": (e) => {
                if (e.key === "Enter") this.quickAddSupplier();
            },
        };

        Object.entries(inputs).forEach(([id, handler]) => {
            const el = document.getElementById(id);
            if (el) {
                const eventType = el.tagName === "INPUT" ? "input" : "change";
                el.addEventListener(eventType, handler);
            }
        });

        // Backup Upload
        const backupInput = document.getElementById("import-backup-input");
        if (backupInput) {
            backupInput.addEventListener("change", (e) => {
                if (e.target.files?.[0]) {
                    window.zepocket.importBackup(e.target.files[0]);
                }
            });
        }
    }

    handleAction(action, id, value, el) {
        if (!this.app) {
            console.error("ZePocket app not initialized");
            return;
        }

        switch (action) {
            // Navegação e UI
            case "switch-tab":
                this.switchCatalogTab(value);
                break;
            case "open-sheet":
                this.app.spa?.openSheet(value);
                break;
            case "close-sheet":
                this.app.spa?.closeSheet();
                break;

            // Catálogo/Carrinho
            case "toggle-cart":
                this.addToCart(id);
                break;
            case "remove-from-cart":
                this.removeFromCart(id);
                break;
            case "update-cart-qty":
                this.updateCartQuantity(id, el.dataset.qty);
                break;
            case "update-cart-supplier":
                this.updateCartSupplier(id, el.value);
                break;
            case "clear-cart":
                this.clearCart();
                break;
            case "create-order":
                this.finalizeOrders();
                break;

            // Cotações
            case "reset-quote":
                this.resetQuoteForm();
                break;
            case "clear-quote":
                this.clearQuoteSelection();
                break;
            case "save-quote":
                this.saveQuote();
                break;
            case "select-quote-product":
                this.selectQuoteProduct(id);
                break;
            case "create-new-quote-product":
                this.createNewQuoteProduct(value);
                break;

            // Fornecedores
            case "quick-add-supplier":
                this.quickAddSupplier();
                break;
            case "edit-supplier":
                this.editSupplier(id);
                break;
            case "delete-supplier":
                this.deleteSupplier(id);
                break;
            case "save-supplier":
                this.saveSupplier();
                break;

            // Produtos
            case "open-add-product":
                this.openProductSheet();
                break;
            case "edit-product":
                this.editProduct(id);
                break;
            case "delete-product":
                this.deleteProduct(id);
                break;
            case "save-product":
                this.saveProduct();
                break;

            // Pedidos / Export
            case "view-order":
                this.showOrderDetail(id);
                break;
            case "download-order-pdf":
                this.app.downloadOrderPDF?.(id);
                break;
            case "send-order-whatsapp":
                this.app.sendOrderToWhatsapp?.(id);
                break;
            case "copy-order-text":
                this.app.copyOrderText?.(id);
                break;
            case "clone-order":
                this.cloneOrder(id);
                break;

            // Configurações
            case "sync-data":
                this.app.forceSync?.();
                break;
            case "export-backup":
                this.app.exportBackup?.();
                break;
            case "clear-data":
                this.confirmClearData();
                break;
        }
    }

    // ============================
    // CATALOG VIEW
    // ============================

    switchCatalogTab(tab) {
        const tabs = ["catalog", "cart", "export"];
        tabs.forEach((t) => {
            const btn = document.getElementById(`btn-${t}-tab`);
            const view = document.getElementById(`view-${t}-list`);
            if (btn) btn.classList.toggle("active", t === tab);
            if (view) view.classList.toggle("hidden", t !== tab);
        });

        // Carregar conteúdo da aba
        if (tab === "catalog") this.loadCatalogProducts();
        if (tab === "cart") this.loadCartItems();
        if (tab === "export") this.loadExportOrders();

        this.updateCartFooterVisibility(tab === "cart");
    }

    async loadCatalogProducts(search = "") {
        const container = document.getElementById("catalog-products-list");
        if (!container) return;

        const products = await window.zepocket.searchProducts(search);
        const cartItems = await window.zepocket.getCartItems();
        const cartProductIds = new Set(
            cartItems.map((item) => item.product_id)
        );

        if (!products || products.length === 0) {
            container.innerHTML = this.ui.renderEmptyState(
                "Nenhum produto encontrado",
                "package"
            );
            return;
        }

        container.innerHTML = products
            .map((p) => this.ui.renderProductCard(p, cartProductIds.has(p.id)))
            .join("");

        this.ui.refreshIcons();
    }

    searchCatalog(term) {
        clearTimeout(this._catalogSearchTimeout);
        this._catalogSearchTimeout = setTimeout(() => {
            this.loadCatalogProducts(term);
        }, 300);
    }

    async addToCart(productId, quantity = 1, supplierId = null) {
        await window.zepocket.addToCart(productId, quantity, supplierId);
        this.updateCartBadge();
        this.showToast("Produto adicionado ao pedido");
    }

    async loadCartItems() {
        const container = document.getElementById("cart-items-list");
        if (!container) return;

        const items = await window.zepocket.getCartItems();

        if (!items || items.length === 0) {
            container.innerHTML = this.ui.renderEmptyState(
                "Carrinho vazio",
                "shopping-cart"
            );
            this.updateCartFooter(null);
            return;
        }

        // Para cada item, precisamos das cotações disponíveis (opções de fornecedor)
        const itemsHtml = await Promise.all(
            items.map(async (item) => {
                const quotes = await window.zepocket.getProductQuotes(
                    item.product_id
                );
                return this.ui.renderCartItem(item, quotes);
            })
        );

        container.innerHTML = itemsHtml.join("");

        this.updateCartFooter(items);
        this.ui.refreshIcons();
    }

    async updateCartQuantity(itemId, newQty) {
        if (newQty <= 0) {
            await this.removeFromCart(itemId);
            return;
        }
        await window.zepocket.updateCartItem(itemId, newQty);
        this.loadCartItems();
    }

    async removeFromCart(itemId) {
        await window.zepocket.removeFromCart(itemId);
        this.loadCartItems();
        this.updateCartBadge();
    }

    async clearCart() {
        if (!confirm("Limpar todos os itens do carrinho?")) return;
        await window.zepocket.clearCart();
        this.loadCartItems();
        this.updateCartBadge();
    }

    updateCartFooter(items) {
        const footer = document.getElementById("cart-footer");
        const content = document.getElementById("cart-footer-content");

        if (!footer || !content) return;

        if (!items || items.length === 0) {
            footer.classList.add("hidden");
            return;
        }

        // Calcula totais para o footer
        const totals = items.reduce(
            (acc, item) => {
                acc.totalCost += item.subtotal || 0;
                acc.totalProfit += item.profit || 0;
                return acc;
            },
            { totalCost: 0, totalProfit: 0 }
        );

        content.innerHTML = this.ui.renderCartFooter(totals);

        footer.classList.remove("hidden");
        this.ui.refreshIcons();
    }

    updateCartFooterVisibility(show) {
        const footer = document.getElementById("cart-footer");
        if (footer && show) {
            // Mostra só se tiver itens
            this.loadCartItems();
        } else if (footer) {
            footer.classList.add("hidden");
        }
    }

    async updateCartBadge() {
        const items = await window.zepocket.getCartItems();
        const count = items ? items.reduce((sum, i) => sum + i.quantity, 0) : 0;

        ["catalog-cart-badge", "tab-cart-badge"].forEach((id) => {
            const el = document.getElementById(id);
            if (el) {
                if (count > 0) {
                    el.textContent = count;
                    el.classList.remove("hidden");
                } else {
                    el.classList.add("hidden");
                }
            }
        });
    }

    async finishOrder() {
        const items = await window.zepocket.getCartItems();
        if (!items || items.length === 0) {
            this.showToast("Carrinho vazio!", "error");
            return;
        }

        // Agrupa por fornecedor
        const grouped = {};
        items.forEach((item) => {
            const supplierId = item.supplier_id || "sem_fornecedor";
            if (!grouped[supplierId]) grouped[supplierId] = [];
            grouped[supplierId].push(item);
        });

        // Cria pedidos por fornecedor
        for (const [supplierId, supplierItems] of Object.entries(grouped)) {
            await window.zepocket.createOrderFromCart(
                supplierId === "sem_fornecedor" ? null : parseInt(supplierId),
                supplierItems.map((i) => i.id)
            );
        }

        // Limpa carrinho e vai para exportar
        await window.zepocket.clearCart();
        this.switchCatalogTab("export");
        this.updateCartBadge();
        this.showToast("Pedido(s) criado(s) com sucesso!", "success");
    }

    async loadExportOrders() {
        const container = document.getElementById("export-orders-list");
        if (!container) return;

        const orders = await window.zepocket.getOrders("draft");

        if (!orders || orders.length === 0) {
            container.innerHTML = this.ui.renderEmptyState(
                "Nenhum pedido para exportar",
                "file-text"
            );
            return;
        }

        container.innerHTML = orders
            .map((order) => this.ui.renderOrderExportCard(order))
            .join("");

        this.ui.refreshIcons();
    }

    async sendToWhatsapp(orderId) {
        const result = await window.zepocket.generateWhatsappText(orderId);
        if (result.url) {
            window.open(result.url, "_blank");
            // Marca como enviado
            await window.zepocket.markOrderSent(orderId);
            this.loadExportOrders();
        }
    }

    async generatePdf(orderId) {
        // Por enquanto, copia texto para clipboard
        const result = await window.zepocket.generateWhatsappText(orderId);
        if (result.text) {
            await navigator.clipboard.writeText(result.text);
            this.showToast("Texto copiado! Cole no WhatsApp", "success");
        }
    }

    // ============================
    // QUOTE VIEW
    // ============================

    async handleQuoteSearch(term) {
        const suggestions = document.getElementById("quote-suggestions");
        if (!suggestions) return;

        if (term.length < 2) {
            suggestions.classList.add("hidden");
            return;
        }

        const products = await window.zepocket.searchProducts(term);

        let html = products
            .map(
                (p) => `
            <div data-action="select-quote-product" data-id="${p.id}" 
                 class="p-3 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-0">
                <p class="font-bold text-sm text-slate-800">${p.name}</p>
                <p class="text-xs text-slate-500">${p.brand || ""} ${
                    p.unit || ""
                }</p>
            </div>
        `
            )
            .join("");

        // Opção de criar novo
        html += `
            <div data-action="create-new-quote-product" data-value="${term}" 
                 class="p-3 bg-blue-50 hover:bg-blue-100 cursor-pointer flex items-center gap-2 text-blue-600">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span class="font-bold text-sm">Cadastrar "${term}"</span>
            </div>
        `;

        suggestions.innerHTML = html;
        suggestions.classList.remove("hidden");
        this.ui.refreshIcons();
    }

    async selectQuoteProduct(productId) {
        const products = await window.zepocket.searchProducts("");
        const product = products.find((p) => p.id === productId);
        if (!product) return;

        this.currentQuoteProduct = product;

        document.getElementById("quote-suggestions").classList.add("hidden");
        document.getElementById("quote-search").value = product.name;
        document.getElementById("quote-clear-btn").classList.remove("hidden");
        document.getElementById("quote-form-area").classList.remove("hidden");
        document
            .getElementById("quote-new-product-inputs")
            .classList.add("hidden");

        document.getElementById("quote-product-name").textContent =
            product.name;
        document.getElementById("quote-product-brand").textContent =
            product.brand || "Sem marca";
        document.getElementById("quote-product-unit").textContent =
            product.unit || "UN";
        document.getElementById("quote-product-ean").textContent =
            product.ean || "-";

        await this.loadSupplierOptions();
    }

    createNewQuoteProduct(name) {
        this.currentQuoteProduct = { id: null, name };

        document.getElementById("quote-suggestions").classList.add("hidden");
        document.getElementById("quote-search").value = name;
        document.getElementById("quote-clear-btn").classList.remove("hidden");
        document.getElementById("quote-form-area").classList.remove("hidden");
        document
            .getElementById("quote-new-product-inputs")
            .classList.remove("hidden");

        document.getElementById("quote-new-name").value = name;
        document.getElementById("quote-product-name").textContent =
            "Novo Produto";
        document.getElementById("quote-product-brand").textContent = "-";
        document.getElementById("quote-product-unit").textContent = "-";
        document.getElementById("quote-product-ean").textContent = "-";

        this.loadSupplierOptions();
    }

    clearQuoteSelection() {
        this.currentQuoteProduct = null;
        document.getElementById("quote-search").value = "";
        document.getElementById("quote-clear-btn").classList.add("hidden");
        document.getElementById("quote-form-area").classList.add("hidden");
        document.getElementById("quote-suggestions").classList.add("hidden");
        this.resetQuoteForm();
    }

    resetQuoteForm() {
        document.getElementById("quote-search").value = "";
        document.getElementById("quote-supplier").value = "";
        document.getElementById("quote-cost-price").value = "";
        document.getElementById("quote-sale-price").value = "";
        document.getElementById("quote-margin-card").classList.add("hidden");
        this.clearQuoteSelection();
    }

    async loadSupplierOptions() {
        const select = document.getElementById("quote-supplier");
        if (!select) return;

        const suppliers = await window.zepocket.getSuppliers();

        select.innerHTML =
            '<option value="">Selecione...</option>' +
            suppliers
                .map((s) => `<option value="${s.id}">${s.name}</option>`)
                .join("");
    }

    async checkPriceHistory() {
        if (!this.currentQuoteProduct || !this.currentQuoteProduct.id) return;

        const supplierId = document.getElementById("quote-supplier").value;
        if (!supplierId) return;

        const quotes = await window.zepocket.getQuotes(
            this.currentQuoteProduct.id,
            supplierId
        );
        const hint = document.getElementById("quote-price-hint");

        if (quotes && quotes.length > 0) {
            const last = quotes[0];
            hint.classList.remove("hidden");
            hint.textContent = `Último: R$ ${last.cost_price.toFixed(2)}`;
            document.getElementById("quote-cost-price").placeholder =
                last.cost_price.toFixed(2);
        } else {
            hint.classList.add("hidden");
        }
    }

    calculateMargin() {
        const cost =
            parseFloat(document.getElementById("quote-cost-price").value) || 0;
        const sale =
            parseFloat(document.getElementById("quote-sale-price").value) || 0;

        const card = document.getElementById("quote-margin-card");
        const percentEl = document.getElementById("quote-margin-percent");
        const profitEl = document.getElementById("quote-profit-value");

        if (cost > 0 && sale > 0) {
            const profit = sale - cost;
            const margin = (profit / cost) * 100;

            percentEl.textContent = margin.toFixed(1) + "%";
            profitEl.textContent = "R$ " + profit.toFixed(2);

            // Cor baseada na margem
            card.classList.remove(
                "hidden",
                "bg-red-100",
                "bg-yellow-100",
                "bg-emerald-100"
            );
            if (margin < 10) card.classList.add("bg-red-100");
            else if (margin < 20) card.classList.add("bg-yellow-100");
            else card.classList.add("bg-emerald-100");
        } else {
            card.classList.add("hidden");
        }
    }

    async saveQuote() {
        const supplierId = document.getElementById("quote-supplier").value;
        const costPrice = parseFloat(
            document.getElementById("quote-cost-price").value
        );
        const salePrice = parseFloat(
            document.getElementById("quote-sale-price").value
        );

        if (!supplierId) {
            this.showToast("Selecione um fornecedor", "error");
            return;
        }
        if (!costPrice || costPrice <= 0) {
            this.showToast("Informe o preço de custo", "error");
            return;
        }

        let productId = this.currentQuoteProduct?.id;

        // Se é produto novo, cria primeiro
        if (!productId) {
            const newProduct = await window.zepocket.saveProduct({
                name: document.getElementById("quote-new-name").value,
                brand: document.getElementById("quote-new-brand").value,
                unit: document.getElementById("quote-new-unit").value || "UN",
                ean: document.getElementById("quote-new-ean").value,
                sale_price: salePrice,
            });
            productId = newProduct.id;
        }

        // Salva a cotação
        await window.zepocket.saveQuote({
            product_id: productId,
            supplier_id: parseInt(supplierId),
            cost_price: costPrice,
            sale_price: salePrice,
        });

        this.showToast("Cotação registrada!", "success");
        this.resetQuoteForm();

        // Volta para home
        if (window.spa) window.spa.back();
    }

    // ============================
    // PRODUCTS VIEW
    // ============================

    async filterProducts(term) {
        const container = document.getElementById("products-list");
        if (!container) return;

        const products = await window.zepocket.searchProducts(term);

        if (!products || products.length === 0) {
            container.innerHTML = this.ui.renderEmptyState(
                "Nenhum produto",
                "package"
            );
            return;
        }

        container.innerHTML = products
            .map(
                (p) => `
            <div class="bento-card p-4 flex items-center justify-between cursor-pointer active:bg-slate-50" 
                 data-action="edit-product" data-id="${p.id}">
                <div class="flex-1">
                    <h4 class="font-bold text-slate-800">${p.name}</h4>
                    <p class="text-xs text-slate-500">${p.brand || "-"} • ${
                    p.unit || "UN"
                }</p>
                    ${
                        p.sale_price
                            ? `<p class="text-sm font-bold text-primary mt-1">Venda: R$ ${p.sale_price.toFixed(
                                  2
                              )}</p>`
                            : ""
                    }
                </div>
                <i data-lucide="chevron-right" class="w-5 h-5 text-slate-400"></i>
            </div>
        `
            )
            .join("");

        this.ui.refreshIcons();
    }

    async editProduct(productId) {
        const products = await window.zepocket.searchProducts("");
        const product = products.find((p) => p.id === productId);
        if (!product) return;

        document.getElementById("product-sheet-title").textContent =
            "Editar Produto";
        document.getElementById("product-edit-id").value = product.id;
        document.getElementById("product-name").value = product.name;
        document.getElementById("product-brand").value = product.brand || "";
        document.getElementById("product-unit").value = product.unit || "UN";
        document.getElementById("product-ean").value = product.ean || "";
        document.getElementById("product-sale-price").value =
            product.sale_price || "";

        if (window.spa) window.spa.openSheet("add-product");
    }

    async saveProduct() {
        const id = document.getElementById("product-edit-id").value;
        const data = {
            name: document.getElementById("product-name").value,
            brand: document.getElementById("product-brand").value,
            unit: document.getElementById("product-unit").value,
            ean: document.getElementById("product-ean").value,
            sale_price:
                parseFloat(
                    document.getElementById("product-sale-price").value
                ) || null,
        };

        if (!data.name) {
            this.showToast("Nome é obrigatório", "error");
            return;
        }

        if (id) data.id = parseInt(id);

        await window.zepocket.saveProduct(data);
        this.showToast("Produto salvo!", "success");

        if (window.spa) window.spa.closeSheet();
        this.filterProducts("");

        // Reset form
        document.getElementById("product-sheet-title").textContent =
            "Novo Produto";
        document.getElementById("product-edit-id").value = "";
        document.getElementById("product-name").value = "";
        document.getElementById("product-brand").value = "";
        document.getElementById("product-unit").value = "UN";
        document.getElementById("product-ean").value = "";
        document.getElementById("product-sale-price").value = "";
    }

    // ============================
    // SUPPLIERS VIEW
    // ============================

    async loadSuppliers() {
        const container = document.getElementById("suppliers-list");
        if (!container) return;

        const suppliers = await window.zepocket.getSuppliers();

        if (!suppliers || suppliers.length === 0) {
            container.innerHTML = this.ui.renderEmptyState(
                "building-2",
                "Nenhum fornecedor",
                "Adicione seus distribuidores"
            );
            return;
        }

        container.innerHTML = suppliers
            .map((s) =>
                this.ui.renderSupplierCard(s, {
                    onEdit: `zepocketViews.editSupplier(${s.id})`,
                    onWhatsapp: s.whatsapp
                        ? `window.open('https://wa.me/55${s.whatsapp}', '_blank')`
                        : null,
                })
            )
            .join("");

        this.ui.refreshIcons();
    }

    async quickAddSupplier() {
        const input = document.getElementById("new-supplier-name");
        const name = input?.value?.trim();

        if (!name) {
            this.showToast("Digite o nome", "error");
            return;
        }

        await window.zepocket.saveSupplier({ name, is_active: true });
        input.value = "";
        this.loadSuppliers();
        this.showToast("Fornecedor adicionado!", "success");
    }

    async editSupplier(supplierId) {
        const suppliers = await window.zepocket.getSuppliers();
        const supplier = suppliers.find((s) => s.id === supplierId);
        if (!supplier) return;

        document.getElementById("supplier-sheet-title").textContent =
            "Editar Fornecedor";
        document.getElementById("supplier-edit-id").value = supplier.id;
        document.getElementById("supplier-name").value = supplier.name;
        document.getElementById("supplier-whatsapp").value =
            supplier.whatsapp || "";
        document.getElementById("supplier-notes").value = supplier.notes || "";
        document.getElementById("supplier-active").checked =
            supplier.is_active !== false;

        if (window.spa) window.spa.openSheet("add-supplier");
    }

    async saveSupplier() {
        const id = document.getElementById("supplier-edit-id").value;
        const data = {
            name: document.getElementById("supplier-name").value,
            whatsapp: document.getElementById("supplier-whatsapp").value,
            notes: document.getElementById("supplier-notes").value,
            is_active: document.getElementById("supplier-active").checked,
        };

        if (!data.name) {
            this.showToast("Nome é obrigatório", "error");
            return;
        }

        if (id) data.id = parseInt(id);

        await window.zepocket.saveSupplier(data);
        this.showToast("Fornecedor salvo!", "success");

        if (window.spa) window.spa.closeSheet();
        this.loadSuppliers();

        // Reset form
        document.getElementById("supplier-sheet-title").textContent =
            "Novo Fornecedor";
        document.getElementById("supplier-edit-id").value = "";
        document.getElementById("supplier-name").value = "";
        document.getElementById("supplier-whatsapp").value = "";
        document.getElementById("supplier-notes").value = "";
        document.getElementById("supplier-active").checked = true;
    }

    // ============================
    // ORDERS VIEW
    // ============================

    async filterOrders(status) {
        const container = document.getElementById("orders-list");
        if (!container) return;

        // Update filter buttons
        document.querySelectorAll(".order-filter-btn").forEach((btn) => {
            btn.classList.toggle(
                "active",
                btn.textContent
                    .toLowerCase()
                    .includes(status === "all" ? "todos" : status)
            );
        });

        const orders = await window.zepocket.getOrders(
            status === "all" ? null : status
        );

        if (!orders || orders.length === 0) {
            container.innerHTML = this.ui.renderEmptyState(
                "history",
                "Nenhum pedido encontrado",
                "Crie pedidos no catálogo"
            );
            return;
        }

        container.innerHTML = orders
            .map(
                (order) => `
            <div class="bento-card p-4 cursor-pointer active:bg-slate-50" 
                 data-action="view-order" data-id="${order.id}">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="font-bold text-slate-800">${
                            order.supplier_name || "Sem fornecedor"
                        }</h4>
                        <p class="text-xs text-slate-500">${new Date(
                            order.created_at
                        ).toLocaleDateString("pt-BR")}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-lg ${this.getStatusClass(
                        order.status
                    )}">${this.getStatusLabel(order.status)}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                    <span class="text-sm text-slate-600">${
                        order.items_count || 0
                    } itens</span>
                    <span class="font-bold text-primary">R$ ${(
                        order.total_amount || 0
                    ).toFixed(2)}</span>
                </div>
            </div>
        `
            )
            .join("");

        this.ui.refreshIcons();
    }

    getStatusClass(status) {
        const classes = {
            draft: "bg-slate-200 text-slate-600",
            sent: "bg-blue-100 text-blue-600",
            completed: "bg-emerald-100 text-emerald-600",
            cancelled: "bg-red-100 text-red-600",
        };
        return classes[status] || classes.draft;
    }

    getStatusLabel(status) {
        const labels = {
            draft: "Rascunho",
            sent: "Enviado",
            completed: "Concluído",
            cancelled: "Cancelado",
        };
        return labels[status] || status;
    }

    async showOrderDetail(orderId) {
        const orders = await window.zepocket.getOrders();
        const order = orders.find((o) => o.id === orderId);
        if (!order) return;

        this.currentOrderDetail = order;

        document.getElementById("order-detail-id").textContent = order.id;
        document.getElementById("order-detail-status").textContent =
            this.getStatusLabel(order.status);
        document.getElementById(
            "order-detail-status"
        ).className = `text-xs px-2 py-1 rounded-lg ${this.getStatusClass(
            order.status
        )}`;
        document.getElementById("order-detail-supplier").textContent =
            order.supplier_name || "-";
        document.getElementById("order-detail-whatsapp").textContent =
            order.supplier_whatsapp ? `📱 ${order.supplier_whatsapp}` : "-";
        document.getElementById("order-detail-total").textContent = `R$ ${(
            order.total_amount || 0
        ).toFixed(2)}`;
        document.getElementById("order-detail-profit").textContent = `R$ ${(
            order.total_profit || 0
        ).toFixed(2)}`;
        document.getElementById("order-detail-date").textContent = new Date(
            order.created_at
        ).toLocaleString("pt-BR");

        // Items
        const itemsContainer = document.getElementById("order-detail-items");
        if (order.items && order.items.length > 0) {
            itemsContainer.innerHTML = order.items
                .map(
                    (item) => `
                <div class="bg-slate-50 p-3 rounded-lg flex justify-between items-center">
                    <div>
                        <p class="font-semibold text-sm text-slate-800">${
                            item.product_name
                        }</p>
                        <p class="text-xs text-slate-500">${
                            item.quantity
                        }x R$ ${(item.unit_cost || 0).toFixed(2)}</p>
                    </div>
                    <span class="font-bold text-slate-800">R$ ${(
                        item.subtotal || 0
                    ).toFixed(2)}</span>
                </div>
            `
                )
                .join("");
        } else {
            itemsContainer.innerHTML =
                '<p class="text-center text-slate-400 text-sm py-4">Sem itens</p>';
        }

        if (window.spa) window.spa.openSheet("order-detail");
    }

    async cloneOrder() {
        if (!this.currentOrderDetail) return;
        await window.zepocket.cloneOrderToCart(this.currentOrderDetail.id);
        if (window.spa) window.spa.closeSheet();
        this.showToast("Itens adicionados ao carrinho!", "success");
        this.updateCartBadge();
    }

    async sendOrderWhatsapp() {
        if (!this.currentOrderDetail) return;
        await this.sendToWhatsapp(this.currentOrderDetail.id);
        if (window.spa) window.spa.closeSheet();
    }

    // ============================
    // PRICE COMPARE
    // ============================

    async showPriceCompare(productId) {
        const products = await window.zepocket.searchProducts("");
        const product = products.find((p) => p.id === productId);
        if (!product) return;

        this.currentCompareProduct = product;

        document.getElementById("compare-product-name").textContent =
            product.name;
        document.getElementById("compare-product-brand").textContent =
            product.brand || "-";

        const quotes = await window.zepocket.getQuotes(productId);
        const container = document.getElementById("compare-prices-list");
        const bestOption = document.getElementById("compare-best-option");

        if (!quotes || quotes.length === 0) {
            container.innerHTML = this.ui.renderEmptyState(
                "receipt",
                "Sem cotações",
                "Registre preços para este produto"
            );
            bestOption.classList.add("hidden");
        } else {
            // Agrupa por fornecedor e pega o mais recente
            const bySupplier = {};
            quotes.forEach((q) => {
                if (
                    !bySupplier[q.supplier_id] ||
                    new Date(q.quoted_at) >
                        new Date(bySupplier[q.supplier_id].quoted_at)
                ) {
                    bySupplier[q.supplier_id] = q;
                }
            });

            const prices = Object.values(bySupplier).sort(
                (a, b) => a.cost_price - b.cost_price
            );

            container.innerHTML = prices
                .map(
                    (q, i) => `
                <div class="p-3 rounded-xl ${
                    i === 0
                        ? "bg-emerald-50 border border-emerald-200"
                        : "bg-slate-50"
                } flex justify-between items-center">
                    <div>
                        <p class="font-bold text-sm ${
                            i === 0 ? "text-emerald-800" : "text-slate-800"
                        }">${q.supplier_name}</p>
                        <p class="text-[10px] text-slate-500">${new Date(
                            q.quoted_at
                        ).toLocaleDateString("pt-BR")}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-black text-lg ${
                            i === 0 ? "text-emerald-600" : "text-slate-700"
                        }">R$ ${q.cost_price.toFixed(2)}</p>
                        ${
                            q.price_variation
                                ? `<p class="text-[10px] ${
                                      q.price_variation > 0
                                          ? "text-red-500"
                                          : "text-emerald-500"
                                  }">${
                                      q.price_variation > 0 ? "↑" : "↓"
                                  } ${Math.abs(q.price_variation).toFixed(
                                      1
                                  )}%</p>`
                                : ""
                        }
                    </div>
                </div>
            `
                )
                .join("");

            // Mostra melhor opção
            if (prices.length > 0) {
                const best = prices[0];
                document.getElementById("compare-best-supplier").textContent =
                    best.supplier_name;
                document.getElementById(
                    "compare-best-price"
                ).textContent = `R$ ${best.cost_price.toFixed(2)}`;

                if (prices.length > 1) {
                    const savings =
                        prices[prices.length - 1].cost_price - best.cost_price;
                    document.getElementById(
                        "compare-best-savings"
                    ).textContent = `Economia de R$ ${savings.toFixed(
                        2
                    )} vs pior preço`;
                } else {
                    document.getElementById(
                        "compare-best-savings"
                    ).textContent = "Único fornecedor cotado";
                }
                bestOption.classList.remove("hidden");
            }
        }

        if (window.spa) window.spa.openSheet("price-compare");
        this.ui.refreshIcons();
    }

    async addBestToCart() {
        if (!this.currentCompareProduct) return;

        const quotes = await window.zepocket.getQuotes(
            this.currentCompareProduct.id
        );
        if (!quotes || quotes.length === 0) return;

        // Pega o menor preço
        const best = quotes.reduce(
            (min, q) => (q.cost_price < min.cost_price ? q : min),
            quotes[0]
        );

        await this.addToCart(
            this.currentCompareProduct.id,
            1,
            best.supplier_id
        );
        if (window.spa) window.spa.closeSheet();
    }

    // ============================
    // SETTINGS VIEW
    // ============================

    async loadSettingsData() {
        const counts = await window.zepocket.getCounts();

        document.getElementById("count-products").textContent =
            counts.products || 0;
        document.getElementById("count-suppliers").textContent =
            counts.suppliers || 0;
        document.getElementById("count-quotes").textContent =
            counts.quotes || 0;
        document.getElementById("count-orders").textContent =
            counts.orders || 0;

        const lastSync = await window.zepocket.getLastSyncTime();
        document.getElementById("last-sync-time").textContent = lastSync
            ? new Date(lastSync).toLocaleString("pt-BR")
            : "Nunca";
    }

    async confirmClearData() {
        if (
            !confirm(
                "⚠️ ATENÇÃO!\n\nIsso irá apagar TODOS os dados locais.\nEsta ação não pode ser desfeita.\n\nDeseja continuar?"
            )
        )
            return;
        if (!confirm("Tem certeza ABSOLUTA? Todos os dados serão perdidos!"))
            return;

        await window.zepocket.clearAllData();
        this.showToast("Dados apagados", "success");
        this.loadSettingsData();
    }

    // ============================
    // HOME VIEW
    // ============================

    async loadHomeData() {
        // Contadores
        const counts = await window.zepocket.getCounts();
        document.getElementById("home-count-products").textContent =
            counts.products || 0;
        document.getElementById("home-count-suppliers").textContent =
            counts.suppliers || 0;
        document.getElementById("home-count-orders").textContent =
            counts.orders || 0;

        // Mês atual
        const now = new Date();
        document.getElementById("summary-month").textContent = now
            .toLocaleDateString("pt-BR", { month: "short", year: "numeric" })
            .toUpperCase();

        // Resumo do mês
        const summary = await window.zepocket.getMonthSummary();
        document.getElementById("summary-total").textContent = `R$ ${(
            summary.total || 0
        ).toFixed(2)}`;
        document.getElementById("summary-profit").textContent = `R$ ${(
            summary.profit || 0
        ).toFixed(2)}`;
        document.getElementById("summary-orders-count").textContent =
            summary.count || 0;

        // Últimas cotações
        await this.loadRecentQuotes();

        // Status de sync
        this.updateSyncStatus();
    }

    async loadRecentQuotes() {
        const container = document.getElementById("home-recent-quotes");
        if (!container) return;

        const quotes = await window.zepocket.getRecentQuotes(5);

        if (!quotes || quotes.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 text-slate-400 text-sm">
                    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                    <p>Nenhuma cotação recente</p>
                </div>
            `;
            this.ui.refreshIcons();
            return;
        }

        container.innerHTML = quotes
            .map((q) => this.ui.renderQuoteCard(q))
            .join("");
        this.ui.refreshIcons();
    }

    showAllQuotes() {
        // TODO: Implementar página de todas as cotações
        this.showToast("Em breve!", "info");
    }

    updateSyncStatus() {
        const icon = document.getElementById("sync-status-icon");
        const text = document.getElementById("sync-status-text");

        if (navigator.onLine) {
            icon.classList.remove("bg-slate-300", "bg-red-500");
            icon.classList.add("bg-emerald-500");
            text.textContent = "Online";
        } else {
            icon.classList.remove("bg-slate-300", "bg-emerald-500");
            icon.classList.add("bg-red-500");
            text.textContent = "Offline";
        }
    }

    // ============================
    // UTILS
    // ============================

    showToast(message, type = "info") {
        // Usa toast do SPA se disponível
        if (window.spa && window.spa.toast) {
            window.spa.toast(message, type);
            return;
        }

        // Fallback simples
        const toast = document.createElement("div");
        toast.className = `fixed bottom-20 left-4 right-4 max-w-lg mx-auto p-4 rounded-xl text-white font-bold text-center z-[100] transition-all transform translate-y-full opacity-0 ${
            type === "error"
                ? "bg-red-500"
                : type === "success"
                ? "bg-emerald-500"
                : "bg-slate-800"
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);

        requestAnimationFrame(() => {
            toast.classList.remove("translate-y-full", "opacity-0");
        });

        setTimeout(() => {
            toast.classList.add("translate-y-full", "opacity-0");
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Inicialização quando página é exibida
    onPageShow(pageId) {
        if (!this.app && window.zepocket) {
            this.app = window.zepocket;
        }

        switch (pageId) {
            case "page-zepocket":
                this.loadHomeData();
                break;
            case "page-catalog":
                this.switchCatalogTab("catalog");
                this.updateCartBadge();
                break;
            case "page-products":
                this.filterProducts("");
                break;
            case "page-suppliers":
                this.loadSuppliers();
                break;
            case "page-orders":
                this.filterOrders("all");
                break;
            case "page-zepocket-settings":
                this.loadSettingsData();
                break;
        }
    }
}

// Hook para SPA page transitions
if (typeof document !== "undefined") {
    document.addEventListener("spa:pageshow", (e) => {
        if (window.zepocketViews) {
            window.zepocketViews.onPageShow(e.detail?.pageId);
        }
    });

    // Atualiza status de conexão
    window.addEventListener("online", () => {
        if (window.zepocketViews) window.zepocketViews.updateSyncStatus();
    });
    window.addEventListener("offline", () => {
        if (window.zepocketViews) window.zepocketViews.updateSyncStatus();
    });
}

export { ZePocketViews };
