/**
 * SPA Framework - Service Worker para Produção
 * Versão simplificada pronta para uso
 */

const CACHE_NAME = "spa-framework-v1.0.0";
const OFFLINE_URL = "/spa-framework/examples/basic/index.html";

// Assets para cache inicial
const PRECACHE_ASSETS = [
    "/spa-framework/examples/basic/index.html",
    "/spa-framework/dist/spa.css",
    "/spa-framework/src/core/spa.js",
    "/spa-framework/src/ui/modals.js",
    "/spa-framework/src/storage/indexeddb.js",
    "/spa-framework/src/storage/localstorage.js",
    "/spa-framework/src/offline/queue.js",
    "/spa-framework/src/pwa/install.js",
    "/spa-framework/src/pwa/notifications.js",
];

// Instalação - Pre-cache de assets
self.addEventListener("install", (event) => {
    console.log("[SW] Instalando...");

    event.waitUntil(
        caches
            .open(CACHE_NAME)
            .then((cache) => {
                console.log("[SW] Pre-caching assets");
                return cache.addAll(PRECACHE_ASSETS);
            })
            .then(() => self.skipWaiting())
    );
});

// Ativação - Limpa caches antigos
self.addEventListener("activate", (event) => {
    console.log("[SW] Ativando...");

    event.waitUntil(
        caches
            .keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames
                        .filter((name) => name !== CACHE_NAME)
                        .map((name) => {
                            console.log("[SW] Removendo cache antigo:", name);
                            return caches.delete(name);
                        })
                );
            })
            .then(() => self.clients.claim())
    );
});

// Estratégia de fetch
self.addEventListener("fetch", (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Ignora requests não-GET
    if (request.method !== "GET") return;

    // Ignora requests externos
    if (url.origin !== location.origin) return;

    // Ignora requests de API
    if (url.pathname.startsWith("/api/")) return;

    // Estratégia: Stale-While-Revalidate para assets estáticos
    if (isStaticAsset(url.pathname)) {
        event.respondWith(staleWhileRevalidate(request));
        return;
    }

    // Estratégia: Network-First para páginas HTML
    if (request.headers.get("accept")?.includes("text/html")) {
        event.respondWith(networkFirst(request));
        return;
    }

    // Estratégia padrão: Cache-First
    event.respondWith(cacheFirst(request));
});

// Verifica se é asset estático
function isStaticAsset(pathname) {
    return /\.(js|css|png|jpg|jpeg|gif|svg|woff2?|ttf|eot|ico)$/.test(pathname);
}

// Estratégia: Cache-First
async function cacheFirst(request) {
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }

    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            try {
                const cache = await caches.open(CACHE_NAME);
                cache.put(request, networkResponse.clone());
            } catch (e) {
                console.warn(
                    "[SW] Não foi possível armazenar no cache (cache.put):",
                    e
                );
            }
        }
        return networkResponse;
    } catch (error) {
        console.log("[SW] Fetch falhou, sem cache:", request.url);
        throw error;
    }
}

// Estratégia: Network-First
async function networkFirst(request) {
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            try {
                const cache = await caches.open(CACHE_NAME);
                cache.put(request, networkResponse.clone());
            } catch (e) {
                console.warn(
                    "[SW] Não foi possível armazenar no cache (cache.put):",
                    e
                );
            }
        }
        return networkResponse;
    } catch (error) {
        console.log("[SW] Network falhou, usando cache:", request.url);
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        // Retorna página offline se disponível
        return caches.match(OFFLINE_URL);
    }
}

// Estratégia: Stale-While-Revalidate
async function staleWhileRevalidate(request) {
    const cachedResponse = await caches.match(request);

    const fetchPromise = fetch(request)
        .then((networkResponse) => {
            if (networkResponse.ok) {
                caches.open(CACHE_NAME).then((cache) => {
                    try {
                        cache.put(request, networkResponse.clone());
                    } catch (e) {
                        console.warn(
                            "[SW] Não foi possível armazenar no cache (cache.put):",
                            e
                        );
                    }
                });
            }
            return networkResponse;
        })
        .catch(() => cachedResponse);

    return cachedResponse || fetchPromise;
}

// Recebe mensagens do cliente
self.addEventListener("message", (event) => {
    if (event.data.action === "skipWaiting") {
        self.skipWaiting();
    }

    if (event.data.action === "clearCache") {
        caches.delete(CACHE_NAME).then(() => {
            console.log("[SW] Cache limpo");
            event.ports[0].postMessage({ success: true });
        });
    }
});

// Notificações Push
self.addEventListener("push", (event) => {
    if (!event.data) return;

    let data;
    try {
        data = event.data.json();
    } catch (e) {
        data = {
            title: "Nova Notificação",
            body: event.data.text(),
        };
    }

    const options = {
        body: data.body || "",
        icon: data.icon || "/spa-framework/img/icon-192.png",
        badge: data.badge || "/spa-framework/img/icon-72.png",
        tag: data.tag || "default",
        data: data.data || {},
        actions: data.actions || [],
    };

    event.waitUntil(
        self.registration.showNotification(
            data.title || "SPA Framework",
            options
        )
    );
});

// Click em notificação
self.addEventListener("notificationclick", (event) => {
    event.notification.close();

    const urlToOpen =
        event.notification.data?.url || "/spa-framework/examples/basic/";

    event.waitUntil(
        clients
            .matchAll({ type: "window", includeUncontrolled: true })
            .then((clientList) => {
                // Se já tem janela aberta, foca nela
                for (const client of clientList) {
                    if (
                        client.url.includes("/spa-framework/") &&
                        "focus" in client
                    ) {
                        return client.focus();
                    }
                }
                // Senão, abre nova janela
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

console.log("[SW] Service Worker carregado");
