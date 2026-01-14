/**
 * 📦 ZePocket - Main App
 * Classe principal do módulo ZePocket
 */

import { initDatabase, ZePocketDB } from "./db.js";
import { ZePocketSync, registerSyncActions } from "./sync.js";
import { registerActions } from "./actions.js";
import { JobQueue } from "../spa/src/offline/queue.js";

/**
 * Classe principal do ZePocket
 */
export class ZePocket {
    constructor(options = {}) {
        this.options = {
            autoSync: true,
            syncInterval: 60000, // 1 minuto
            ...options,
        };

        this.spa = options.spa || null;
        this.db = null;
        this.queue = null;
        this.sync = null;
        this._listeners = new Map();
        this._initialized = false;
    }

    /**
     * Inicializa o ZePocket
     */
    async init() {
        if (this._initialized) return this;

        console.log("🚀 Inicializando ZePocket...");

        try {
            // Inicializa banco de dados
            const orm = await initDatabase();
            this.db = new ZePocketDB(orm);

            // Inicializa fila de jobs
            this.queue = new JobQueue(orm);
            await this.queue.init();

            // Inicializa sincronização
            this.sync = new ZePocketSync(this.db, this.queue, {
                baseUrl: "/api",
                onSyncStart: () => this.emit("sync:start"),
                onSyncComplete: (data) => this.emit("sync:complete", data),
                onSyncError: (error) => this.emit("sync:error", error),
                onProgress: (percent, message) =>
                    this.emit("sync:progress", { percent, message }),
            });

            // Registra actions de sync
            registerSyncActions(this.queue, this.sync);

            // Registra actions de negócio
            registerActions(this);

            // Configura sync automático
            if (this.options.autoSync) {
                this._setupAutoSync();
            }

            // Listeners de conectividade
            this._setupConnectivityListeners();

            this._initialized = true;
            console.log("✅ ZePocket inicializado");

            // Sync inicial se online
            if (navigator.onLine) {
                setTimeout(() => this.sync.quickSync(), 1000);
            }

            return this;
        } catch (error) {
            console.error("❌ Erro ao inicializar ZePocket:", error);
            throw error;
        }
    }

    /**
     * Configura sincronização automática
     */
    _setupAutoSync() {
        // Sync periódico
        setInterval(async () => {
            if (navigator.onLine && document.visibilityState === "visible") {
                await this.sync.quickSync();
            }
        }, this.options.syncInterval);

        // Sync quando volta online
        window.addEventListener("online", async () => {
            console.log("🌐 Online - sincronizando...");
            await this.sync.fullSync();
            this.emit("online");
        });
    }

    /**
     * Configura listeners de conectividade
     */
    _setupConnectivityListeners() {
        window.addEventListener("offline", () => {
            console.log("📴 Offline");
            this.spa?.toastWarning(
                "Modo Offline",
                "Alterações serão sincronizadas quando voltar online"
            );
            this.emit("offline");
        });

        // Sync quando a aba volta ao foco
        document.addEventListener("visibilitychange", async () => {
            if (document.visibilityState === "visible" && navigator.onLine) {
                await this.sync.quickSync();
            }
        });
    }

    /**
     * Adiciona listener de evento
     */
    on(event, callback) {
        if (!this._listeners.has(event)) {
            this._listeners.set(event, new Set());
        }
        this._listeners.get(event).add(callback);
        return () => this._listeners.get(event).delete(callback);
    }

    /**
     * Remove listener de evento
     */
    off(event, callback) {
        if (this._listeners.has(event)) {
            this._listeners.get(event).delete(callback);
        }
    }

    /**
     * Emite evento
     */
    emit(event, data = null) {
        if (this._listeners.has(event)) {
            this._listeners.get(event).forEach((callback) => {
                try {
                    callback(data);
                } catch (e) {
                    console.error(`Erro no listener ${event}:`, e);
                }
            });
        }
    }

    /**
     * Status do sistema
     */
    getStatus() {
        return {
            initialized: this._initialized,
            online: navigator.onLine,
            pendingJobs: this.queue?.pending()?.length || 0,
        };
    }
}

// Export singleton factory
let instance = null;

export function createZePocket(options = {}) {
    if (!instance) {
        instance = new ZePocket(options);
    }
    return instance;
}

export function getZePocket() {
    return instance;
}

// Export para uso global
if (typeof window !== "undefined") {
    window.ZePocket = ZePocket;
    window.createZePocket = createZePocket;
    window.getZePocket = getZePocket;
}
