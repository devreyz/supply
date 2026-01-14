/**
 * 🎯 ZePocket - Entry Point
 * Ponto de entrada do módulo ZePocket para integração com Vite
 */

// Core exports
export { ZePocket, createZePocket, getZePocket } from "./app.js";
export { initDatabase, ZePocketDB } from "./db.js";
export { ZePocketSync, registerSyncActions } from "./sync.js";
export { registerActions } from "./actions.js";

// UI Components
export { ZePocketUI } from "./ui.js";
export { ZePocketViews } from "./views.js";

/**
 * Inicializa ZePocket com SPA Framework
 * @param {SPA} spa - Instância do SPA Framework
 * @param {Object} options - Opções de configuração
 */
export async function initZePocket(spa, options = {}) {
    const { createZePocket } = await import("./app.js");
    const { ZePocketViews } = await import("./views.js");

    const zepocket = createZePocket({
        spa,
        ...options,
    });

    await zepocket.init();

    // Expõe globalmente
    window.zepocket = zepocket;
    window.zepocketViews = new ZePocketViews(zepocket);

    // Carrega dados iniciais da home
    if (document.getElementById("page-zepocket")) {
        window.zepocketViews.loadHomeData();
    }

    return zepocket;
}
