/**
 * 💾 ZePocket - Database Configuration
 * Configuração do IndexedDB ORM para o módulo ZePocket
 */

import { IndexedDBORM } from "../spa/src/storage/indexeddb.js";

/**
 * Inicializa e configura o banco de dados IndexedDB do ZePocket
 */
export async function initDatabase() {
    const db = new IndexedDBORM("zepocket", 2);

    // Tabela de Produtos
    await db.defineTable("products", {
        keyPath: "id",
        autoIncrement: false,
        timestamps: true,
        indexes: [
            { name: "name", keyPath: "name" },
            { name: "brand", keyPath: "brand" },
            { name: "ean", keyPath: "ean", unique: true },
            { name: "is_global", keyPath: "is_global" },
        ],
    });

    // Tabela de Fornecedores
    await db.defineTable("suppliers", {
        keyPath: "id",
        autoIncrement: false,
        timestamps: true,
        indexes: [
            { name: "name", keyPath: "name" },
            { name: "is_active", keyPath: "is_active" },
        ],
    });

    // Tabela de Cotações (Preços de Custo)
    await db.defineTable("quotes", {
        keyPath: "id",
        autoIncrement: false,
        timestamps: true,
        indexes: [
            { name: "product_id", keyPath: "product_id" },
            { name: "supplier_id", keyPath: "supplier_id" },
            { name: "cost_price", keyPath: "cost_price" },
        ],
    });

    // Tabela de Pedidos
    await db.defineTable("orders", {
        keyPath: "id",
        autoIncrement: true, // IDs locais podem ser auto-increment
        timestamps: true,
        indexes: [
            { name: "supplier_id", keyPath: "supplier_id" },
            { name: "status", keyPath: "status" },
            { name: "server_id", keyPath: "server_id" },
        ],
    });

    // Tabela de Itens do Pedido
    await db.defineTable("order_items", {
        keyPath: "id",
        autoIncrement: true,
        timestamps: true,
        indexes: [
            { name: "order_id", keyPath: "order_id" },
            { name: "product_id", keyPath: "product_id" },
        ],
    });

    // Tabela de Carrinho (temporário, local)
    await db.defineTable("cart", {
        keyPath: "product_id",
        autoIncrement: false,
        timestamps: false,
        indexes: [{ name: "supplier_id", keyPath: "supplier_id" }],
    });

    // Tabela de Configurações do Usuário
    await db.defineTable("settings", {
        keyPath: "key",
        autoIncrement: false,
        timestamps: false,
    });

    // Inicializa o banco
    await db.init();

    console.log("💾 ZePocket Database inicializado");

    return db;
}

/**
 * Classe de acesso rápido ao banco
 */
export class ZePocketDB {
    constructor(db) {
        this.db = db;
    }

    // =================== PRODUTOS ===================

    async getAllProducts() {
        return this.db.table("products").all();
    }

    async searchProducts(term) {
        const termLower = term.toLowerCase();
        return this.db
            .table("products")
            .where(
                (p) =>
                    p.name?.toLowerCase().includes(termLower) ||
                    p.brand?.toLowerCase().includes(termLower) ||
                    p.ean?.includes(term)
            )
            .all();
    }

    async getProduct(id) {
        return this.db.table("products").find(id);
    }

    async saveProduct(product) {
        const existing = await this.db.table("products").find(product.id);
        if (existing) {
            return this.db.table("products").update(product.id, product);
        }
        return this.db.table("products").insert(product);
    }

    async bulkSaveProducts(products) {
        for (const product of products) {
            await this.saveProduct(product);
        }
    }

    // =================== FORNECEDORES ===================

    async getAllSuppliers(activeOnly = false) {
        let query = this.db.table("suppliers");
        if (activeOnly) {
            query = query.where("is_active", true);
        }
        return query.all();
    }

    async getSupplier(id) {
        return this.db.table("suppliers").find(id);
    }

    async saveSupplier(supplier) {
        const existing = await this.db.table("suppliers").find(supplier.id);
        if (existing) {
            return this.db.table("suppliers").update(supplier.id, supplier);
        }
        return this.db.table("suppliers").insert(supplier);
    }

    async deleteSupplier(id) {
        return this.db.table("suppliers").delete(id);
    }

    async bulkSaveSuppliers(suppliers) {
        for (const supplier of suppliers) {
            await this.saveSupplier(supplier);
        }
    }

    // =================== COTAÇÕES ===================

    async getAllQuotes() {
        return this.db.table("quotes").all();
    }

    async getQuotesForProduct(productId) {
        return this.db
            .table("quotes")
            .where("product_id", productId)
            .orderBy("cost_price", "asc")
            .all();
    }

    async getQuotesForSupplier(supplierId) {
        return this.db.table("quotes").where("supplier_id", supplierId).all();
    }

    async getBestQuote(productId) {
        const quotes = await this.getQuotesForProduct(productId);
        return quotes[0] || null;
    }

    async getQuote(productId, supplierId) {
        return this.db
            .table("quotes")
            .where(
                (q) =>
                    q.product_id === productId && q.supplier_id === supplierId
            )
            .first();
    }

    async saveQuote(quote) {
        // Verifica se já existe cotação para produto/fornecedor
        const existing = await this.getQuote(
            quote.product_id,
            quote.supplier_id
        );
        if (existing) {
            return this.db.table("quotes").update(existing.id, {
                ...quote,
                id: existing.id,
                previous_price: existing.cost_price,
            });
        }
        return this.db.table("quotes").insert(quote);
    }

    async bulkSaveQuotes(quotes) {
        for (const quote of quotes) {
            await this.saveQuote(quote);
        }
    }

    // =================== CARRINHO ===================

    async getCart() {
        return this.db.table("cart").all();
    }

    async addToCart(item) {
        // Item: { product_id, supplier_id, quantity, unit_cost }
        const existing = await this.db.table("cart").find(item.product_id);
        if (existing) {
            return this.db.table("cart").update(item.product_id, {
                ...existing,
                quantity: item.quantity || existing.quantity,
                supplier_id: item.supplier_id || existing.supplier_id,
                unit_cost: item.unit_cost || existing.unit_cost,
            });
        }
        return this.db.table("cart").insert(item);
    }

    async updateCartItem(productId, data) {
        return this.db.table("cart").update(productId, data);
    }

    async removeFromCart(productId) {
        return this.db.table("cart").delete(productId);
    }

    async clearCart() {
        const items = await this.getCart();
        for (const item of items) {
            await this.removeFromCart(item.product_id);
        }
    }

    async getCartWithDetails() {
        const cart = await this.getCart();
        const details = [];

        for (const item of cart) {
            const product = await this.getProduct(item.product_id);
            const supplier = await this.getSupplier(item.supplier_id);

            details.push({
                ...item,
                product,
                supplier,
                subtotal: item.quantity * item.unit_cost,
                profit: product?.sale_price
                    ? (product.sale_price - item.unit_cost) * item.quantity
                    : null,
            });
        }

        return details;
    }

    // =================== PEDIDOS ===================

    async getAllOrders() {
        return this.db.table("orders").orderBy("createdAt", "desc").all();
    }

    async getOrder(id) {
        return this.db.table("orders").find(id);
    }

    async saveOrder(order) {
        if (order.id) {
            return this.db.table("orders").update(order.id, order);
        }
        return this.db.table("orders").insert(order);
    }

    async getOrderItems(orderId) {
        return this.db.table("order_items").where("order_id", orderId).all();
    }

    async saveOrderItem(item) {
        if (item.id) {
            return this.db.table("order_items").update(item.id, item);
        }
        return this.db.table("order_items").insert(item);
    }

    async getOrderWithDetails(orderId) {
        const order = await this.getOrder(orderId);
        if (!order) return null;

        const items = await this.getOrderItems(orderId);
        const supplier = await this.getSupplier(order.supplier_id);

        const itemsWithProducts = [];
        for (const item of items) {
            const product = await this.getProduct(item.product_id);
            itemsWithProducts.push({ ...item, product });
        }

        return {
            ...order,
            supplier,
            items: itemsWithProducts,
        };
    }

    async bulkSaveOrders(orders) {
        for (const order of orders) {
            await this.saveOrder(order);
            if (order.items) {
                for (const item of order.items) {
                    await this.saveOrderItem({ ...item, order_id: order.id });
                }
            }
        }
    }

    // =================== SETTINGS ===================

    async getSetting(key, defaultValue = null) {
        const setting = await this.db.table("settings").find(key);
        return setting?.value ?? defaultValue;
    }

    async setSetting(key, value) {
        const existing = await this.db.table("settings").find(key);
        if (existing) {
            return this.db.table("settings").update(key, { key, value });
        }
        return this.db.table("settings").insert({ key, value });
    }

    // =================== SYNC HELPERS ===================

    async getLastSync() {
        return this.getSetting("last_sync");
    }

    async setLastSync(timestamp) {
        return this.setSetting("last_sync", timestamp);
    }

    async clearAll() {
        await this.db.table("products").clear();
        await this.db.table("suppliers").clear();
        await this.db.table("quotes").clear();
        await this.db.table("orders").clear();
        await this.db.table("order_items").clear();
        await this.db.table("cart").clear();
    }

    // =================== EXPORT/IMPORT ===================

    async exportBackup() {
        return {
            products: await this.getAllProducts(),
            suppliers: await this.getAllSuppliers(),
            quotes: await this.getAllQuotes(),
            orders: await this.getAllOrders(),
            cart: await this.getCart(),
            settings: await this.db.table("settings").all(),
            exportedAt: new Date().toISOString(),
        };
    }

    async importBackup(data) {
        if (data.products) await this.bulkSaveProducts(data.products);
        if (data.suppliers) await this.bulkSaveSuppliers(data.suppliers);
        if (data.quotes) await this.bulkSaveQuotes(data.quotes);
        if (data.orders) await this.bulkSaveOrders(data.orders);
        if (data.cart) {
            for (const item of data.cart) {
                await this.addToCart(item);
            }
        }
    }
}
