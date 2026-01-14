/**
 * 🎨 ZePocket - UI Components
 * Componentes de interface para renderização dinâmica
 */

/**
 * Classe de componentes UI do ZePocket
 */
export class ZePocketUI {
    constructor(app) {
        this.app = app;
        this.db = app.db;
        this.spa = app.spa;
    }

    /**
     * Formata valor em Real
     */
    formatCurrency(value) {
        return new Intl.NumberFormat("pt-BR", {
            style: "currency",
            currency: "BRL",
        }).format(value || 0);
    }

    /**
     * Formata porcentagem
     */
    formatPercent(value) {
        return `${(value || 0).toFixed(1)}%`;
    }

    /**
     * Renderiza card de produto no catálogo
     */
    renderProductCard(product, isInCart = false) {
        const priceDisplay = product.best_price
            ? this.formatCurrency(product.best_price)
            : "--";

        let profitHtml = "";
        if (product.best_price && product.sale_price > 0) {
            const profit = product.sale_price - product.best_price;
            const color = profit >= 0 ? "emerald" : "red";
            profitHtml = `
                <span class="ml-2 text-[10px] font-bold text-${color}-600 bg-${color}-50 px-1.5 py-0.5 rounded">
                    Lucro: ${this.formatCurrency(profit)}
                </span>
            `;
        }

        // Variação de preço
        let variationHtml = "";
        if (
            product.price_variation !== undefined &&
            product.price_variation !== null
        ) {
            const isUp = product.price_variation > 0;
            variationHtml = `
                <span class="text-[10px] font-bold ${
                    isUp ? "text-red-500" : "text-emerald-500"
                }">
                    ${isUp ? "▲" : "▼"} ${this.formatCurrency(
                Math.abs(product.price_variation)
            )}
                </span>
            `;
        }

        return `
            <div class="bento-card p-3 flex items-center justify-between ${
                isInCart
                    ? "border-emerald-500 ring-1 ring-emerald-500 bg-emerald-50/30"
                    : ""
            }" 
                 data-product-id="${product.id}">
                <div class="flex-1">
                    <h3 class="font-bold text-slate-800 text-sm">${
                        product.name
                    }</h3>
                    <div class="flex gap-2 text-xs text-slate-500 mt-0.5">
                        ${
                            product.brand
                                ? `<span class="bg-slate-100 px-1.5 rounded">${product.brand}</span>`
                                : ""
                        }
                        <span class="bg-slate-100 px-1.5 rounded">${
                            product.unit || "UN"
                        }</span>
                    </div>
                    <div class="mt-2 text-xs flex items-center flex-wrap gap-1">
                        <span class="text-slate-400 mr-1">Custo:</span> 
                        <span class="font-bold text-slate-700">${priceDisplay}</span>
                        ${variationHtml}
                        ${profitHtml}
                    </div>
                </div>
                
                <button data-action="toggle-cart" data-id="${product.id}" 
                        class="ml-3 w-10 h-10 rounded-xl flex items-center justify-center transition-all 
                               ${
                                   product.best_price
                                       ? ""
                                       : "opacity-50 pointer-events-none"
                               } 
                               ${
                                   isInCart
                                       ? "bg-emerald-100 text-emerald-600"
                                       : "bg-slate-100 text-slate-400 hover:bg-emerald-500 hover:text-white"
                               }">
                    <i data-lucide="${
                        isInCart ? "check" : "plus"
                    }" class="w-5 h-5"></i>
                </button>
            </div>
        `;
    }

    /**
     * Renderiza item do carrinho
     */
    renderCartItem(item, availableQuotes = []) {
        const { product, supplier, quantity, unit_cost, subtotal, profit } =
            item;

        const options = availableQuotes
            .map(
                (q) => `
            <option value="${q.supplier_id}" ${
                    q.supplier_id === item.supplier_id ? "selected" : ""
                }>
                ${q.supplier_name || "Fornecedor"} (${this.formatCurrency(
                    q.cost_price
                )})
            </option>
        `
            )
            .join("");

        return `
            <div class="bento-card p-4 relative" data-cart-item="${
                item.product_id
            }">
                <button data-action="remove-from-cart" data-id="${
                    item.product_id
                }" 
                        class="absolute top-4 right-4 text-red-400 hover:text-red-600">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
                
                <h3 class="font-bold text-slate-800 text-sm mb-3 pr-8">${
                    product?.name || "Produto"
                }</h3>

                <div class="grid grid-cols-1 gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Fornecedor</label>
                        <select data-action="update-cart-supplier" data-id="${
                            item.product_id
                        }" 
                                class="w-full bg-white border border-slate-200 text-xs font-medium p-2 rounded-lg outline-none focus:border-emerald-500">
                            ${options}
                        </select>
                    </div>

                    <div class="flex gap-3">
                        <div class="flex-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Qtd</label>
                            <div class="flex items-center">
                                <button data-action="update-cart-qty" data-id="${
                                    item.product_id
                                }" data-qty="${quantity - 1}" 
                                        class="w-8 h-8 bg-white border border-slate-200 rounded-l-lg hover:bg-slate-100">-</button>
                                <input type="number" readonly value="${quantity}" 
                                       class="w-full h-8 text-center bg-white border-y border-slate-200 text-sm font-bold text-slate-700">
                                <button data-action="update-cart-qty" data-id="${
                                    item.product_id
                                }" data-qty="${quantity + 1}" 
                                        class="w-8 h-8 bg-white border border-slate-200 rounded-r-lg hover:bg-slate-100">+</button>
                            </div>
                        </div>
                        <div class="flex-1 text-right">
                            <label class="text-[10px] font-bold text-slate-400 uppercase">Subtotal</label>
                            <div class="h-8 flex flex-col justify-center items-end leading-none">
                                <span class="text-slate-800 font-bold text-sm">${this.formatCurrency(
                                    subtotal
                                )}</span>
                                ${
                                    profit !== null
                                        ? `
                                    <span class="text-[10px] ${
                                        profit >= 0
                                            ? "text-emerald-600"
                                            : "text-red-500"
                                    } font-medium">
                                        Lucro: ${this.formatCurrency(profit)}
                                    </span>
                                `
                                        : ""
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Renderiza card de pedido para exportação
     */
    renderOrderExportCard(order) {
        const { supplier, items = [], total_amount, total_profit } = order;

        return `
            <div class="bento-card p-5">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-bold text-lg text-slate-800">${
                            supplier?.name || "Fornecedor"
                        }</h3>
                        <p class="text-xs text-slate-500">${
                            items.length
                        } itens neste pedido</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-400 font-bold uppercase">Custo Total</p>
                        <p class="text-xl font-bold text-slate-800">${this.formatCurrency(
                            total_amount
                        )}</p>
                    </div>
                </div>

                <div class="mb-4 bg-emerald-50 border border-emerald-100 p-2 rounded-lg flex justify-between items-center">
                    <span class="text-xs font-bold text-emerald-800 flex items-center gap-1">
                        <i data-lucide="trending-up" class="w-4 h-4"></i> Lucro Previsto:
                    </span>
                    <span class="text-sm font-black text-emerald-700">${this.formatCurrency(
                        total_profit
                    )}</span>
                </div>

                <div class="flex gap-2">
                    <button data-action="view-order" data-id="${order.id}" 
                            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition">
                        <i data-lucide="eye" class="w-4 h-4"></i> Detalhes
                    </button>
                    <button data-action="send-order-whatsapp" data-id="${
                        order.id
                    }" 
                            class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white py-2.5 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition">
                        <i data-lucide="message-circle" class="w-4 h-4"></i> WhatsApp
                    </button>
                </div>
            </div>
        `;
    }

    /**
     * Renderiza card de fornecedor
     */
    renderSupplierCard(supplier, quotesCount = 0) {
        return `
            <div class="bento-card p-4 flex justify-between items-center" data-supplier-id="${
                supplier.id
            }">
                <div class="flex-1">
                    <h3 class="font-bold text-sm text-slate-800">${
                        supplier.name
                    }</h3>
                    ${
                        supplier.contact_name
                            ? `<p class="text-xs text-slate-500">${supplier.contact_name}</p>`
                            : ""
                    }
                    <div class="flex gap-2 mt-2">
                        ${
                            supplier.phone
                                ? `
                            <a href="tel:${supplier.phone}" class="text-xs text-blue-600 hover:underline flex items-center gap-1">
                                <i data-lucide="phone" class="w-3 h-3"></i> ${supplier.phone}
                            </a>
                        `
                                : ""
                        }
                        ${
                            supplier.whatsapp
                                ? `
                            <a href="${supplier.whatsapp_link}" target="_blank" 
                               class="text-xs text-emerald-600 hover:underline flex items-center gap-1">
                                <i data-lucide="message-circle" class="w-3 h-3"></i> WhatsApp
                            </a>
                        `
                                : ""
                        }
                    </div>
                    ${
                        quotesCount > 0
                            ? `
                        <span class="mt-2 inline-block text-[10px] bg-slate-100 px-2 py-0.5 rounded text-slate-500">
                            ${quotesCount} cotações
                        </span>
                    `
                            : ""
                    }
                </div>
                <div class="flex gap-2">
                    <button data-action="edit-supplier" data-id="${
                        supplier.id
                    }" 
                            class="p-2 text-slate-400 hover:text-blue-600 transition">
                        <i data-lucide="pencil" class="w-4 h-4"></i>
                    </button>
                    <button data-action="delete-supplier" data-id="${
                        supplier.id
                    }" 
                            class="p-2 text-slate-400 hover:text-red-600 transition">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        `;
    }

    /**
     * Renderiza card de cotação
     */
    renderQuoteCard(quote, product, supplier) {
        const margin = product?.sale_price
            ? (
                  ((product.sale_price - quote.cost_price) /
                      product.sale_price) *
                  100
              ).toFixed(1)
            : null;

        let variationHtml = "";
        if (quote.is_price_increase) {
            variationHtml = `<span class="text-red-500 text-xs">▲ +${this.formatCurrency(
                quote.price_variation
            )}</span>`;
        } else if (quote.is_price_decrease) {
            variationHtml = `<span class="text-emerald-500 text-xs">▼ ${this.formatCurrency(
                quote.price_variation
            )}</span>`;
        }

        return `
            <div class="bg-white border border-slate-200 p-3 rounded-lg">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm">${
                            product?.name || "Produto"
                        }</h4>
                        <p class="text-xs text-slate-500">${
                            supplier?.name || "Fornecedor"
                        }</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-lg text-slate-800">${this.formatCurrency(
                            quote.cost_price
                        )}</p>
                        ${variationHtml}
                    </div>
                </div>
                ${
                    margin !== null
                        ? `
                    <div class="mt-2 pt-2 border-t border-slate-100 flex justify-between text-xs">
                        <span class="text-slate-400">Venda: ${this.formatCurrency(
                            product.sale_price
                        )}</span>
                        <span class="font-bold ${
                            parseFloat(margin) >= 0
                                ? "text-emerald-600"
                                : "text-red-500"
                        }">
                            Margem: ${margin}%
                        </span>
                    </div>
                `
                        : ""
                }
            </div>
        `;
    }

    /**
     * Renderiza card de margem de lucro
     */
    renderMarginCard(costPrice, salePrice) {
        if (!costPrice || !salePrice) return "";

        const profit = salePrice - costPrice;
        const margin = salePrice > 0 ? (profit / salePrice) * 100 : 0;

        const colorClass =
            profit > 0
                ? "bg-emerald-100 text-emerald-800"
                : profit < 0
                ? "bg-red-100 text-red-800"
                : "bg-slate-100 text-slate-800";

        return `
            <div class="${colorClass} rounded-xl p-3 flex justify-between items-center transition-colors duration-300">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider opacity-60">Margem de Lucro</p>
                    <p class="text-xl font-black">${margin.toFixed(1)}%</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-bold uppercase tracking-wider opacity-60">Lucro Unitário</p>
                    <p class="text-lg font-bold">${this.formatCurrency(
                        profit
                    )}</p>
                </div>
            </div>
        `;
    }

    /**
     * Renderiza footer do carrinho
     */
    renderCartFooter(totals) {
        return `
            <div class="flex justify-between items-end mb-3 pb-3 border-b border-slate-700">
                <div>
                    <p class="text-[10px] uppercase text-slate-400 font-bold tracking-wider">Custo Total</p>
                    <p class="text-xl font-bold text-white">${this.formatCurrency(
                        totals.totalCost
                    )}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] uppercase text-emerald-400 font-bold tracking-wider">Lucro Estimado</p>
                    <p class="text-lg font-bold text-emerald-400">${this.formatCurrency(
                        totals.totalProfit
                    )}</p>
                </div>
            </div>
            <button data-action="create-order" 
                    class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-3 rounded-xl font-bold text-sm transition flex items-center justify-center gap-2">
                Finalizar Compra <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        `;
    }

    /**
     * Renderiza lista vazia
     */
    renderEmptyState(message = "Nenhum item encontrado", icon = "inbox") {
        return `
            <div class="text-center py-10 text-slate-400">
                <i data-lucide="${icon}" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                <p>${message}</p>
            </div>
        `;
    }

    /**
     * Atualiza ícones Lucide
     */
    refreshIcons() {
        if (typeof lucide !== "undefined") {
            lucide.createIcons();
        }
    }
}

// Export
if (typeof window !== "undefined") {
    window.ZePocketUI = ZePocketUI;
}
