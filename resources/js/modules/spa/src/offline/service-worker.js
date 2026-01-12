/**
 * 🔄 SPA Framework - Service Worker Template
 * Template de Service Worker para cache e funcionamento offline
 */

const CACHE_NAME = "spa-cache-v1";
const DYNAMIC_CACHE = "spa-dynamic-v1";

// Assets para cache inicial (adicione seus arquivos aqui)
const STATIC_ASSETS = [
    "/",
    "/index.html",
    "/dist/spa.css",
    "/dist/spa.min.js",
    "/manifest.json",
    // Adicione mais assets conforme necessário
];

// Rotas para cache dinâmico (API, etc)
const DYNAMIC_ROUTES = ["/api/"];

// Estratégias de cache
const STRATEGIES = {
    CACHE_FIRST: "cache-first",
    NETWORK_FIRST: "network-first",
    STALE_WHILE_REVALIDATE: "stale-while-revalidate",
};

// Configuração de rotas
const ROUTE_CONFIG = {
    // Assets estáticos: cache first
    static: {
        match: (url) =>
            STATIC_ASSETS.some((asset) => url.pathname.endsWith(asset)),
        strategy: STRATEGIES.CACHE_FIRST,
    },
    // API: network first
    api: {
        match: (url) =>
            DYNAMIC_ROUTES.some((route) => url.pathname.startsWith(route)),
        strategy: STRATEGIES.NETWORK_FIRST,
    },
    // Imagens: stale while revalidate
    images: {
        match: (url) =>
            /\.(jpg|jpeg|png|gif|svg|webp|ico)$/i.test(url.pathname),
        strategy: STRATEGIES.STALE_WHILE_REVALIDATE,
    },
    // Fontes: cache first
    fonts: {
        match: (url) => /\.(woff|woff2|ttf|eot)$/i.test(url.pathname),
        strategy: STRATEGIES.CACHE_FIRST,
    },
};

// =================== INSTALL ===================

self.addEventListener("install", (event) => {
    console.log("🔧 Service Worker: Instalando...");

    event.waitUntil(
        caches
            .open(CACHE_NAME)
            .then((cache) => {
                console.log("📦 Cacheando assets estáticos...");
                return cache.addAll(STATIC_ASSETS);
            })
            .then(() => {
                console.log("✅ Service Worker: Instalado");
                return self.skipWaiting();
            })
            .catch((error) => {
                console.error("❌ Erro ao instalar SW:", error);
            })
    );
});

// =================== ACTIVATE ===================

self.addEventListener("activate", (event) => {
    console.log("🔧 Service Worker: Ativando...");

    event.waitUntil(
        caches
            .keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames
                        .filter(
                            (name) =>
                                name !== CACHE_NAME && name !== DYNAMIC_CACHE
                        )
                        .map((name) => {
                            console.log("🗑️ Removendo cache antigo:", name);
                            return caches.delete(name);
                        })
                );
            })
            .then(() => {
                console.log("✅ Service Worker: Ativado");
                return self.clients.claim();
            })
    );
});

// =================== FETCH ===================

self.addEventListener("fetch", (event) => {
    const url = new URL(event.request.url);

    // Ignora requisições de outras origens
    if (url.origin !== self.location.origin) {
        return;
    }

    // Ignora requisições de extensões
    if (url.protocol === "chrome-extension:") {
        return;
    }

    // Determina estratégia
    let strategy = STRATEGIES.NETWORK_FIRST;

    for (const [, config] of Object.entries(ROUTE_CONFIG)) {
        if (config.match(url)) {
            strategy = config.strategy;
            break;
        }
    }

    event.respondWith(handleFetch(event.request, strategy));
});

// =================== ESTRATÉGIAS DE CACHE ===================

async function handleFetch(request, strategy) {
    switch (strategy) {
        case STRATEGIES.CACHE_FIRST:
            return cacheFirst(request);
        case STRATEGIES.NETWORK_FIRST:
            return networkFirst(request);
        case STRATEGIES.STALE_WHILE_REVALIDATE:
            return staleWhileRevalidate(request);
        default:
            return fetch(request);
    }
}

/**
 * Cache First: Tenta cache, depois rede
 */
async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) {
        return cached;
    }

    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        return offlineFallback();
    }
}

/**
 * Network First: Tenta rede, depois cache
 */
async function networkFirst(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        const cached = await caches.match(request);
        if (cached) {
            return cached;
        }
        return offlineFallback();
    }
}

/**
 * Stale While Revalidate: Retorna cache e atualiza em background
 */
async function staleWhileRevalidate(request) {
    const cached = await caches.match(request);

    const networkPromise = fetch(request)
        .then((response) => {
            if (response.ok) {
                const cache = caches.open(DYNAMIC_CACHE);
                cache.then((c) => c.put(request, response.clone()));
            }
            return response;
        })
        .catch(() => null);

    return cached || networkPromise || offlineFallback();
}

/**
 * Fallback quando offline
 */
function offlineFallback() {
    return new Response(
        `
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Offline</title>
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    margin: 0;
                    background: #f5f5f5;
                    text-align: center;
                }
                .container {
                    padding: 2rem;
                }
                .icon {
                    font-size: 4rem;
                    margin-bottom: 1rem;
                }
                h1 {
                    color: #333;
                    margin-bottom: 0.5rem;
                }
                p {
                    color: #666;
                }
                button {
                    margin-top: 1rem;
                    padding: 0.75rem 1.5rem;
                    background: #333;
                    color: white;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="icon">📴</div>
                <h1>Você está offline</h1>
                <p>Verifique sua conexão e tente novamente.</p>
                <button onclick="location.reload()">Tentar novamente</button>
            </div>
        </body>
        </html>
        `,
        {
            status: 503,
            statusText: "Service Unavailable",
            headers: { "Content-Type": "text/html" },
        }
    );
}

// =================== SYNC ===================

self.addEventListener("sync", (event) => {
    console.log("🔄 Background Sync:", event.tag);

    if (event.tag === "spa-sync") {
        event.waitUntil(doSync());
    }
});

async function doSync() {
    // Notifica clientes para processar queue
    const clients = await self.clients.matchAll();
    clients.forEach((client) => {
        client.postMessage({
            type: "SYNC_TRIGGERED",
        });
    });
}

// =================== PUSH NOTIFICATIONS ===================

self.addEventListener("push", (event) => {
    console.log("🔔 Push recebido");

    let data = {
        title: "Notificação",
        body: "Você tem uma nova notificação",
        icon: "/img/icon-192.png",
        badge: "/img/badge.png",
    };

    if (event.data) {
        try {
            data = { ...data, ...event.data.json() };
        } catch (e) {
            data.body = event.data.text();
        }
    }

    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: data.icon,
            badge: data.badge,
            vibrate: [100, 50, 100],
            data: data.data || {},
            actions: data.actions || [],
        })
    );
});

self.addEventListener("notificationclick", (event) => {
    console.log("🔔 Notificação clicada");
    event.notification.close();

    const data = event.notification.data || {};
    const url = data.url || "/";

    event.waitUntil(
        clients.matchAll({ type: "window" }).then((clientList) => {
            // Se já tem janela aberta, foca nela
            for (const client of clientList) {
                if (client.url === url && "focus" in client) {
                    return client.focus();
                }
            }
            // Senão, abre nova janela
            return clients.openWindow(url);
        })
    );
});

// =================== MESSAGE ===================

self.addEventListener("message", (event) => {
    console.log("📩 Mensagem recebida:", event.data);

    const { type, payload } = event.data;

    switch (type) {
        case "SKIP_WAITING":
            self.skipWaiting();
            break;

        case "CACHE_URLS":
            caches.open(CACHE_NAME).then((cache) => {
                cache.addAll(payload.urls);
            });
            break;

        case "CLEAR_CACHE":
            caches.delete(CACHE_NAME);
            caches.delete(DYNAMIC_CACHE);
            break;

        case "GET_VERSION":
            event.ports[0].postMessage({ version: CACHE_NAME });
            break;
    }
});

console.log("🚀 Service Worker carregado");
