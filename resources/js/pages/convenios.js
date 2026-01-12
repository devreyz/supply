/**
 * 💳 Convênios Page
 */

/**
 * Inicializa a página de Convênios
 * @param {SPA} app - Instância do SPA
 */
export function initConveniosPage(app) {
    const page = document.getElementById("page-convenios");
    if (!page) return;

    page.addEventListener("page:enter", () => {
        console.log("💳 Convênios page entered");
    });
}

/**
 * Abre WhatsApp para dúvidas de convênio
 */
export function contactAboutInsurance() {
    const app = window.app;
    app?.openSheet("tpl-sheet-whatsapp");
}

// Expõe globalmente
window.contactAboutInsurance = contactAboutInsurance;
