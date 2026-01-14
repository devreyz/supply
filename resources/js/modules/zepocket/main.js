/**
 * 🎯 ZePocket Main Entry - Vite Build
 * Este arquivo é o ponto de entrada para compilação do Vite
 */

// Importa o módulo ZePocket
import { initZePocket } from "./index.js";

// Importa views controller
import "./views.js";

// Auto-inicialização quando SPA estiver pronto
document.addEventListener("DOMContentLoaded", async () => {
    // Aguarda SPA Framework estar disponível
    if (typeof window.spa !== "undefined") {
        await initZePocket(window.spa, {
            apiBase: "/api/zepocket",
            autoSync: true,
            syncInterval: 60000, // 1 minuto
        });

        console.log("✅ ZePocket initialized with SPA");
    } else {
        // Inicializa standalone
        const { createZePocket } = await import("./app.js");
        const zepocket = createZePocket({
            apiBase: "/api/zepocket",
            autoSync: true,
        });

        await zepocket.init();
        window.zepocket = zepocket;

        console.log("✅ ZePocket initialized standalone");
    }

    // Carrega dados iniciais se estiver na página ZePocket
    if (window.zepocketViews && document.getElementById("page-zepocket")) {
        window.zepocketViews.loadHomeData();
    }
});

// Exporta para uso em outros módulos
export * from "./index.js";
