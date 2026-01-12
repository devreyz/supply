/**
 * 📑 Sheets - Gerenciador de Bottom Sheets
 */

/**
 * Inicializa todos os sheets
 * @param {SPA} app - Instância do SPA
 */
export function initSheets(app) {
    initWhatsAppSheet(app);
    initUserSettingsSheet(app);
}

/**
 * WhatsApp Sheet
 * @param {SPA} app
 */
function initWhatsAppSheet(app) {
    // Setup de opções de cidades e convênios no form
    setupWhatsAppForm();
}

/**
 * Configura formulário intermediário do WhatsApp
 */
function setupWhatsAppForm() {
    const cities = window.SPA_CONFIG?.cities || [
        "Uberlândia",
        "Uberaba",
        "Araguari",
    ];
    const insurances = window.SPA_CONFIG?.insurances || [
        "Unimed",
        "Bradesco",
        "Particular",
    ];

    renderOptions("whatsapp-cities", cities, "whatsapp-city");
    renderOptions("whatsapp-insurances", insurances, "whatsapp-insurance");
}

/**
 * Renderiza opções em grid
 * @param {string} containerId
 * @param {Array} list
 * @param {string} hiddenInputId
 */
function renderOptions(containerId, list, hiddenInputId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = "";
    list.forEach((item) => {
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className =
            "px-3 py-1.5 rounded-full border text-sm bg-white hover:bg-slate-50 transition-colors";
        btn.textContent = item;
        btn.dataset.value = item;

        btn.onclick = () => {
            // Desmarca todos
            Array.from(container.children).forEach((c) => {
                c.classList.remove("ring-2", "ring-red-400", "bg-red-50");
            });
            // Marca selecionado
            btn.classList.add("ring-2", "ring-red-400", "bg-red-50");

            const hidden = document.getElementById(hiddenInputId);
            if (hidden) hidden.value = item;
        };

        container.appendChild(btn);
    });
}

/**
 * Abre WhatsApp com tópico
 * @param {string} mensagem
 * @param {string} topic
 */
export function abrirWhatsAppTopic(mensagem, topic) {
    const app = window.app;

    // Define tópico e abre form intermediário
    const topicInput = document.getElementById("whatsapp-topic");
    if (topicInput) topicInput.value = topic;

    const intentInput = document.getElementById("whatsapp-intent");
    if (intentInput) intentInput.value = mensagem;

    // Mostra/esconde campo de data de nascimento para resultados
    const dobRow = document.getElementById("whatsapp-dob-row");
    if (dobRow) {
        dobRow.style.display = topic === "results" ? "block" : "none";
    }

    // Fecha sheet atual e abre form
    app?.closeSheet();
    setTimeout(() => {
        app?.openSheet("tpl-sheet-whatsapp-form");
    }, 300);
}

/**
 * Máscara de data
 * @param {HTMLInputElement} el
 */
export function maskDob(el) {
    let v = el.value.replace(/\D/g, "");
    if (v.length > 2) v = v.slice(0, 2) + "/" + v.slice(2);
    if (v.length > 5) v = v.slice(0, 5) + "/" + v.slice(5, 9);
    el.value = v;
}

/**
 * Submete form intermediário e abre WhatsApp
 */
export function submitWhatsAppMiddleware() {
    const app = window.app;

    const name = document.getElementById("whatsapp-name")?.value.trim();
    const city = document.getElementById("whatsapp-city")?.value;
    const insurance = document.getElementById("whatsapp-insurance")?.value;
    const intent = document.getElementById("whatsapp-intent")?.value || "";
    const topic = document.getElementById("whatsapp-topic")?.value || "other";
    const dob = document.getElementById("whatsapp-dob")?.value;

    if (!name) {
        app?.toastWarning("Nome obrigatório", "Informe o nome do paciente");
        return;
    }

    if (!city) {
        app?.toastWarning("Cidade obrigatória", "Selecione a cidade");
        return;
    }

    // Monta mensagem
    let mensagem = intent;
    mensagem += `\n\n*Nome:* ${name}`;
    mensagem += `\n*Cidade:* ${city}`;

    if (insurance) {
        mensagem += `\n*Convênio:* ${insurance}`;
    }

    if (topic === "results" && dob) {
        mensagem += `\n*Data de Nascimento:* ${dob}`;
    }

    // Abre WhatsApp
    const numero =
        window.SPA_CONFIG?.contacts?.whatsappNumber || "5534999999999";
    const url = `https://wa.me/${numero}?text=${encodeURIComponent(mensagem)}`;

    app?.closeSheet();

    setTimeout(() => {
        window.open(url, "_blank");
        app?.toastSuccess("Abrindo WhatsApp...", "");
    }, 300);
}

/**
 * Abre WhatsApp direto
 * @param {string} mensagem
 */
export function abrirWhatsApp(mensagem) {
    const app = window.app;
    const numero =
        window.SPA_CONFIG?.contacts?.whatsappNumber || "5534999999999";
    const url = `https://wa.me/${numero}?text=${encodeURIComponent(mensagem)}`;

    app?.closeSheet();

    setTimeout(() => {
        window.open(url, "_blank");
        app?.toastSuccess("Abrindo WhatsApp...", "");
    }, 300);
}

/**
 * User Settings Sheet
 * @param {SPA} app
 */
function initUserSettingsSheet(app) {
    // Sincroniza estado do toggle de tema
    const themeToggle = document.getElementById("theme-toggle-sheet");
    if (themeToggle) {
        const settings = JSON.parse(
            localStorage.getItem("lamarck_settings") || "{}"
        );
        themeToggle.checked = settings.theme === "dark";
    }

    // Sincroniza animação selecionada
    const animSelect = document.querySelector(".animation-select");
    if (animSelect) {
        const settings = JSON.parse(
            localStorage.getItem("lamarck_settings") || "{}"
        );
        animSelect.value = settings.animation || "fade";
    }
}

// Expõe globalmente
window.abrirWhatsAppTopic = abrirWhatsAppTopic;
window.abrirWhatsApp = abrirWhatsApp;
window.maskDob = maskDob;
window.submitWhatsAppMiddleware = submitWhatsAppMiddleware;
