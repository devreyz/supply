/**
 * 📋 Resultado Page - Visualização de exames
 */

/**
 * Inicializa a página de Resultado
 * @param {SPA} app - Instância do SPA
 */
export function initResultadoPage(app) {
    const page = document.getElementById("page-resultado");
    if (!page) return;

    page.addEventListener("page:enter", () => {
        console.log("📋 Resultado page entered");
        loadResultado();
        setupEventListeners(app);
    });
}

/**
 * Configura event listeners
 */
function setupEventListeners(app) {
    const btnAbrir = document.getElementById("btn-abrir-resultado");
    if (btnAbrir) {
        btnAbrir.addEventListener("click", () => openResultadoUrl());
    }
}

/**
 * Carrega dados do resultado
 */
function loadResultado() {
    // Busca código de acesso (pode vir de um input anterior ou storage)
    const codigo =
        localStorage.getItem("lamarck_current_result_code") || "D31B91F2";

    // Dados simulados (substituir por API real)
    const paciente = "João da Silva";
    const prazo = "2 dias úteis";
    const url = `https://lamarck.sisvida.com.br/r/${codigo}`;

    // Exames simulados
    const exames = [
        { name: "Hemograma Completo", realizado: true },
        { name: "Glicemia em Jejum", realizado: true },
        { name: "Colesterol Total", realizado: true },
        { name: "TSH", realizado: false },
    ];

    // Atualiza UI
    const patientName = document.getElementById("result-patient-name");
    const examCode = document.getElementById("result-exam-code");
    const examDate = document.getElementById("result-exam-date");
    const patientNameSmall = document.getElementById(
        "result-patient-name-small"
    );
    const examCodeSmall = document.getElementById("result-exam-code-small");
    const resultUrl = document.getElementById("result-url");
    const deadline = document.getElementById("result-deadline");

    if (patientName) patientName.textContent = paciente;
    if (examCode) examCode.textContent = `Código: ${codigo}`;
    if (examDate)
        examDate.textContent = `Data: ${new Date().toLocaleDateString(
            "pt-BR"
        )}`;
    if (patientNameSmall) patientNameSmall.textContent = paciente;
    if (examCodeSmall) examCodeSmall.textContent = codigo;
    if (resultUrl) {
        resultUrl.textContent = url;
        resultUrl.href = url;
    }
    if (deadline) deadline.textContent = prazo;

    // Lista de exames
    renderExamesList(exames.filter((e) => e.realizado));
}

/**
 * Renderiza lista de exames
 * @param {Array} exames
 */
function renderExamesList(exames) {
    const list = document.getElementById("result-exams-list");
    if (!list) return;

    list.innerHTML = exames
        .map(
            (ex) => `
        <div class="list-item">
            <div class="list-item-icon bg-green-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-green-600"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
            <div class="list-item-content">
                <p class="list-item-title">${ex.name}</p>
                <p class="list-item-subtitle">Resultado disponível</p>
            </div>
        </div>
    `
        )
        .join("");
}

/**
 * Abre URL do resultado
 */
export function openResultadoUrl() {
    const link = document.getElementById("result-url")?.href;

    if (link) {
        window.open(link, "_blank");
    } else {
        window.app?.toastError("Erro", "URL do resultado indisponível");
    }
}

/**
 * Define código de resultado atual
 * @param {string} codigo
 */
export function setResultCode(codigo) {
    localStorage.setItem("lamarck_current_result_code", codigo);
}

/**
 * Busca resultado por código
 * @param {string} codigo
 */
export async function buscarResultado(codigo) {
    const app = window.app;

    if (!codigo || codigo.length < 6) {
        app?.toastWarning(
            "Código inválido",
            "Informe um código de acesso válido"
        );
        return;
    }

    app?.showLoading();

    try {
        // Simula busca (substituir por API real)
        await new Promise((resolve) => setTimeout(resolve, 1500));

        // Salva código e navega
        setResultCode(codigo.toUpperCase());

        app?.hideLoading();
        app?.go("resultado");
    } catch (error) {
        app?.hideLoading();
        app?.toastError("Erro", "Não foi possível encontrar o resultado");
    }
}

// Expõe globalmente
window.openResultadoUrl = openResultadoUrl;
window.buscarResultado = buscarResultado;
window.setResultCode = setResultCode;
