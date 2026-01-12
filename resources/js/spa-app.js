/**
 * 🚀 Lamarck SPA - Main Entry Point
 * Inicializa o SPA Framework e registra todos os módulos de página
 *
 * @version 1.0.0
 */

// Core SPA
import SPA from "../modules/spa/index.js";

// Pages
import { initHomePage } from "./pages/home.js";
import { initAgendamentoPage } from "./pages/agendamento.js";
import { initChatPage } from "./pages/chat.js";
import { initConfiguracoesPage } from "./pages/configuracoes.js";
import { initConveniosPage } from "./pages/convenios.js";
import { initExamesListaPage } from "./pages/exames-lista.js";
import { initOrcamentosPage } from "./pages/orcamentos.js";
import { initResultadoPage } from "./pages/resultado.js";

// Sheets
import { initSheets } from "./sheets/index.js";

// Utils
import {
    initLucide,
    formatPhone,
    formatCPF,
    formatDate,
} from "./utils/helpers.js";

/**
 * Configuração global do SPA
 */
const SPA_CONFIG = {
    homePage: "home",
    pagePrefix: "page-",
    useHistory: true,
    scrollToTop: true,

    animation: {
        type: "fade",
        speed: 0.3,
        backSpeed: 0.15,
        easing: "cubic-bezier(0.25, 0.46, 0.45, 0.94)",
    },

    gestures: {
        enabled: true,
        swipeBack: true,
        swipeThreshold: 50,
        edgeWidth: 30,
    },

    ui: {
        toast: {
            duration: 4000,
            position: "bottom-center",
            maxVisible: 3,
        },
    },

    doubleTapExit: true,
    doubleTapTimeout: 2000,

    debug: {
        enabled: import.meta.env?.DEV || false,
        level: 1,
    },

    // Dados específicos do Lamarck
    contacts: {
        whatsappNumber: "5534999999999",
        phone: "(34) 3333-3333",
    },

    cities: [
        "Uberlândia",
        "Uberaba",
        "Araguari",
        "Patos de Minas",
        "Ituiutaba",
    ],

    insurances: [
        "Unimed",
        "Bradesco",
        "SulAmérica",
        "Amil",
        "Porto Seguro",
        "Particular",
    ],
};

// Expõe config globalmente
window.SPA_CONFIG = SPA_CONFIG;

/**
 * Inicializa a aplicação
 */
async function initApp() {
    try {
        console.log("🚀 Inicializando Lamarck SPA...");

        // Cria instância do SPA
        const app = new SPA(SPA_CONFIG);

        // Expõe globalmente para uso nos blades/templates
        window.app = app;

        // Inicializa o SPA
        await app.init();

        // Inicializa ícones Lucide
        initLucide();

        // Registra handlers de páginas
        initHomePage(app);
        initAgendamentoPage(app);
        initChatPage(app);
        initConfiguracoesPage(app);
        initConveniosPage(app);
        initExamesListaPage(app);
        initOrcamentosPage(app);
        initResultadoPage(app);

        // Inicializa sheets
        initSheets(app);

        // Escuta eventos globais
        setupGlobalEvents(app);

        console.log("✅ Lamarck SPA inicializado com sucesso!");

        return app;
    } catch (error) {
        console.error("❌ Erro ao inicializar Lamarck SPA:", error);
        throw error;
    }
}

/**
 * Configura eventos globais
 */
function setupGlobalEvents(app) {
    // Re-renderiza ícones Lucide após navegação
    document.addEventListener("page:enter", () => {
        setTimeout(() => initLucide(), 50);
    });

    // Trata erros de conexão
    window.addEventListener("offline", () => {
        app.toastWarning("Sem conexão", "Você está offline");
    });

    window.addEventListener("online", () => {
        app.toastSuccess("Conectado", "Conexão restabelecida");
    });

    // Previne zoom em inputs no iOS
    document.addEventListener("touchstart", (e) => {
        if (e.target.tagName === "INPUT" || e.target.tagName === "TEXTAREA") {
            e.target.style.fontSize = "16px";
        }
    });
}

/**
 * Funções utilitárias globais
 */
window.formatPhone = formatPhone;
window.formatCPF = formatCPF;
window.formatDate = formatDate;

// Inicializa quando DOM estiver pronto
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initApp);
} else {
    initApp();
}

export { initApp, SPA_CONFIG };
