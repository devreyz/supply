/**
 * 💰 Orçamentos Page
 */

import { formatPhone, formatCPF, isValidEmail } from "../utils/helpers.js";

/**
 * Inicializa a página de Orçamentos
 * @param {SPA} app - Instância do SPA
 */
export function initOrcamentosPage(app) {
    const page = document.getElementById("page-orcamentos");
    if (!page) return;

    page.addEventListener("page:enter", () => {
        console.log("💰 Orçamentos page entered");
        setupInputMasks();
        setupEventListeners(app);
        loadOrcamentos();
    });
}

/**
 * Configura event listeners
 */
function setupEventListeners(app) {
    // Botão upload
    const btnUpload = document.getElementById("btn-upload-orcamento");
    const inputFile = document.getElementById("orc-pedido");
    if (btnUpload && inputFile) {
        btnUpload.addEventListener("click", () => inputFile.click());
        inputFile.addEventListener("change", (e) =>
            handleOrcamentoFile(e.target)
        );
    }

    // Botão enviar
    const btnEnviar = document.getElementById("btn-enviar-orcamento");
    if (btnEnviar) {
        btnEnviar.addEventListener("click", () => enviarOrcamento());
    }
}

/**
 * Configura máscaras de input
 */
function setupInputMasks() {
    const telefoneInput = document.getElementById("orc-telefone");
    if (telefoneInput) {
        telefoneInput.addEventListener("input", (e) => {
            e.target.value = formatPhone(e.target.value);
        });
    }

    const cpfInput = document.getElementById("orc-cpf");
    if (cpfInput) {
        cpfInput.addEventListener("input", (e) => {
            e.target.value = formatCPF(e.target.value);
        });
    }
}

/**
 * Carrega orçamentos salvos
 */
function loadOrcamentos() {
    const saved = localStorage.getItem("lamarck_orcamentos");
    if (!saved) return;

    try {
        const orcamentos = JSON.parse(saved);
        renderOrcamentos(orcamentos);
    } catch (e) {
        console.error("Erro ao carregar orçamentos:", e);
    }
}

/**
 * Renderiza lista de orçamentos
 * @param {Array} orcamentos
 */
function renderOrcamentos(orcamentos) {
    const container = document.getElementById("orcamentos-lista");
    if (!container) return;

    if (orcamentos.length === 0) {
        container.innerHTML = `
            <div class="p-4 text-center text-slate-500">
                <p>Nenhum orçamento solicitado ainda</p>
            </div>
        `;
        return;
    }

    container.innerHTML = orcamentos
        .slice(0, 5)
        .map((orc) => {
            const statusClass =
                orc.status === "respondido"
                    ? "bg-green-100 text-green-600"
                    : "bg-amber-100 text-amber-600";
            const statusIcon = orc.status === "respondido" ? "check" : "clock";

            return `
            <div class="list-item">
                <div class="list-item-icon ${statusClass}">
                    <i data-lucide="${statusIcon}" class="w-5 h-5"></i>
                </div>
                <div class="list-item-content">
                    <p class="list-item-title">Orçamento #${orc.id}</p>
                    <p class="list-item-subtitle">Enviado em ${orc.date} • ${
                orc.status === "respondido" ? "Respondido" : "Aguardando"
            }</p>
                </div>
                <i data-lucide="chevron-right" class="list-item-action"></i>
            </div>
        `;
        })
        .join("");
}

/**
 * Manipula seleção de arquivo
 * @param {HTMLInputElement} input
 */
export function handleOrcamentoFile(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const preview = document.getElementById("orc-file-preview");
            if (preview) {
                preview.querySelector("img").src = e.target.result;
                preview.classList.remove("hidden");
            }

            const label = document.getElementById("orc-file-label");
            if (label) label.textContent = "Pedido Anexado ✓";
        };
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * Envia orçamento
 */
export async function enviarOrcamento() {
    const app = window.app;

    // Coleta dados
    const nome = document.getElementById("orc-nome")?.value.trim();
    const telefone = document.getElementById("orc-telefone")?.value.trim();
    const email = document.getElementById("orc-email")?.value.trim();
    const cpf = document.getElementById("orc-cpf")?.value.trim();
    const convenio = document.getElementById("orc-convenio")?.value;
    const exames = document.getElementById("orc-exames")?.value.trim();
    const pedidoFile = document.getElementById("orc-pedido")?.files[0];

    // Validação
    if (!nome) {
        app?.toastWarning("Campo obrigatório", "Informe seu nome");
        return;
    }
    if (!telefone || telefone.length < 14) {
        app?.toastWarning("Campo obrigatório", "Informe um telefone válido");
        return;
    }
    if (!email || !isValidEmail(email)) {
        app?.toastWarning("Campo obrigatório", "Informe um email válido");
        return;
    }
    if (!exames && !pedidoFile) {
        app?.toastWarning(
            "Exames não informados",
            "Liste os exames ou envie o pedido médico"
        );
        return;
    }

    app?.showLoading();

    try {
        // Simula envio (substituir por API real)
        await new Promise((resolve) => setTimeout(resolve, 2000));

        // Salva localmente
        const orcamentos = JSON.parse(
            localStorage.getItem("lamarck_orcamentos") || "[]"
        );
        const newOrc = {
            id: Math.random().toString(36).substring(2, 8).toUpperCase(),
            date: new Date().toLocaleDateString("pt-BR"),
            status: "pendente",
            nome,
            email,
            exames,
        };
        orcamentos.unshift(newOrc);
        localStorage.setItem(
            "lamarck_orcamentos",
            JSON.stringify(orcamentos.slice(0, 20))
        );

        app?.hideLoading();
        app?.toastSuccess("Orçamento enviado!", "Responderemos em até 24h");

        // Limpa formulário
        clearOrcamentoForm();

        // Atualiza lista
        loadOrcamentos();
    } catch (error) {
        app?.hideLoading();
        app?.toastError("Erro", "Não foi possível enviar o orçamento");
    }
}

/**
 * Limpa formulário de orçamento
 */
function clearOrcamentoForm() {
    ["orc-nome", "orc-telefone", "orc-email", "orc-cpf", "orc-exames"].forEach(
        (id) => {
            const el = document.getElementById(id);
            if (el) el.value = "";
        }
    );

    const convenio = document.getElementById("orc-convenio");
    if (convenio) convenio.selectedIndex = 0;

    const pedido = document.getElementById("orc-pedido");
    if (pedido) pedido.value = "";

    const preview = document.getElementById("orc-file-preview");
    if (preview) preview.classList.add("hidden");

    const label = document.getElementById("orc-file-label");
    if (label) label.textContent = "Enviar Pedido Médico";
}

// Expõe globalmente
window.handleOrcamentoFile = handleOrcamentoFile;
window.enviarOrcamento = enviarOrcamento;
