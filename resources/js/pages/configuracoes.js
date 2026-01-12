/**
 * ⚙️ Configurações Page
 */

const SETTINGS_KEY = "lamarck_settings";

// Configurações padrão
const defaultSettings = {
    animation: "fade",
    notifications: {
        push: false,
        email: true,
        whatsapp: false,
    },
    theme: "light",
};

/**
 * Inicializa a página de Configurações
 * @param {SPA} app - Instância do SPA
 */
export function initConfiguracoesPage(app) {
    const page = document.getElementById("page-configuracoes");
    if (!page) return;

    page.addEventListener("page:enter", () => {
        console.log("⚙️ Configurações page entered");
        loadSettings();
        setupEventListeners(app);
    });
}

/**
 * Configura event listeners
 */
function setupEventListeners(app) {
    // Animation select
    const animSelect = document.getElementById("animation-select");
    if (animSelect) {
        animSelect.addEventListener("change", (e) =>
            mudarAnimacao(e.target.value)
        );
    }

    // Notification toggles
    const pushToggle = document.getElementById("push-notifications-toggle");
    if (pushToggle) {
        pushToggle.addEventListener("change", (e) =>
            toggleNotifications(e.target.checked)
        );
    }

    const emailToggle = document.getElementById("email-notifications-toggle");
    if (emailToggle) {
        emailToggle.addEventListener("change", (e) =>
            toggleEmailNotifications(e.target.checked)
        );
    }

    const whatsappToggle = document.getElementById(
        "whatsapp-notifications-toggle"
    );
    if (whatsappToggle) {
        whatsappToggle.addEventListener("change", (e) =>
            toggleWhatsAppNotifications(e.target.checked)
        );
    }

    // Botão termos e privacidade
    const btnTermos = document.getElementById("btn-termos-privacidade");
    if (btnTermos) {
        btnTermos.addEventListener("click", () => {
            app?.openModal("tpl-modal-termo", () => {
                setTimeout(() => {
                    const checkbox = document.getElementById("termo-aceite");
                    if (checkbox) {
                        checkbox.checked = !!localStorage.getItem(
                            "lamarck_lgpd_accepted"
                        );
                    }
                }, 100);
            });
        });
    }

    // Botão limpar dados
    const btnLimpar = document.getElementById("btn-limpar-dados");
    if (btnLimpar) {
        btnLimpar.addEventListener("click", () => limparDadosApp());
    }
}

/**
 * Carrega configurações salvas
 */
function loadSettings() {
    const settings = getSettings();

    // Animation select
    const animSelect = document.getElementById("animation-select");
    if (animSelect) {
        animSelect.value = settings.animation;
    }

    // Notification toggles
    const pushToggle = document.getElementById("push-notifications-toggle");
    const emailToggle = document.getElementById("email-notifications-toggle");
    const whatsappToggle = document.getElementById(
        "whatsapp-notifications-toggle"
    );

    if (pushToggle) pushToggle.checked = settings.notifications.push;
    if (emailToggle) emailToggle.checked = settings.notifications.email;
    if (whatsappToggle)
        whatsappToggle.checked = settings.notifications.whatsapp;
}

/**
 * Obtém configurações
 * @returns {Object}
 */
export function getSettings() {
    const saved = localStorage.getItem(SETTINGS_KEY);
    return saved
        ? { ...defaultSettings, ...JSON.parse(saved) }
        : defaultSettings;
}

/**
 * Salva configurações
 * @param {Object} settings
 */
export function saveSettings(settings) {
    localStorage.setItem(SETTINGS_KEY, JSON.stringify(settings));
}

/**
 * Muda animação de transição
 * @param {string} type
 */
export function mudarAnimacao(type) {
    const app = window.app;
    const settings = getSettings();

    settings.animation = type;
    saveSettings(settings);

    // Aplica imediatamente
    document.documentElement.dataset.animation = type;

    if (app?.config) {
        app.config.animation.type = type;
    }

    app?.toastSuccess("Animação alterada", `Transição: ${type}`);
}

/**
 * Toggle notificações push
 * @param {boolean} enabled
 */
export async function toggleNotifications(enabled) {
    const app = window.app;
    const settings = getSettings();

    if (enabled) {
        // Solicita permissão
        if (app?.notifications) {
            const granted = await app.notifications.request();
            if (granted) {
                settings.notifications.push = true;
                saveSettings(settings);
                app?.toastSuccess(
                    "Notificações ativadas",
                    "Você receberá alertas push"
                );
            } else {
                // Reverte toggle
                const toggle = document.getElementById(
                    "push-notifications-toggle"
                );
                if (toggle) toggle.checked = false;
                app?.toastWarning(
                    "Permissão negada",
                    "Habilite nas configurações do navegador"
                );
            }
        }
    } else {
        settings.notifications.push = false;
        saveSettings(settings);
        app?.toastInfo("Notificações desativadas", "");
    }
}

/**
 * Toggle notificações por email
 * @param {boolean} enabled
 */
export function toggleEmailNotifications(enabled) {
    const app = window.app;
    const settings = getSettings();

    settings.notifications.email = enabled;
    saveSettings(settings);

    app?.toastInfo(enabled ? "Email ativado" : "Email desativado", "");
}

/**
 * Toggle notificações WhatsApp
 * @param {boolean} enabled
 */
export function toggleWhatsAppNotifications(enabled) {
    const app = window.app;
    const settings = getSettings();

    settings.notifications.whatsapp = enabled;
    saveSettings(settings);

    app?.toastInfo(enabled ? "WhatsApp ativado" : "WhatsApp desativado", "");
}

/**
 * Limpa todos os dados do app
 */
export async function limparDadosApp() {
    const app = window.app;

    const confirmed = await app?.modal({
        title: "Limpar Dados",
        message:
            "Tem certeza que deseja apagar todos os dados salvos no app? Esta ação não pode ser desfeita.",
        type: "confirm",
        confirmText: "Sim, limpar",
        cancelText: "Cancelar",
        destructive: true,
    });

    if (confirmed) {
        // Limpa localStorage (exceto login)
        const keysToRemove = [];
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key?.startsWith("lamarck_")) {
                keysToRemove.push(key);
            }
        }
        keysToRemove.forEach((k) => localStorage.removeItem(k));

        // Limpa IndexedDB
        if (app?.db) {
            try {
                await app.db.clear();
            } catch (e) {
                console.error("Erro ao limpar IndexedDB:", e);
            }
        }

        app?.toastSuccess(
            "Dados limpos",
            "Todas as preferências foram removidas"
        );

        // Recarrega página
        setTimeout(() => location.reload(), 1500);
    }
}

/**
 * Toggle tema (dark/light)
 */
export function toggleTheme() {
    const settings = getSettings();
    const newTheme = settings.theme === "light" ? "dark" : "light";

    settings.theme = newTheme;
    saveSettings(settings);

    document.documentElement.classList.toggle("dark", newTheme === "dark");
    document.documentElement.dataset.theme = newTheme;

    // Atualiza toggle no sheet
    const toggleSheet = document.getElementById("theme-toggle-sheet");
    if (toggleSheet) toggleSheet.checked = newTheme === "dark";

    window.app?.toastInfo(
        `Tema ${newTheme === "dark" ? "escuro" : "claro"} ativado`,
        ""
    );
}

/**
 * Muda animação (do sheet de configurações)
 * @param {string} type
 */
export function changeAnimation(type) {
    mudarAnimacao(type);
}

/**
 * Logout
 */
export function logout() {
    const app = window.app;

    app?.modal({
        title: "Sair",
        message: "Deseja realmente sair da sua conta?",
        type: "confirm",
        confirmText: "Sair",
        cancelText: "Cancelar",
    }).then((confirmed) => {
        if (confirmed) {
            // Redireciona para logout (Laravel)
            window.location.href = "/logout";
        }
    });
}

// Expõe globalmente
window.mudarAnimacao = mudarAnimacao;
window.toggleNotifications = toggleNotifications;
window.toggleEmailNotifications = toggleEmailNotifications;
window.toggleWhatsAppNotifications = toggleWhatsAppNotifications;
window.limparDadosApp = limparDadosApp;
window.toggleTheme = toggleTheme;
window.changeAnimation = changeAnimation;
window.logout = logout;
