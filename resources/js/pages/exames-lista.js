/**
 * 🔬 Exames Lista Page - Catálogo de exames
 */

// Base de dados de preparos
const examPreps = {
    hemograma: {
        title: "Hemograma Completo",
        alert: "Jejum de 4 horas recomendado",
        desc: "Exame básico de sangue para avaliação geral",
        items: [
            "Jejum de 4 horas (água liberada)",
            "Trazer documento com foto",
            "Informar medicamentos em uso",
        ],
    },
    glicemia: {
        title: "Glicemia em Jejum",
        alert: "Jejum de 8 a 12 horas obrigatório",
        desc: "Avaliação do nível de açúcar no sangue",
        items: [
            "Jejum de 8 a 12 horas",
            "Não ingerir bebidas açucaradas",
            "Água liberada",
            "Trazer pedido médico",
        ],
    },
    colesterol: {
        title: "Perfil Lipídico",
        alert: "Jejum de 12 horas obrigatório",
        desc: "Avaliação de colesterol e triglicerídeos",
        items: [
            "Jejum de 12 horas obrigatório",
            "Evitar bebidas alcoólicas 72h antes",
            "Manter dieta habitual nos dias anteriores",
            "Água liberada",
        ],
    },
    tsh: {
        title: "TSH e T4 Livre",
        alert: "Sem preparo especial",
        desc: "Avaliação da função da tireoide",
        items: [
            "Não requer jejum",
            "Informar se usa medicação para tireoide",
            "Trazer exames anteriores se tiver",
        ],
    },
    eas: {
        title: "EAS - Urina Tipo I",
        alert: "Primeira urina da manhã",
        desc: "Análise básica de urina",
        items: [
            "Coletar primeira urina da manhã",
            "Higienizar a região antes da coleta",
            "Desprezar o primeiro jato",
            "Coletar o jato médio",
        ],
    },
    urocultura: {
        title: "Urocultura",
        alert: "Jato médio, 4h de retenção",
        desc: "Cultura de urina para identificar bactérias",
        items: [
            "Retenção urinária de 4 horas",
            "Higienizar bem a região",
            "Desprezar primeiro jato",
            "Coletar jato médio em frasco estéril",
        ],
    },
    testosterona: {
        title: "Testosterona Total",
        alert: "Coleta pela manhã",
        desc: "Dosagem do hormônio masculino",
        items: [
            "Coletar entre 7h e 9h da manhã",
            "Jejum não obrigatório",
            "Informar uso de suplementos",
        ],
    },
    estradiol: {
        title: "Estradiol",
        alert: "Informar dia do ciclo menstrual",
        desc: "Dosagem do hormônio feminino",
        items: [
            "Informar dia do ciclo menstrual",
            "Jejum não obrigatório",
            "Informar uso de anticoncepcionais",
        ],
    },
    cortisol: {
        title: "Cortisol",
        alert: "Coleta entre 7h e 9h da manhã",
        desc: "Dosagem do hormônio do estresse",
        items: [
            "Coleta obrigatória entre 7h e 9h",
            "Evitar estresse antes da coleta",
            "Jejum não obrigatório",
        ],
    },
    ultrassom: {
        title: "Ultrassonografia",
        alert: "Preparo varia por região",
        desc: "Exame de imagem por ultrassom",
        items: [
            "Abdome: jejum de 6-8h, bexiga cheia",
            "Pélvico: bexiga cheia (1L água 1h antes)",
            "Tireoide: sem preparo",
            "Trazer exames anteriores",
        ],
    },
    "raio-x": {
        title: "Raio-X",
        alert: "Sem preparo especial",
        desc: "Exame de imagem por radiografia",
        items: [
            "Não requer preparo especial",
            "Remover objetos metálicos da região",
            "Informar se há possibilidade de gravidez",
        ],
    },
};

/**
 * Inicializa a página de Exames
 * @param {SPA} app - Instância do SPA
 */
export function initExamesListaPage(app) {
    const page = document.getElementById("page-exames-lista");
    if (!page) return;

    page.addEventListener("page:enter", () => {
        console.log("🔬 Exames lista page entered");
        setupSearch();
        setupEventListeners(app);
    });
}

/**
 * Configura event listeners
 */
function setupEventListeners(app) {
    // Busca
    const searchInput = document.getElementById("exames-search");
    if (searchInput) {
        searchInput.addEventListener("input", (e) =>
            filterExams(e.target.value)
        );
    }

    // Botão limpar
    const btnClear = document.getElementById("btn-clear-search");
    if (btnClear) {
        btnClear.addEventListener("click", () => clearSearch());
    }

    // Itens de exame
    document.querySelectorAll(".exame-item[data-exam]").forEach((item) => {
        item.addEventListener("click", () => {
            const examId = item.dataset.exam;
            showExamPrep(examId);
        });
    });
}

/**
 * Configura busca
 */
function setupSearch() {
    const input = document.getElementById("exames-search");
    if (input) {
        input.value = "";
        // Reseta filtros
        document.querySelectorAll(".exame-categoria").forEach((cat) => {
            cat.style.display = "";
        });
        document.querySelectorAll(".list-item").forEach((item) => {
            item.style.display = "";
        });
    }
}

/**
 * Filtra exames
 * @param {string} query
 */
export function filterExams(query) {
    const searchTerm = query.toLowerCase().trim();
    const categories = document.querySelectorAll(".exame-categoria");

    categories.forEach((category) => {
        const items = category.querySelectorAll(".list-item");
        let hasVisibleItems = false;

        items.forEach((item) => {
            const title =
                item
                    .querySelector(".list-item-title")
                    ?.textContent.toLowerCase() || "";
            const subtitle =
                item
                    .querySelector(".list-item-subtitle")
                    ?.textContent.toLowerCase() || "";

            const matches =
                title.includes(searchTerm) || subtitle.includes(searchTerm);
            item.style.display = matches ? "" : "none";

            if (matches) hasVisibleItems = true;
        });

        category.style.display = hasVisibleItems ? "" : "none";
    });
}

/**
 * Limpa busca
 */
export function clearSearch() {
    const input = document.getElementById("exames-search");
    if (input) {
        input.value = "";
        filterExams("");
    }
}

/**
 * Mostra preparo do exame
 * @param {string} examId
 */
export function showExamPrep(examId) {
    const app = window.app;
    const prep = examPreps[examId];

    if (!prep) {
        app?.toastWarning(
            "Preparo não encontrado",
            "Consulte nossa equipe via WhatsApp"
        );
        return;
    }

    // Atualiza template do sheet
    const titleEl = document.getElementById("preparo-title");
    const alertEl = document.getElementById("preparo-alert");
    const descEl = document.getElementById("preparo-desc");
    const itemsEl = document.getElementById("preparo-items");

    if (titleEl) titleEl.textContent = prep.title;
    if (alertEl) alertEl.textContent = prep.alert;
    if (descEl) descEl.textContent = prep.desc;

    if (itemsEl) {
        itemsEl.innerHTML = prep.items
            .map(
                (item, i) => `
            <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl">
                <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="text-xs font-bold text-red-600">${i + 1}</span>
                </div>
                <p class="text-slate-700">${item}</p>
            </div>
        `
            )
            .join("");
    }

    // Abre sheet
    app?.openSheet("tpl-sheet-preparo");
}

/**
 * Reseta filtros
 */
export function resetFilters() {
    const app = window.app;

    // Marca todos checkboxes
    document
        .querySelectorAll('#tpl-sheet-filtro input[type="checkbox"]')
        .forEach((cb) => {
            cb.checked = true;
        });

    // Mostra todos
    filterExams("");

    app?.toastInfo("Filtros limpos", "");
}

// Expõe globalmente
window.filterExams = filterExams;
window.clearSearch = clearSearch;
window.showExamPrep = showExamPrep;
window.resetFilters = resetFilters;
