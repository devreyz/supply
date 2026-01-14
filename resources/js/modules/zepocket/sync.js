/**
 * 🔄 ZePocket - Sync Module
 * Sincronização de dados entre IndexedDB e servidor Laravel
 */

/**
 * Classe de sincronização
 */
export class ZePocketSync {
    constructor(db, queue, options = {}) {
        this.db = db;
        this.queue = queue;
        this.baseUrl = options.baseUrl || "/api";
        this.csrfToken =
            options.csrfToken ||
            document.querySelector('meta[name="csrf-token"]')?.content;
        this.onSyncStart = options.onSyncStart || (() => {});
        this.onSyncComplete = options.onSyncComplete || (() => {});
        this.onSyncError = options.onSyncError || (() => {});
        this.onProgress = options.onProgress || (() => {});
    }

    /**
     * Headers padrão para requisições
     */
    get headers() {
        return {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": this.csrfToken,
            "X-Requested-With": "XMLHttpRequest",
        };
    }

    /**
     * Requisição HTTP genérica
     */
    async request(method, endpoint, data = null) {
        const url = `${this.baseUrl}${endpoint}`;
        const options = {
            method,
            headers: this.headers,
            credentials: "same-origin",
        };

        if (
            data &&
            (method === "POST" || method === "PUT" || method === "PATCH")
        ) {
            options.body = JSON.stringify(data);
        }

        const response = await fetch(url, options);

        if (!response.ok) {
            const error = await response
                .json()
                .catch(() => ({ message: "Erro de rede" }));
            throw new Error(error.message || `HTTP ${response.status}`);
        }

        return response.json();
    }

    /**
     * Verifica se há atualizações no servidor
     */
    async checkForUpdates() {
        const lastSync = await this.db.getLastSync();

        try {
            const result = await this.request(
                "GET",
                `/sync/check?last_sync=${lastSync || ""}`
            );
            return result.needs_update;
        } catch (error) {
            console.warn("📡 Erro ao verificar atualizações:", error);
            return false;
        }
    }

    /**
     * Baixa todos os dados do servidor
     */
    async pull() {
        if (!navigator.onLine) {
            console.log("📡 Offline - pulando sincronização");
            return false;
        }

        this.onSyncStart();

        try {
            this.onProgress(10, "Conectando ao servidor...");

            const data = await this.request("GET", "/sync/pull");

            this.onProgress(30, "Salvando produtos...");
            if (data.products?.length) {
                await this.db.bulkSaveProducts(data.products);
            }

            this.onProgress(50, "Salvando fornecedores...");
            if (data.suppliers?.length) {
                await this.db.bulkSaveSuppliers(data.suppliers);
            }

            this.onProgress(70, "Salvando cotações...");
            if (data.quotes?.length) {
                await this.db.bulkSaveQuotes(data.quotes);
            }

            this.onProgress(90, "Salvando pedidos...");
            if (data.orders?.length) {
                await this.db.bulkSaveOrders(data.orders);
            }

            // Salva timestamp
            await this.db.setLastSync(data.sync_timestamp);

            this.onProgress(100, "Sincronização completa!");
            this.onSyncComplete(data);

            console.log("✅ Sincronização concluída:", {
                products: data.products?.length || 0,
                suppliers: data.suppliers?.length || 0,
                quotes: data.quotes?.length || 0,
                orders: data.orders?.length || 0,
            });

            return true;
        } catch (error) {
            console.error("❌ Erro na sincronização:", error);
            this.onSyncError(error);
            return false;
        }
    }

    /**
     * Envia alterações locais para o servidor
     */
    async push(changes) {
        if (!navigator.onLine) {
            // Se offline, adiciona à fila
            for (const change of changes) {
                await this.queue.dispatch("sync_change", change, {
                    unique: true,
                });
            }
            return { queued: true };
        }

        try {
            const result = await this.request("POST", "/sync/push", {
                changes,
            });
            return result;
        } catch (error) {
            // Em caso de erro, adiciona à fila
            for (const change of changes) {
                await this.queue.dispatch("sync_change", change, {
                    unique: true,
                });
            }
            throw error;
        }
    }

    /**
     * Sincronização completa (pull + push pending)
     */
    async fullSync() {
        // Primeiro processa jobs pendentes
        await this.queue.processAll();

        // Depois faz pull
        const hasUpdates = await this.checkForUpdates();
        if (hasUpdates) {
            await this.pull();
        }

        return hasUpdates;
    }

    /**
     * Sincronização rápida (apenas verifica e baixa se necessário)
     */
    async quickSync() {
        const needsUpdate = await this.checkForUpdates();
        if (needsUpdate) {
            return this.pull();
        }
        return false;
    }
}

/**
 * Registra actions de sincronização na fila
 */
export function registerSyncActions(queue, sync) {
    // Action para sincronizar uma mudança individual
    queue.defineAction("sync_change", async (job) => {
        const change = job.data;
        const result = await sync.request("POST", "/sync/push", {
            changes: [change],
        });
        return result;
    });

    // Action para criar produto
    queue.defineAction("create_product", async (job) => {
        const result = await sync.request("POST", "/products", job.data);
        return result;
    });

    // Action para criar fornecedor
    queue.defineAction("create_supplier", async (job) => {
        const result = await sync.request("POST", "/suppliers", job.data);
        return result;
    });

    // Action para salvar cotação
    queue.defineAction("save_quote", async (job) => {
        const result = await sync.request("POST", "/quotes", job.data);
        return result;
    });

    // Action para criar pedido
    queue.defineAction("create_order", async (job) => {
        const result = await sync.request("POST", "/orders", job.data);
        return result;
    });

    // Action para atualizar pedido
    queue.defineAction("update_order", async (job) => {
        const { id, ...data } = job.data;
        const result = await sync.request("PUT", `/orders/${id}`, data);
        return result;
    });

    console.log("📋 Sync actions registradas");
}
