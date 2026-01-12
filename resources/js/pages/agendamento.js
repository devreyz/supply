/**
 * 📅 Agendamento Page - Formulário multi-step
 */

import { formatPhone, formatCPF } from "../utils/helpers.js";

// Estado do formulário
let currentStep = 1;
let selectedHorario = null;
let formData = {
    nome: "",
    cpf: "",
    telefone: "",
    exame: "",
    pedido: null,
    data: "",
    horario: "",
};

/**
 * Inicializa a página de Agendamento
 * @param {SPA} app - Instância do SPA
 */
export function initAgendamentoPage(app) {
    const page = document.getElementById("page-agendamento");
    if (!page) return;

    page.addEventListener("page:enter", () => {
        console.log("📅 Agendamento page entered");
        resetForm();
        setupInputMasks();
        setupHorarios();
        setupButtons(app);
        setMinDate();
    });
}

/**
 * Configura os botões do formulário
 */
function setupButtons(app) {
    // Botão upload
    const btnUpload = document.getElementById("btn-upload-pedido");
    const inputFile = document.getElementById("agend-pedido");
    if (btnUpload && inputFile) {
        btnUpload.addEventListener("click", () => inputFile.click());
        inputFile.addEventListener("change", handleFileSelect);
    }

    // Botão limpar arquivo
    const btnClear = document.getElementById("btn-clear-file");
    if (btnClear) {
        btnClear.addEventListener("click", clearFile);
    }

    // Botões de navegação entre steps
    const btnStep2 = document.getElementById("btn-step-2");
    if (btnStep2) {
        btnStep2.addEventListener("click", () => nextStep(2));
    }

    const btnStep3 = document.getElementById("btn-step-3");
    if (btnStep3) {
        btnStep3.addEventListener("click", () => nextStep(3));
    }

    const btnBackStep1 = document.getElementById("btn-back-step-1");
    if (btnBackStep1) {
        btnBackStep1.addEventListener("click", () => prevStep(1));
    }

    const btnBackStep2 = document.getElementById("btn-back-step-2");
    if (btnBackStep2) {
        btnBackStep2.addEventListener("click", () => prevStep(2));
    }

    // Botão submit
    const btnSubmit = document.getElementById("btn-submit-agendamento");
    if (btnSubmit) {
        btnSubmit.addEventListener("click", () => submitAgendamento(app));
    }
}

/**
 * Handle file selection
 */
function handleFileSelect(e) {
    const input = e.target;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (event) => {
            const preview = document.getElementById("file-preview");
            const img = preview?.querySelector("img");
            if (img) {
                img.src = event.target.result;
            }
            preview?.classList.remove("hidden");
            const label = document.getElementById("file-label");
            if (label) label.textContent = "Foto anexada ✓";
            formData.pedido = input.files[0];
        };
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * Clear selected file
 */
function clearFile() {
    const input = document.getElementById("agend-pedido");
    if (input) input.value = "";
    const preview = document.getElementById("file-preview");
    if (preview) preview.classList.add("hidden");
    const label = document.getElementById("file-label");
    if (label) label.textContent = "Enviar foto do pedido";
    formData.pedido = null;
}

/**
 * Reseta o formulário
 */
function resetForm() {
    currentStep = 1;
    selectedHorario = null;
    formData = {
        nome: "",
        cpf: "",
        telefone: "",
        exame: "",
        pedido: null,
        data: "",
        horario: "",
    };

    // Reseta steps visuais
    updateStepUI(1);

    // Limpa inputs
    [
        "agend-nome",
        "agend-cpf",
        "agend-telefone",
        "agend-exame",
        "agend-data",
    ].forEach((id) => {
        const el = document.getElementById(id);
        if (el) el.value = "";
    });

    // Esconde preview de arquivo
    const preview = document.getElementById("file-preview");
    if (preview) preview.classList.add("hidden");

    const label = document.getElementById("file-label");
    if (label) label.textContent = "Enviar foto do pedido";
}

/**
 * Configura máscaras de input
 */
function setupInputMasks() {
    const cpfInput = document.getElementById("agend-cpf");
    if (cpfInput) {
        cpfInput.addEventListener("input", (e) => {
            e.target.value = formatCPF(e.target.value);
        });
    }

    const phoneInput = document.getElementById("agend-telefone");
    if (phoneInput) {
        phoneInput.addEventListener("input", (e) => {
            e.target.value = formatPhone(e.target.value);
        });
    }
}

/**
 * Configura seleção de horários
 */
function setupHorarios() {
    const buttons = document.querySelectorAll(".horario-btn");
    buttons.forEach((btn) => {
        btn.addEventListener("click", () => {
            // Remove seleção anterior
            buttons.forEach((b) => b.classList.remove("btn-primary"));
            buttons.forEach((b) => b.classList.add("btn-outline"));

            // Adiciona seleção
            btn.classList.remove("btn-outline");
            btn.classList.add("btn-primary");

            selectedHorario = btn.dataset.horario;
            formData.horario = selectedHorario;
        });
    });
}

/**
 * Define data mínima como hoje
 */
function setMinDate() {
    const dateInput = document.getElementById("agend-data");
    if (dateInput) {
        const today = new Date().toISOString().split("T")[0];
        dateInput.min = today;
    }
}

/**
 * Avança para próximo step
 * @param {number} step
 */
export function nextStep(step) {
    const app = window.app;

    // Validação do step atual
    if (currentStep === 1) {
        const nome = document.getElementById("agend-nome")?.value.trim();
        const cpf = document.getElementById("agend-cpf")?.value.trim();
        const telefone = document
            .getElementById("agend-telefone")
            ?.value.trim();

        if (!nome) {
            app?.toastWarning("Campo obrigatório", "Informe seu nome");
            return;
        }
        if (!telefone || telefone.length < 14) {
            app?.toastWarning(
                "Campo obrigatório",
                "Informe um telefone válido"
            );
            return;
        }

        formData.nome = nome;
        formData.cpf = cpf;
        formData.telefone = telefone;
        formData.exame = document.getElementById("agend-exame")?.value || "";
    }

    if (currentStep === 2) {
        const data = document.getElementById("agend-data")?.value;

        if (!data) {
            app?.toastWarning("Campo obrigatório", "Selecione uma data");
            return;
        }
        if (!selectedHorario) {
            app?.toastWarning("Campo obrigatório", "Selecione um horário");
            return;
        }

        formData.data = data;
        formData.horario = selectedHorario;

        // Preenche confirmação
        updateConfirmation();
    }

    currentStep = step;
    updateStepUI(step);
}

/**
 * Volta para step anterior
 * @param {number} step
 */
export function prevStep(step) {
    currentStep = step;
    updateStepUI(step);
}

/**
 * Atualiza UI dos steps
 * @param {number} activeStep
 */
function updateStepUI(activeStep) {
    // Atualiza indicadores
    for (let i = 1; i <= 3; i++) {
        const stepEl = document.getElementById(`step-${i}`);
        const formStep = document.getElementById(`form-step-${i}`);

        if (stepEl) {
            if (i < activeStep) {
                // Completed
                stepEl.className =
                    "w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center text-sm font-bold";
            } else if (i === activeStep) {
                // Active
                stepEl.className =
                    "w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center text-sm font-bold";
            } else {
                // Pending
                stepEl.className =
                    "w-8 h-8 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-sm font-bold";
            }
        }

        if (formStep) {
            formStep.classList.toggle("hidden", i !== activeStep);
        }
    }

    // Atualiza linhas
    const line1 = document.getElementById("step-line-1");
    const line2 = document.getElementById("step-line-2");

    if (line1) {
        line1.className =
            activeStep > 1
                ? "flex-1 h-0.5 bg-green-500 mx-2"
                : "flex-1 h-0.5 bg-slate-200 mx-2";
    }
    if (line2) {
        line2.className =
            activeStep > 2
                ? "flex-1 h-0.5 bg-green-500 mx-2"
                : "flex-1 h-0.5 bg-slate-200 mx-2";
    }
}

/**
 * Atualiza tela de confirmação
 */
function updateConfirmation() {
    const exameLabel =
        document.getElementById("agend-exame")?.selectedOptions[0]?.text ||
        "Não especificado";
    const dataFormatted = formData.data
        ? new Date(formData.data + "T12:00:00").toLocaleDateString("pt-BR")
        : "-";

    document.getElementById("confirm-nome").textContent = formData.nome;
    document.getElementById("confirm-cpf").textContent =
        formData.cpf || "Não informado";
    document.getElementById("confirm-telefone").textContent = formData.telefone;
    document.getElementById("confirm-exame").textContent = exameLabel;
    document.getElementById("confirm-data").textContent = dataFormatted;
    document.getElementById("confirm-horario").textContent = formData.horario;
}

/**
 * Envia agendamento
 */
async function submitAgendamento(app) {
    app?.showLoading();

    try {
        // Simula envio (substituir por API real)
        await new Promise((resolve) => setTimeout(resolve, 2000));

        app?.hideLoading();
        app?.toastSuccess(
            "Agendamento enviado!",
            "Você receberá confirmação via WhatsApp"
        );

        // Volta para home após 2s
        setTimeout(() => {
            app?.go("home");
        }, 2000);
    } catch (error) {
        app?.hideLoading();
        app?.toastError("Erro", "Não foi possível enviar o agendamento");
    }
}
window.clearFile = clearFile;
