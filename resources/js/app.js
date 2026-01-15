import "./bootstrap";

import { SPA as App } from "./modules/spa";

// Importa lógica global e overlays
import { initGlobalLogic } from "./modules/global-logic.js";

// Importa handlers de sheets
import { initSheets } from "./sheets/index.js";

// Importa helpers
import { initLucide } from "./utils/helpers.js";
// Import Html5Qrcode so Vite bundles it and we can expose it to window for legacy inline scripts
import { Html5Qrcode } from "html5-qrcode";

// ============================================================
// 📦 ZEPOCKET - LÓGICA DE NEGÓCIO INTEGRADA
// ============================================================

/**
 * Classe ZePocket integrada ao sistema principal
 */
class ZePocket {
    constructor(app) {
        this.app = app;
        this.db = app.db;
        this.storage = app.storage;
        this.cart = [];
        this.currentProduct = null;
        this.currentOrderDetail = null;
        this.currentCompareProduct = null;
    }

    /**
     * Inicializa o ZePocket
     */
    async init() {
        console.log("🚀 Inicializando ZePocket...");

        try {
            // Configura as tabelas do IndexedDB
            await this._setupDatabase();

            // Registra elementos
            this._registerElements();

            // Seed de dados iniciais (se necessário)
            await this._seedInitialData();

            // Bind eventos
            this._bindEvents();

            console.log("✅ ZePocket inicializado");
        } catch (error) {
            console.error("❌ Erro ao inicializar ZePocket:", error);
            this.app.toastError("Erro", "Falha ao inicializar sistema");
        }
    }

    /**
     * Configura tabelas do banco de dados
     */
    async _setupDatabase() {
        // Tabela de Produtos
        await this.db.defineTable("zepocket_products", {
            keyPath: "id",
            autoIncrement: false,
            indexes: [
                { name: "name", keyPath: "name" },
                { name: "brand", keyPath: "brand" },
                { name: "ean", keyPath: "ean", unique: true },
            ],
        });

        // Tabela de Fornecedores
        await this.db.defineTable("zepocket_suppliers", {
            keyPath: "id",
            autoIncrement: true,
            indexes: [{ name: "name", keyPath: "name" }],
        });

        // Tabela de Cotações
        await this.db.defineTable("zepocket_quotes", {
            keyPath: "id",
            autoIncrement: false,
            indexes: [
                { name: "productId", keyPath: "productId" },
                { name: "supplierId", keyPath: "supplierId" },
            ],
        });

        // Tabela de Pedidos exportados
        await this.db.defineTable("zepocket_orders", {
            keyPath: "id",
            autoIncrement: false,
            indexes: [{ name: "date", keyPath: "date" }],
        });
    }

    /**
     * Registra elementos importantes
     */
    _registerElements() {
        this.app.registerElements({
            // Catalog
            catalogSearch: "catalog-search",
            catalogList: "catalog-list",
            cartBadge: "catalog-cart-badge",
            headerTotalItems: "headerTotalItems",

            // Quote
            quoteSearch: "quote-search",
            quotePrice: "quote-cost-price",
            quoteSalePrice: "quote-sale-price",
            quoteSupplier: "quote-supplier",
        });
    }

    /**
     * Seed de dados iniciais
     */
    async _seedInitialData() {
        const productsCount = await this.db.table("zepocket_products").count();

        if (productsCount === 0) {
            console.log("📦 Seed inicial de produtos...");

            const initialProducts = [
                {
                    id: "SYS-1",
                    ean: "7894900011500",
                    name: "Pepsi Original",
                    brand: "Pepsi",
                    unit: "2L",
                    salePrice: 10.0,
                },
                {
                    id: "SYS-2",
                    ean: "7894900011517",
                    name: "Coca-Cola Original",
                    brand: "Coca-Cola",
                    unit: "2L",
                    salePrice: 12.0,
                },
            ];

            for (const product of initialProducts) {
                await this.db.table("zepocket_products").upsert(product);
            }
        }

        const suppliersCount = await this.db
            .table("zepocket_suppliers")
            .count();

        if (suppliersCount === 0) {
            console.log("🏪 Seed inicial de fornecedores...");

            const initialSuppliers = [
                { name: "Atacadão Distribuidor" },
                { name: "Martins Atacado" },
            ];

            for (const supplier of initialSuppliers) {
                await this.db.table("zepocket_suppliers").upsert(supplier);
            }
        }
    }

    /**
     * Bind eventos globais
     */
    _bindEvents() {
        // Delegação de eventos
        document.addEventListener("click", (e) => {
            const el = e.target.closest("[data-zepocket-action]");
            if (!el) return;

            const action = el.dataset.zepocketAction;
            const id = el.dataset.id;
            const value = el.dataset.value;

            this.handleAction(action, id, value, el);
        });

        // Eventos de Input
        document.addEventListener("input", (e) => {
            if (e.target.id === "catalog-search") {
                this.searchCatalog(e.target.value);
            }
            if (e.target.id === "quote-search") {
                this.searchQuote(e.target.value);
            }
            if (
                e.target.id === "quote-cost-price" ||
                e.target.id === "quote-sale-price"
            ) {
                this.calculateMargin();
            }
        });

        // Escuta mudanças de página
        document.addEventListener("page:enter", (e) => {
            const pageId = e.detail?.pageId;
            if (pageId === "catalog") this.loadCatalog();
            if (pageId === "quote") this.loadQuote();
        });
    }

    /**
     * Handler central de ações
     */
    handleAction(action, id, value, el) {
        switch (action) {
            // Catálogo
            case "toggle-cart":
                this.toggleCart(id);
                break;
            case "remove-from-cart":
                this.removeFromCart(id);
                break;
            case "switch-tab":
                this.switchTab(value);
                break;
            case "update-cart-qty":
                this.updateCartQuantity(id, el.value);
                break;
            case "update-cart-supplier":
                this.updateCartSupplier(id, el.value);
                break;

            // Quote
            case "select-product":
                this.selectProduct(id);
                break;
            case "save-quote":
                this.saveQuote();
                break;
            case "reset-quote":
                this.resetQuote();
                break;

            default:
                console.warn(`Ação não reconhecida: ${action}`);
        }
    }

    // ============================================================
    // CATÁLOGO - MÉTODOS
    // ============================================================

    async loadCatalog() {
        await this.renderCatalog();
    }

    async searchCatalog(query) {
        await this.renderCatalog(query);
    }

    async renderCatalog(filter = "") {
        const products = await this.db.table("zepocket_products").all();
        const term = filter.toLowerCase();
        const filtered = products.filter((p) =>
            p.name.toLowerCase().includes(term)
        );

        const listEl = this.app.el("catalogList");
        if (!listEl) return;

        if (filtered.length === 0) {
            listEl.innerHTML = `<div class="text-center text-slate-400 py-10">Nada encontrado.</div>`;
            return;
        }

        let html = "";
        for (const product of filtered) {
            const bestQuote = await this.getBestQuote(product.id);
            const isInCart = this.cart.find((c) => c.productId === product.id);
            const supplierName = bestQuote
                ? (
                      await this.db
                          .table("zepocket_suppliers")
                          .find(bestQuote.supplierId)
                  )?.name
                : "--";
            const priceDisplay = bestQuote
                ? `R$ ${bestQuote.price.toFixed(2)}`
                : "Sem cotação";

            html += `
                <div class="bento-card p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-sm">${product.name}</h3>
                            <p class="text-xs text-slate-500">${
                                product.brand
                            } · ${product.unit}</p>
                            <p class="text-xs text-emerald-600 font-bold mt-1">${priceDisplay}</p>
                            <p class="text-xs text-slate-400">${supplierName}</p>
                        </div>
                        <button 
                            data-zepocket-action="toggle-cart" 
                            data-id="${product.id}"
                            class="p-2 rounded-lg ${
                                isInCart
                                    ? "bg-emerald-500 text-white"
                                    : "bg-slate-100 text-slate-600"
                            }">
                            <i class="ph ph-${
                                isInCart ? "check" : "plus"
                            } text-lg"></i>
                        </button>
                    </div>
                </div>
            `;
        }

        listEl.innerHTML = html;
    }

    async getBestQuote(productId) {
        const quotes = await this.db
            .table("zepocket_quotes")
            .where("productId", productId)
            .all();
        if (quotes.length === 0) return null;
        return quotes.sort((a, b) => a.price - b.price)[0];
    }

    toggleCart(productId) {
        const index = this.cart.findIndex((c) => c.productId === productId);

        if (index > -1) {
            this.cart.splice(index, 1);
            this.app.toastSuccess("Removido", "Item removido do carrinho");
        } else {
            const bestQuote = this.getBestQuote(productId);
            if (!bestQuote) {
                this.app.toastWarning("Aviso", "Produto sem cotação");
                return;
            }
            this.cart.push({
                productId,
                supplierId: bestQuote.supplierId,
                qty: 1,
                price: bestQuote.price,
            });
            this.app.toastSuccess("Adicionado", "Item adicionado ao carrinho");
        }

        this.updateCartUI();
    }

    removeFromCart(productId) {
        const index = this.cart.findIndex((c) => c.productId === productId);
        if (index > -1) {
            this.cart.splice(index, 1);
            this.updateCartUI();
            this.app.toastSuccess("Removido", "Item removido");
        }
    }

    updateCartQuantity(productId, qty) {
        const item = this.cart.find((c) => c.productId === productId);
        if (item) {
            item.qty = parseInt(qty) || 1;
            this.updateCartUI();
        }
    }

    updateCartSupplier(productId, supplierId) {
        const item = this.cart.find((c) => c.productId === productId);
        if (item) {
            item.supplierId = parseInt(supplierId);
            this.updateCartUI();
        }
    }

    async updateCartUI() {
        await this.renderCatalog();

        const badge = this.app.el.cartBadge;
        if (badge) {
            if (this.cart.length > 0) {
                badge.textContent = this.cart.length;
                badge.classList.remove("hidden");
            } else {
                badge.classList.add("hidden");
            }
        }

        const totalEl = this.app.el.headerTotalItems;
        if (totalEl) {
            totalEl.textContent = this.cart.length;
        }
    }

    switchTab(tab) {
        const tabs = ["catalogo", "carrinho", "exportar"];
        tabs.forEach((t) => {
            const view = document.getElementById(`view-${t}`);
            const btn = document.getElementById(`btn-${t}`);
            if (view) {
                view.classList.toggle("hidden", t !== tab);
            }
            if (btn) {
                if (t === tab) {
                    btn.className =
                        "active-tab py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex flex-col items-center gap-1 relative";
                } else {
                    btn.className =
                        "inactive-tab py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm flex flex-col items-center gap-1 relative";
                }
            }
        });
    }

    // ============================================================
    // COTAÇÃO - MÉTODOS
    // ============================================================

    async loadQuote() {
        await this.renderSuppliersSelect();
    }

    async searchQuote(query) {
        // Implementar busca de produtos para cotação
        const products = await this.db.table("zepocket_products").all();
        const term = query.toLowerCase();
        const filtered = products.filter((p) =>
            p.name.toLowerCase().includes(term)
        );

        // Renderizar sugestões
        const listEl = document.getElementById("suggestionsList");
        if (!listEl) return;

        if (query.length < 2) {
            listEl.classList.add("hidden");
            return;
        }

        let html = filtered
            .map(
                (p) => `
            <div 
                data-zepocket-action="select-product" 
                data-id="${p.id}"
                class="p-3 hover:bg-slate-50 cursor-pointer border-b border-slate-100">
                <p class="font-bold text-sm">${p.name}</p>
                <p class="text-xs text-slate-500">${p.brand} · ${p.unit}</p>
            </div>
        `
            )
            .join("");

        listEl.innerHTML = html;
        listEl.classList.remove("hidden");
    }

    async selectProduct(id) {
        this.currentProduct = await this.db.table("zepocket_products").find(id);

        if (!this.currentProduct) return;

        // Preenche o formulário
        const nameEl = document.getElementById("selectedName");
        const brandEl = document.getElementById("selectedBrand");
        const unitEl = document.getElementById("selectedUnit");
        const eanEl = document.getElementById("selectedEan");
        const salePriceEl = this.app.el.quoteSalePrice;

        if (nameEl) nameEl.textContent = this.currentProduct.name;
        if (brandEl) brandEl.textContent = this.currentProduct.brand;
        if (unitEl) unitEl.textContent = this.currentProduct.unit;
        if (eanEl) eanEl.textContent = this.currentProduct.ean || "Sem EAN";
        if (salePriceEl)
            salePriceEl.value = this.currentProduct.salePrice?.toFixed(2) || "";

        // Mostra formulário
        const formEl = document.getElementById("quoteFormArea");
        if (formEl) formEl.classList.remove("hidden");

        // Esconde sugestões
        const listEl = document.getElementById("suggestionsList");
        if (listEl) listEl.classList.add("hidden");

        await this.renderSuppliersSelect();
    }

    async checkPriceHistory() {
        const supplierEl =
            document.getElementById("quote-supplier") ||
            document.getElementById("quoteSupplier");
        const priceInput =
            document.getElementById("quote-cost-price") ||
            document.getElementById("quotePrice");
        const hint =
            document.getElementById("priceHint") ||
            document.getElementById("quote-price-hint") ||
            document.getElementById("priceHint");

        if (hint) hint.classList.add("hidden");
        if (priceInput) priceInput.value = "";

        const supplierId = supplierEl ? parseInt(supplierEl.value) : null;
        if (!this.currentProduct || !this.currentProduct.id || !supplierId)
            return;

        const quotes = await this.db
            .table("zepocket_quotes")
            .where(
                (q) =>
                    q.productId === this.currentProduct.id &&
                    q.supplierId === supplierId
            )
            .all();

        if (quotes && quotes.length > 0) {
            const q = quotes[0];
            if (hint) {
                hint.classList.remove("hidden");
                hint.textContent = `Último: R$ ${Number(
                    q.price || q.cost_price || 0
                ).toFixed(2)}`;
            }
            if (priceInput && q.price)
                priceInput.placeholder = Number(q.price).toFixed(2);
        }
    }

    calculateMargin() {
        const costEl = this.app.el.quotePrice;
        const saleEl = this.app.el.quoteSalePrice;
        const cardEl = document.getElementById("marginCard");

        if (!costEl || !saleEl || !cardEl) return;

        const cost = parseFloat(costEl.value);
        const sale = parseFloat(saleEl.value);

        if (isNaN(cost) || isNaN(sale) || cost <= 0) {
            cardEl.classList.add("hidden");
            return;
        }

        cardEl.classList.remove("hidden");

        const profit = sale - cost;
        const margin = sale > 0 ? (profit / sale) * 100 : 0;

        const profitEl = document.getElementById("profitValue");
        const marginEl = document.getElementById("marginPercent");

        if (profitEl) profitEl.textContent = `R$ ${profit.toFixed(2)}`;
        if (marginEl) marginEl.textContent = `${margin.toFixed(1)}%`;

        // Cores dinâmicas
        if (profit > 0) {
            cardEl.className =
                "bg-emerald-100 text-emerald-800 rounded-xl p-3 flex justify-between items-center transition-colors duration-300";
        } else if (profit < 0) {
            cardEl.className =
                "bg-red-100 text-red-800 rounded-xl p-3 flex justify-between items-center transition-colors duration-300";
        } else {
            cardEl.className =
                "bg-slate-100 text-slate-800 rounded-xl p-3 flex justify-between items-center transition-colors duration-300";
        }
    }

    async renderSuppliersSelect() {
        const suppliers = await this.db.table("zepocket_suppliers").all();
        const selectEl = this.app.el.quoteSupplier;

        if (!selectEl) return;

        selectEl.innerHTML = `
            <option value="">Selecione...</option>
            ${suppliers
                .map((s) => `<option value="${s.id}">${s.name}</option>`)
                .join("")}
        `;
    }

    async saveQuote() {
        const supplierEl = this.app.el.quoteSupplier;
        const priceEl = this.app.el.quotePrice;
        const salePriceEl = this.app.el.quoteSalePrice;

        if (!supplierEl || !priceEl) return;

        const supplierId = parseInt(supplierEl.value);
        const price = parseFloat(priceEl.value);
        const salePrice = parseFloat(salePriceEl?.value);

        if (!supplierId || isNaN(price)) {
            this.app.toastWarning("Aviso", "Preencha todos os campos");
            return;
        }

        // Salva cotação
        const quoteId = `${this.currentProduct.id}-${supplierId}`;
        await this.db.table("zepocket_quotes").upsert({
            id: quoteId,
            productId: this.currentProduct.id,
            supplierId,
            price,
            date: new Date().toISOString(),
        });

        // Atualiza preço de venda do produto
        if (!isNaN(salePrice)) {
            this.currentProduct.salePrice = salePrice;
            await this.db
                .table("zepocket_products")
                .upsert(this.currentProduct);
        }

        this.app.toastSuccess("Sucesso", "Cotação salva!");
        this.resetQuote();
    }

    resetQuote() {
        this.currentProduct = null;

        const searchEl = document.getElementById("searchInput");
        const formEl = document.getElementById("quoteFormArea");
        const priceEl = this.app.el.quotePrice;
        const salePriceEl = this.app.el.quoteSalePrice;

        if (searchEl) searchEl.value = "";
        if (formEl) formEl.classList.add("hidden");
        if (priceEl) priceEl.value = "";
        if (salePriceEl) salePriceEl.value = "";
    }
}

// Inicializa a aplicação SPA
document.addEventListener("DOMContentLoaded", async () => {
    window.app = new App({
        homePage: "home",
        animation: {
            type: "zoom",
            speed: 0.35,
        },
        ui: {
            autoTheme: false, // Desabilita a detecção automática do tema do sistema
        },
        pwa: {
            enabled: true,
            showBanner: false, // Desabilita o banner automático para não conflitar com o LGPD
        },
        db: {
            name: "supply",
            version: 1,
        },
        storage: {
            prefix: "supply_",
        },
    });
    await app.init();

    // Inicializa lógica global (overlays, tema, LGPD)
    initGlobalLogic(app);

    // Inicializa sheets
    initSheets(app);

    // Inicializa ícones Lucide
    initLucide();

    // ============================================================
    // INICIALIZA ZEPOCKET
    // ============================================================
    const zepocket = new ZePocket(app);
    await zepocket.init();

    // Expõe globalmente
    window.zepocket = zepocket;
    app.zepocket = zepocket;

    // Compatibilidade com handlers inline legados
    window.renderCatalog = (q) => zepocket.renderCatalog(q);
    window.switchTab = (t) => zepocket.switchTab(t);
    window.handleSearch = (q) => {
        try {
            const page = app.current;
            if (page === "quote") return zepocket.searchQuote(q);
            return zepocket.searchCatalog(q);
        } catch (e) {
            return zepocket.searchCatalog(q);
        }
    };
    window.saveQuote = () => zepocket.saveQuote();
    window.clearSelection = () => zepocket.resetQuote();
    window.checkPriceHistory = () => zepocket.checkPriceHistory();

    console.log({ app, zepocket });

    // Expose Html5Qrcode to window so existing inline scanner scripts work without CDN
    try {
        if (typeof Html5Qrcode !== "undefined")
            window.Html5Qrcode = Html5Qrcode;
    } catch (e) {
        console.warn("Could not expose Html5Qrcode to window", e);
    }

    // Re-init icons when SPA injects new page content
    document.addEventListener("spa:page-loaded", (e) => {
        try {
            initLucide();
        } catch (err) {
            console.warn("initLucide error", err);
        }
    });

    // Observe DOM for dynamically injected elements with data-lucide and initialize icons
    try {
        const debouncedInit = (function () {
            let t;
            return function () {
                clearTimeout(t);
                t = setTimeout(() => {
                    try {
                        initLucide();
                    } catch (e) {}
                }, 120);
            };
        })();

        let lucideProcessing = false;
        const observer = new MutationObserver((mutations) => {
            for (const m of mutations) {
                if (m.addedNodes && m.addedNodes.length) {
                    for (const node of m.addedNodes) {
                        if (node.nodeType === 1) {
                            if (lucideProcessing) return;
                            if (node.matches && node.matches("[data-lucide]")) {
                                lucideProcessing = true;
                                debouncedInit();
                                setTimeout(
                                    () => (lucideProcessing = false),
                                    400
                                );
                                return;
                            }
                            if (
                                node.querySelector &&
                                node.querySelector("[data-lucide]")
                            ) {
                                lucideProcessing = true;
                                debouncedInit();
                                setTimeout(
                                    () => (lucideProcessing = false),
                                    400
                                );
                                return;
                            }
                        }
                    }
                }
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });
    } catch (err) {
        console.warn("Lucide observer init failed", err);
    }
});
