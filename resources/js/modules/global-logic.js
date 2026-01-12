/**
 * Global logic for overlays (sheets, modals) that appear across the app
 */

export function initGlobalLogic(app) {
    // 1. Theme Toggle Logic
    const themeBtn = document.getElementById("btn-toggle-theme");
    const themeToggle = document.getElementById("theme-toggle-sheet");

    if (themeBtn && themeToggle) {
        // Inicializa estado do checkbox baseado no tema atual
        const currentTheme = document.documentElement.dataset.theme || "light";
        themeToggle.checked = currentTheme === "dark";

        themeBtn.addEventListener("click", () => {
            app.toggleTheme();
            themeToggle.checked = !themeToggle.checked;
        });
    }

    // 2. Animation Select Logic
    const animationSelects = document.querySelectorAll(".animation-select");
    animationSelects.forEach((select) => {
        // Set initial value from config
        select.value = app.config.animation.type;

        select.addEventListener("change", (e) => {
            const type = e.target.value;
            app.setAnimation(type);
        });
    });

    // 3. Logout Logic
    const logoutBtn = document.querySelector('[data-action="logout"]');
    if (logoutBtn) {
        logoutBtn.addEventListener("click", (e) => {
            e.preventDefault();
            app.logout();
        });
    } else {
        // Fallback for the one in user-settings if it's not ID-based yet
        const legacyLogout = document.getElementById("btn-logout-settings");
        if (legacyLogout) {
            legacyLogout.addEventListener("click", () => app.logout());
        }
    }

    // 4. LGPD Modal Logic
    setupLGPD(app);
}

function setupLGPD(app) {
    const accepted = localStorage.getItem("lamarck_lgpd_accepted");

    // Se já aceitou, não faz nada
    if (accepted && accepted !== "null") return;

    // Flag de controle local (singleton)
    if (window._lgpdInitialized) return;
    window._lgpdInitialized = true;

    let modalOpened = false;

    const tryOpenModal = () => {
        // Só abre na home e se ainda não abriu
        if (modalOpened || app.current !== "home") return;

        // Verificação final no storage
        const stillNotAccepted = !localStorage.getItem("lamarck_lgpd_accepted");
        if (stillNotAccepted) {
            modalOpened = true;
            openLGPDModal(app);
        }
    };

    // Escuta evento de entrada na página
    document.addEventListener("page:enter", (e) => {
        const pageId = e.detail?.pageId || e.target?.id?.replace("page-", "");
        if (pageId === "home") {
            setTimeout(tryOpenModal, 2000);
        }
    });

    // Caso o app já tenha iniciado e já esteja na home (primeiro carregamento)
    if (app.current === "home") {
        setTimeout(tryOpenModal, 2000);
    }
}

async function openLGPDModal(app) {
    // Evita abrir múltiplos: detecta se já há um modal com o checkbox dos termos
    if (document.querySelector(".modal-overlay #termo-aceite")) return;

    // Configura interceptação ANTES de abrir o modal
    const handleModalClick = (e) => {
        const acceptBtn = e.target.closest("#btn-aceitar");
        if (!acceptBtn) return;

        const checkbox = document.getElementById("termo-aceite");
        if (!checkbox || !checkbox.checked) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            app.toast(
                "Você precisa aceitar os termos para continuar",
                "warning"
            );
            return;
        }

        // Checkbox marcado: permite o modal fechar e executa lógica de aceite
        // Remove o listener para evitar múltiplas execuções
        document.removeEventListener("click", handleModalClick, true);

        // Salva aceite
        localStorage.setItem("lamarck_lgpd_accepted", Date.now());

        // Aguarda um frame para garantir que o modal fechou
        requestAnimationFrame(() => {
            app.toast("Bem-vindo ao Laboratório Lamarck!", "success");

            // Agenda exibição do PWA após 15 segundos
            if (app.pwa && typeof app.pwa.showInstallPrompt === "function") {
                console.log("🚀 PWA: Agendando prompt para daqui a 15s...");
                setTimeout(() => {
                    const stillAccepted = localStorage.getItem(
                        "lamarck_lgpd_accepted"
                    );
                    if (stillAccepted) {
                        console.log(
                            "🚀 PWA: Disparando prompt automático após LGPD"
                        );
                        app.pwa.showInstallPrompt();
                    }
                }, 15000);
            }
        });
    };

    // Adiciona listener em capturing phase (executa ANTES do ModalManager)
    document.addEventListener("click", handleModalClick, true);

    try {
        const result = await app.modal({
            template: "tpl-modal-termo",
            closeOnBackdrop: false,
        });

        // Se o modal foi fechado de outra forma (ESC, etc), remove listener
        document.removeEventListener("click", handleModalClick, true);
    } catch (err) {
        console.error("Erro ao abrir modal LGPD:", err);
        document.removeEventListener("click", handleModalClick, true);
    }
}
