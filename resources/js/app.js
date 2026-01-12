import "./bootstrap";

import { SPA as App } from "./modules/spa";

// Importa módulos de páginas
import { initHomePage } from "./pages/home.js";
import { initAgendamentoPage } from "./pages/agendamento.js";
import { initChatPage } from "./pages/chat.js";
import { initConfiguracoesPage } from "./pages/configuracoes.js";
import { initConveniosPage } from "./pages/convenios.js";
import { initExamesListaPage } from "./pages/exames-lista.js";
import { initOrcamentosPage } from "./pages/orcamentos.js";
import { initResultadoPage } from "./pages/resultado.js";

// Importa lógica global e overlays
import { initGlobalLogic } from "./modules/global-logic.js";

// Importa handlers de sheets
import { initSheets } from "./sheets/index.js";

// Importa helpers
import { initLucide } from "./utils/helpers.js";
// Import Html5Qrcode so Vite bundles it and we can expose it to window for legacy inline scripts
import { Html5Qrcode } from "html5-qrcode";

// Inicializa a aplicação SPA
document.addEventListener("DOMContentLoaded", () => {
    window.app = new App();
    app.init({
        homePage: "home",
        animation: {
            type: "fade",
            speed: 0.35,
        },
        ui: {
            autoTheme: false, // Desabilita a detecção automática do tema do sistema
        },
        pwa: {
            enabled: true,
            showBanner: false, // Desabilita o banner automático para não conflitar com o LGPD
        },
    });

    // Inicializa módulos de página
    initHomePage(app);
    initAgendamentoPage(app);
    initChatPage(app);
    initConfiguracoesPage(app);
    initConveniosPage(app);
    initExamesListaPage(app);
    initOrcamentosPage(app);
    initResultadoPage(app);

    // Inicializa lógica global (overlays, tema, LGPD)
    initGlobalLogic(app);

    // Inicializa sheets
    initSheets(app);

    // Inicializa ícones Lucide
    initLucide();

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

    console.log("🚀 Lamarck SPA initialized");
});
