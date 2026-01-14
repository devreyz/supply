/**
 * ⚡ ZePocket - Actions
 * Ações de negócio para o módulo ZePocket
 */

/**
 * Registra todas as actions de negócio
 */
export function registerActions(app) {
    const { db, queue, sync, spa } = app;

    // =================== PRODUTO ACTIONS ===================

    /**
     * Busca produtos (local)
     */
    app.searchProducts = async (term) => {
        if (!term || term.length < 2) {
            return db.getAllProducts();
        }
        return db.searchProducts(term);
    };

    /**
     * Cria/atualiza produto
     */
    app.saveProduct = async (productData) => {
        // Salva localmente
        const product = await db.saveProduct(productData);

        // Adiciona à fila de sync
        await queue.dispatch("sync_change", {
            type: "product",
            action: productData.id ? "update" : "create",
            data: productData,
            local_id: product.id?.toString(),
        });

        spa?.toastSuccess("Produto salvo!");
        return product;
    };

    // =================== FORNECEDOR ACTIONS ===================

    /**
     * Lista fornecedores
     */
    app.getSuppliers = async (activeOnly = true) => {
        return db.getAllSuppliers(activeOnly);
    };

    /**
     * Cria/atualiza fornecedor
     */
    app.saveSupplier = async (supplierData) => {
        const supplier = await db.saveSupplier(supplierData);

        await queue.dispatch("sync_change", {
            type: "supplier",
            action: supplierData.id ? "update" : "create",
            data: supplierData,
            local_id: supplier.id?.toString(),
        });

        spa?.toastSuccess("Fornecedor salvo!");
        return supplier;
    };

    /**
     * Remove fornecedor
     */
    app.deleteSupplier = async (id) => {
        await db.deleteSupplier(id);

        await queue.dispatch("sync_change", {
            type: "supplier",
            action: "delete",
            data: { id },
        });

        spa?.toastSuccess("Fornecedor removido!");
    };

    // =================== COTAÇÃO ACTIONS ===================

    /**
     * Busca cotações de um produto
     */
    app.getProductQuotes = async (productId) => {
        return db.getQuotesForProduct(productId);
    };

    /**
     * Salva cotação (preço de custo)
     */
    app.saveQuote = async (quoteData) => {
        // Calcula variação se houver preço anterior
        const existingQuote = await db.getQuote(
            quoteData.product_id,
            quoteData.supplier_id
        );
        if (existingQuote) {
            quoteData.previous_price = existingQuote.cost_price;
            quoteData.price_variation =
                quoteData.cost_price - existingQuote.cost_price;
        }

        const quote = await db.saveQuote({
            ...quoteData,
            last_quoted_at: new Date().toISOString(),
        });

        // Atualiza preço de venda no produto se enviado
        if (quoteData.sale_price !== undefined) {
            const product = await db.getProduct(quoteData.product_id);
            if (product) {
                await db.saveProduct({
                    ...product,
                    sale_price: quoteData.sale_price,
                });
            }
        }

        await queue.dispatch("sync_change", {
            type: "quote",
            action: existingQuote ? "update" : "create",
            data: quoteData,
        });

        spa?.toastSuccess("Cotação salva!");
        return quote;
    };

    /**
     * Obtém melhor preço para um produto
     */
    app.getBestPrice = async (productId) => {
        return db.getBestQuote(productId);
    };

    // =================== CARRINHO ACTIONS ===================

    /**
     * Obtém carrinho
     */
    app.getCart = async () => {
        return db.getCart();
    };

    /**
     * Obtém carrinho com detalhes (produtos e fornecedores)
     */
    app.getCartWithDetails = async () => {
        return db.getCartWithDetails();
    };

    /**
     * Adiciona item ao carrinho
     */
    app.addToCart = async (productId, quantity = 1) => {
        const product = await db.getProduct(productId);
        if (!product) {
            spa?.toastError("Produto não encontrado");
            return null;
        }

        const bestQuote = await db.getBestQuote(productId);
        if (!bestQuote) {
            spa?.toastError("Produto sem cotação cadastrada");
            return null;
        }

        const item = await db.addToCart({
            product_id: productId,
            supplier_id: bestQuote.supplier_id,
            quantity,
            unit_cost: bestQuote.cost_price,
        });

        spa?.toastSuccess("Adicionado ao carrinho!");
        app.emit("cart:updated");
        return item;
    };

    /**
     * Atualiza item do carrinho
     */
    app.updateCartItem = async (productId, data) => {
        // Se mudou o fornecedor, atualiza o preço
        if (data.supplier_id) {
            const quote = await db.getQuote(productId, data.supplier_id);
            if (quote) {
                data.unit_cost = quote.cost_price;
            }
        }

        const item = await db.updateCartItem(productId, data);
        app.emit("cart:updated");
        return item;
    };

    /**
     * Remove item do carrinho
     */
    app.removeFromCart = async (productId) => {
        await db.removeFromCart(productId);
        spa?.toastSuccess("Item removido!");
        app.emit("cart:updated");
    };

    /**
     * Limpa carrinho
     */
    app.clearCart = async () => {
        await db.clearCart();
        app.emit("cart:updated");
    };

    /**
     * Calcula totais do carrinho
     */
    app.getCartTotals = async () => {
        const cart = await db.getCartWithDetails();

        let totalCost = 0;
        let totalProfit = 0;

        for (const item of cart) {
            totalCost += item.subtotal || 0;
            totalProfit += item.profit || 0;
        }

        return {
            items: cart,
            itemsCount: cart.length,
            totalCost,
            totalProfit,
        };
    };

    // =================== PEDIDO ACTIONS ===================

    /**
     * Cria pedido a partir do carrinho
     */
    app.createOrderFromCart = async () => {
        const cart = await db.getCartWithDetails();
        if (cart.length === 0) {
            spa?.toastError("Carrinho vazio!");
            return null;
        }

        // Agrupa itens por fornecedor
        const bySupplier = {};
        for (const item of cart) {
            const supplierId = item.supplier_id;
            if (!bySupplier[supplierId]) {
                bySupplier[supplierId] = {
                    supplier_id: supplierId,
                    supplier: item.supplier,
                    items: [],
                    total_amount: 0,
                    total_profit: 0,
                };
            }
            bySupplier[supplierId].items.push(item);
            bySupplier[supplierId].total_amount += item.subtotal;
            bySupplier[supplierId].total_profit += item.profit || 0;
        }

        const orders = [];

        for (const supplierId in bySupplier) {
            const group = bySupplier[supplierId];

            const order = await db.saveOrder({
                supplier_id: parseInt(supplierId),
                status: "draft",
                total_amount: group.total_amount,
                total_profit: group.total_profit,
                generated_at: new Date().toISOString(),
            });

            // Salva itens
            for (const item of group.items) {
                await db.saveOrderItem({
                    order_id: order.id,
                    product_id: item.product_id,
                    quantity: item.quantity,
                    unit_cost_snapshot: item.unit_cost,
                    unit_sale_snapshot: item.product?.sale_price,
                    subtotal: item.subtotal,
                    profit_snapshot: item.profit,
                });
            }

            // Adiciona à fila de sync
            await queue.dispatch("create_order", {
                supplier_id: parseInt(supplierId),
                items: group.items.map((i) => ({
                    product_id: i.product_id,
                    quantity: i.quantity,
                    unit_cost_snapshot: i.unit_cost,
                    unit_sale_snapshot: i.product?.sale_price,
                })),
            });

            orders.push(order);
        }

        // Limpa carrinho
        await db.clearCart();

        spa?.toastSuccess(`${orders.length} pedido(s) criado(s)!`);
        app.emit("cart:updated");
        app.emit("orders:updated");

        return orders;
    };

    /**
     * Lista pedidos
     */
    app.getOrders = async () => {
        return db.getAllOrders();
    };

    /**
     * Obtém detalhes do pedido
     */
    app.getOrderDetails = async (orderId) => {
        return db.getOrderWithDetails(orderId);
    };

    /**
     * Clona pedido para carrinho (recompra)
     */
    app.cloneOrderToCart = async (orderId) => {
        const order = await db.getOrderWithDetails(orderId);
        if (!order) {
            spa?.toastError("Pedido não encontrado");
            return;
        }

        await db.clearCart();

        for (const item of order.items) {
            const bestQuote = await db.getBestQuote(item.product_id);

            await db.addToCart({
                product_id: item.product_id,
                supplier_id: bestQuote?.supplier_id || order.supplier_id,
                quantity: item.quantity,
                unit_cost: bestQuote?.cost_price || item.unit_cost_snapshot,
            });
        }

        spa?.toastSuccess("Pedido clonado para o carrinho!");
        app.emit("cart:updated");
    };

    /**
     * Gera texto do pedido para WhatsApp
     */
    app.generateWhatsappText = async (orderId) => {
        const order = await db.getOrderWithDetails(orderId);
        if (!order) return null;

        let text = `*PEDIDO DE COMPRA - ${
            order.supplier?.name || "Fornecedor"
        }*\n`;
        text += `Data: ${new Date().toLocaleDateString("pt-BR")}\n`;
        text += `----------------\n`;

        for (const item of order.items) {
            const subtotal = (item.quantity * item.unit_cost_snapshot).toFixed(
                2
            );
            text += `[${item.quantity}x] ${item.product?.name || "Produto"} (${
                item.product?.unit || "UN"
            }) - R$ ${subtotal}\n`;
        }

        text += `----------------\n`;
        text += `*TOTAL: R$ ${order.total_amount.toFixed(2)}*`;

        return text;
    };

    /**
     * Abre WhatsApp com pedido
     */
    app.sendOrderToWhatsapp = async (orderId) => {
        const order = await db.getOrderWithDetails(orderId);
        if (!order || !order.supplier?.whatsapp_link) {
            spa?.toastError("Fornecedor sem WhatsApp cadastrado");
            return;
        }

        const text = await app.generateWhatsappText(orderId);
        const url = `${order.supplier.whatsapp_link}?text=${encodeURIComponent(
            text
        )}`;

        window.open(url, "_blank");
    };

    // =================== BACKUP/RESTORE ACTIONS ===================

    /**
     * Exporta backup do banco
     */
    app.exportBackup = async () => {
        const backup = await db.exportBackup();
        const blob = new Blob([JSON.stringify(backup, null, 2)], {
            type: "application/json",
        });
        const url = URL.createObjectURL(blob);

        const a = document.createElement("a");
        a.href = url;
        a.download = `zepocket_backup_${
            new Date().toISOString().split("T")[0]
        }.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        spa?.toastSuccess("Backup exportado!");
    };

    /**
     * Importa backup
     */
    app.importBackup = async (file) => {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = async (e) => {
                try {
                    const data = JSON.parse(e.target.result);
                    await db.importBackup(data);
                    spa?.toastSuccess("Backup restaurado!");
                    app.emit("data:updated");
                    resolve(true);
                } catch (error) {
                    spa?.toastError("Arquivo de backup inválido");
                    reject(error);
                }
            };
            reader.onerror = () => reject(new Error("Erro ao ler arquivo"));
            reader.readAsText(file);
        });
    };

    // =================== SYNC ACTIONS ===================

    /**
     * Força sincronização completa
     */
    app.forceSync = async () => {
        const loadingId = spa?.toastLoading(
            "Sincronizando...",
            "Conectando ao servidor"
        );

        try {
            await sync.fullSync();

            if (loadingId) {
                spa?.updateToast(loadingId, {
                    type: "success",
                    title: "Sincronizado!",
                    description: "Dados atualizados com sucesso",
                    dismissible: true,
                    duration: 3000,
                });
            }

            app.emit("data:updated");
        } catch (error) {
            if (loadingId) {
                spa?.updateToast(loadingId, {
                    type: "error",
                    title: "Erro na sincronização",
                    description: error.message,
                    dismissible: true,
                });
            }
        }
    };

    console.log("⚡ ZePocket actions registradas");
}
