/**
 * 🔔 SPA Framework - Notifications
 * Gerenciador de notificações push
 */

/**
 * Gerenciador de notificações
 */
export class NotificationManager {
    // constructor aceita tanto (options) quanto (spaInstance)
    constructor(spaOrOptions = {}) {
        if (spaOrOptions && spaOrOptions._registerOverlay) {
            this.spa = spaOrOptions;
            this.options = {
                vapidKey: null,
                defaultIcon: "/img/icon-192.png",
                defaultBadge: "/img/badge.png",
            };
        } else {
            this.spa = null;
            this.options = {
                vapidKey: null,
                defaultIcon: "/img/icon-192.png",
                defaultBadge: "/img/badge.png",
                ...spaOrOptions,
            };
        }

        this.permission = Notification.permission;
        this.subscription = null;
        this.db = null; // referência ao IndexedDBORM quando disponível
    }

    /**
     * Verifica se notificações são suportadas
     */
    isSupported() {
        return "Notification" in window && "serviceWorker" in navigator;
    }

    /**
     * Verifica se está habilitado
     */
    isEnabled() {
        return this.permission === "granted";
    }

    /**
     * Verifica se foi negado
     */
    isDenied() {
        return this.permission === "denied";
    }

    /**
     * Solicita permissão
     */
    async request() {
        if (!this.isSupported()) {
            console.warn("🔔 Notificações não suportadas");
            return false;
        }

        if (this.isEnabled()) {
            return true;
        }

        if (this.isDenied()) {
            console.warn("🔔 Notificações foram bloqueadas pelo usuário");
            return false;
        }

        try {
            const result = await Notification.requestPermission();
            this.permission = result;

            if (result === "granted") {
                console.log("🔔 Permissão concedida");
                this._emit("notifications:granted");
                return true;
            } else {
                console.log("🔔 Permissão negada");
                this._emit("notifications:denied");
                return false;
            }
        } catch (error) {
            console.error("🔔 Erro ao solicitar permissão:", error);
            return false;
        }
    }

    /**
     * Inicializa integração com IndexedDB (cria store `notifications`)
     */
    async init() {
        try {
            if (!this.spa) return;
            // aguarda que o SPA tenha inicializado o db
            this.db = this.spa.db;
            if (!this.db) return;

            await this.db.defineTable("notifications", {
                keyPath: "id",
                autoIncrement: true,
                indexes: [
                    { name: "tag", keyPath: "tag" },
                    { name: "read", keyPath: "read" },
                    { name: "createdAt", keyPath: "createdAt" },
                ],
            });

            if (this.spa && typeof this.spa._log === "function") {
                this.spa._log(
                    2,
                    "🔔 NotificationManager: tabela `notifications` pronta"
                );
            }
        } catch (e) {
            console.error(
                "🔔 Erro ao inicializar NotificationManager (DB):",
                e
            );
        }
    }

    /**
     * Armazena uma notificação localmente
     */
    async store(payload = {}) {
        if (!this.db) return null;
        const record = {
            title: payload.title || "",
            body: payload.body || "",
            tag: payload.tag || null,
            data: payload.data || null,
            icon: payload.icon || this.options.defaultIcon,
            read: payload.read ? true : false,
            createdAt: new Date().toISOString(),
            updatedAt: new Date().toISOString(),
        };
        const res = await this.db.table("notifications").insert(record);
        // notifica UI sobre mudança
        document.dispatchEvent(new CustomEvent("notifications:changed"));
        return res;
    }

    async list() {
        if (!this.db) return [];
        const all = await this.db.table("notifications").all();
        return all.sort(
            (a, b) => new Date(b.createdAt) - new Date(a.createdAt)
        );
    }

    async markRead(id) {
        if (!this.db) return null;
        const rec = await this.db.table("notifications").find(id);
        if (!rec) return null;
        rec.read = true;
        rec.updatedAt = new Date().toISOString();
        const res = await this.db.table("notifications").upsert(rec);
        document.dispatchEvent(new CustomEvent("notifications:changed"));
        return res;
    }

    async remove(id) {
        if (!this.db) return null;
        const res = await this.db.table("notifications").delete(id);
        document.dispatchEvent(new CustomEvent("notifications:changed"));
        return res;
    }

    /**
     * Mostra notificação local
     * @param {string} title - Título
     * @param {Object} options - Opções da notificação
     */
    async show(title, options = {}) {
        if (!this.isEnabled()) {
            const granted = await this.request();
            if (!granted) return null;
        }

        const notifOptions = {
            icon: options.icon || this.options.defaultIcon,
            badge: options.badge || this.options.defaultBadge,
            body: options.body || "",
            requireInteraction: options.requireInteraction || false,
            silent: options.silent || false,
            vibrate: options.vibrate || [100, 50, 100],
            data: options.data || {},
            actions: options.actions || [],
        };

        // Só adiciona tag se for explicitamente fornecida
        // Isso evita que uma notificação substitua a outra automaticamente
        if (options.tag) {
            notifOptions.tag = options.tag;
        }

        try {
            // Usa Service Worker se disponível para persistência
            const registration = await navigator.serviceWorker?.ready;

            if (registration) {
                await registration.showNotification(title, notifOptions);
            } else {
                new Notification(title, notifOptions);
            }

            console.log("🔔 Notificação mostrada:", title);
            return true;
        } catch (error) {
            console.error("🔔 Erro ao mostrar notificação:", error);
            return false;
        }
    }

    /**
     * Atalho para notificação de sucesso
     */
    success(title, body = "", options = {}) {
        return this.show(title, { body, ...options });
    }

    /**
     * Atalho para notificação de erro
     */
    error(title, body = "", options = {}) {
        return this.show(title, { body, ...options });
    }

    /**
     * Atalho para notificação de info
     */
    info(title, body = "", options = {}) {
        return this.show(title, { body, ...options });
    }

    /**
     * Assina para push notifications
     */
    async subscribePush() {
        if (!this.options.vapidKey) {
            console.warn("🔔 VAPID key não configurada");
            return null;
        }

        if (!this.isEnabled()) {
            const granted = await this.request();
            if (!granted) return null;
        }

        try {
            const registration = await navigator.serviceWorker.ready;

            // Verifica subscription existente
            let subscription = await registration.pushManager.getSubscription();

            if (!subscription) {
                // Cria nova subscription
                subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this._urlBase64ToUint8Array(
                        this.options.vapidKey
                    ),
                });
            }

            this.subscription = subscription;
            console.log("🔔 Push subscription:", subscription);

            this._emit("push:subscribed", subscription);
            return subscription;
        } catch (error) {
            console.error("🔔 Erro ao assinar push:", error);
            return null;
        }
    }

    /**
     * Cancela subscription
     */
    async unsubscribePush() {
        if (!this.subscription) {
            const registration = await navigator.serviceWorker?.ready;
            this.subscription =
                await registration?.pushManager.getSubscription();
        }

        if (this.subscription) {
            await this.subscription.unsubscribe();
            this.subscription = null;
            console.log("🔔 Push subscription cancelada");
            this._emit("push:unsubscribed");
            return true;
        }

        return false;
    }

    /**
     * Obtém subscription atual
     */
    async getSubscription() {
        const registration = await navigator.serviceWorker?.ready;
        return registration?.pushManager.getSubscription();
    }

    /**
     * Fecha todas as notificações
     */
    async closeAll() {
        const registration = await navigator.serviceWorker?.ready;
        if (!registration) return;

        const notifications = await registration.getNotifications();
        notifications.forEach((notification) => notification.close());
    }

    /**
     * Fecha notificação por tag
     */
    async closeByTag(tag) {
        const registration = await navigator.serviceWorker?.ready;
        if (!registration) return;

        const notifications = await registration.getNotifications({ tag });
        notifications.forEach((notification) => notification.close());
    }

    /**
     * Converte VAPID key de base64 para Uint8Array
     */
    _urlBase64ToUint8Array(base64String) {
        const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, "+")
            .replace(/_/g, "/");

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }

        return outputArray;
    }

    /**
     * Emite evento
     */
    _emit(event, data = null) {
        document.dispatchEvent(new CustomEvent(event, { detail: data }));
    }
}

// Compatibilidade com uso global (opcional)
if (typeof window !== "undefined") {
    window.NotificationManager = NotificationManager;
}
