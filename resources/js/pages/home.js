/**
 * 📱 Home Page - Dashboard Bento
 */

/**
 * Inicializa a página Home
 * @param {SPA} app - Instância do SPA
 */
export function initHomePage(app) {
    const page = document.getElementById("page-home");
    if (!page) return;

    // Evento quando entra na página
    page.addEventListener("page:enter", () => {
        console.log("🏠 Home page entered");
        updateNotificationBadge();
    });

    // Setup do badge de notificações
    setupNotificationBadge(app);
}

/**
 * Atualiza badge de notificações
 */
function updateNotificationBadge() {
    const badge = document.getElementById("notif-badge");
    if (!badge) return;

    // Busca contagem do storage
    const count = parseInt(localStorage.getItem("lamarck_notif_count") || "0");

    if (count > 0) {
        badge.textContent = count > 99 ? "99+" : count;
        badge.style.display = "flex";
    } else {
        badge.style.display = "none";
    }
}

/**
 * Configura badge de notificações
 * @param {SPA} app
 */
function setupNotificationBadge(app) {
    // Escuta mudanças no storage
    if (app.storage) {
        app.storage.watch("lamarck_notif_count", () => {
            updateNotificationBadge();
        });
    }
}

/**
 * Simula receber notificação (para demo)
 */
export function simulateNotification() {
    const current = parseInt(
        localStorage.getItem("lamarck_notif_count") || "0"
    );
    localStorage.setItem("lamarck_notif_count", (current + 1).toString());
    updateNotificationBadge();

    if (window.app) {
        window.app.toastInfo("Nova notificação", "Você tem uma nova mensagem");
    }
}

/**
 * Limpa notificações
 */
export function clearNotifications() {
    localStorage.setItem("lamarck_notif_count", "0");
    updateNotificationBadge();
}

// Expõe globalmente para uso em templates
window.simulateNotification = simulateNotification;
window.clearNotifications = clearNotifications;
