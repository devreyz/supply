<x-app-layout title="Exames Laboratoriais - Detalhes e Orçamentos">
  <x-breadcrumb :links="[
    ['url' => '', 'label' => 'Exames']]" />

  <!-- Hero Section Modernizado -->
  <section class="bg-gradient-to-br from-primary/10 to-primary-light/10 py-20">
    <x-container class="text-center">
      <div class="max-w-4xl mx-auto" data-aos="fade-up">
        <div class="inline-flex items-center bg-primary/10 px-4 py-2 rounded-full mb-6">
          <svg class="w-5 h-5 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
          <span class="text-primary font-medium">Mais de 500 tipos de exames</span>
        </div>
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
          Catálogo Completo de <span class="text-primary">Exames</span>
        </h1>
        <p class="text-xl text-gray-600 mb-8">
          Encontre todos os exames disponíveis com preços transparentes e informações detalhadas
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <x-link href="#buscar-exames" class="bg-primary text-white hover:bg-primary-dark px-6 py-3 rounded-lg font-medium transition-colors inline-flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Buscar Exames
          </x-link>
          <x-link href="/orcamento" variant="outline" class="border-2 border-primary text-primary hover:bg-primary hover:text-white px-6 py-3 rounded-lg font-medium transition-colors inline-flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
            </svg>
            Solicitar Orçamento
          </x-link>
        </div>
      </div>
    </x-container>
  </section>

  <!-- Categorias de Exames -->
  <section class="py-16 bg-white">
    <x-container>
      <div class="text-center mb-12" data-aos="fade-up">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Categorias de Exames</h2>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
          Navegue por categoria para encontrar rapidamente o exame que você precisa
        </p>
      </div>

      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="text-center p-6 rounded-xl bg-gradient-to-br from-red-50 to-red-100 border border-red-200 hover:shadow-lg transition-shadow cursor-pointer" data-aos="zoom-in">
          <div class="bg-red-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold mb-2 text-red-900">Hematologia</h3>
          <p class="text-red-700 text-sm">Hemograma, Coagulograma, Tipagem sanguínea</p>
        </div>

        <div class="text-center p-6 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 hover:shadow-lg transition-shadow cursor-pointer" data-aos="zoom-in" data-aos-delay="100">
          <div class="bg-blue-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold mb-2 text-blue-900">Bioquímica</h3>
          <p class="text-blue-700 text-sm">Glicose, Colesterol, Triglicerídeos, Função renal</p>
        </div>

        <div class="text-center p-6 rounded-xl bg-gradient-to-br from-green-50 to-green-100 border border-green-200 hover:shadow-lg transition-shadow cursor-pointer" data-aos="zoom-in" data-aos-delay="200">
          <div class="bg-green-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold mb-2 text-green-900">Urinálise</h3>
          <p class="text-green-700 text-sm">Urina rotina, Urocultura, Proteinúria</p>
        </div>

        <div class="text-center p-6 rounded-xl bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 hover:shadow-lg transition-shadow cursor-pointer" data-aos="zoom-in" data-aos-delay="300">
          <div class="bg-purple-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold mb-2 text-purple-900">Hormônios</h3>
          <p class="text-purple-700 text-sm">TSH, T3, T4, Insulina, Cortisol</p>
        </div>
      </div>
    </x-container>
  </section>

  <!-- Busca de Exames -->
  <section id="buscar-exames" class="py-16 bg-gray-50">
    <x-container>
      <div class="text-center mb-12" data-aos="fade-up">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Buscar Exames</h2>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
          Digite o nome do exame ou use filtros para encontrar rapidamente
        </p>
      </div>

      <!-- Campo de Busca Modernizado -->
      <div id="searchContainer" class="max-w-4xl mx-auto mb-12 relative" data-aos="fade-up">
        <div class="relative">
          <input type="text"
            id="searchInput"
            autocomplete="off"
            placeholder="Digite o nome do exame (ex: hemograma, glicose, colesterol...)"
            class="w-full px-6 py-4 pl-14 rounded-xl border-2 border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary shadow-sm text-lg">
          <!-- Ícone de busca -->
          <svg class="w-6 h-6 absolute left-4 top-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <!-- Ícone de Loading -->
          <div id="loadingIndicator" class="absolute right-4 top-4 hidden">
            <svg class="animate-spin h-6 w-6 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </div>
        </div>
        <!-- Container de Resultados -->
        <div class="max-h-[400px] overflow-y-auto" id="resultsContainer"></div>
      </div>

      <!-- Grid de Exames Populares -->
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6" data-aos="fade-up">
        <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all border border-gray-200">
          <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
              <h3 class="text-xl font-semibold text-gray-900 mb-2">Hemograma Completo</h3>
              <p class="text-gray-600 text-sm mb-3">Avaliação completa dos componentes do sangue para detectar anomalias, infecções e distúrbios hematológicos.</p>
              <div class="inline-flex items-center text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">
                <span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>
                Hematologia
              </div>
            </div>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-2xl font-bold text-primary">R$ 25,00</span>
            <button class="bg-primary text-white hover:bg-primary-dark px-4 py-2 rounded-lg font-medium transition-colors">
              Adicionar ao Orçamento
            </button>
          </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all border border-gray-200">
          <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
              <h3 class="text-xl font-semibold text-gray-900 mb-2">Glicose em Jejum</h3>
              <p class="text-gray-600 text-sm mb-3">Medição dos níveis de glicose no sangue após jejum para diagnóstico de diabetes e pré-diabetes.</p>
              <div class="inline-flex items-center text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                <span class="w-2 h-2 bg-blue-500 rounded-full mr-1"></span>
                Bioquímica
              </div>
            </div>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-2xl font-bold text-primary">R$ 15,00</span>
            <button class="bg-primary text-white hover:bg-primary-dark px-4 py-2 rounded-lg font-medium transition-colors">
              Adicionar ao Orçamento
            </button>
          </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all border border-gray-200">
          <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
              <h3 class="text-xl font-semibold text-gray-900 mb-2">Perfil Lipídico</h3>
              <p class="text-gray-600 text-sm mb-3">Análise completa do colesterol total, HDL, LDL e triglicerídeos para avaliação cardiovascular.</p>
              <div class="inline-flex items-center text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                <span class="w-2 h-2 bg-blue-500 rounded-full mr-1"></span>
                Bioquímica
              </div>
            </div>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-2xl font-bold text-primary">R$ 45,00</span>
            <button class="bg-primary text-white hover:bg-primary-dark px-4 py-2 rounded-lg font-medium transition-colors">
              Adicionar ao Orçamento
            </button>
          </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all border border-gray-200">
          <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
              <h3 class="text-xl font-semibold text-gray-900 mb-2">Urina Rotina</h3>
              <p class="text-gray-600 text-sm mb-3">Exame básico de urina para detecção de infecções, problemas renais e outras alterações.</p>
              <div class="inline-flex items-center text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                Urinálise
              </div>
            </div>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-2xl font-bold text-primary">R$ 20,00</span>
            <button class="bg-primary text-white hover:bg-primary-dark px-4 py-2 rounded-lg font-medium transition-colors">
              Adicionar ao Orçamento
            </button>
          </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all border border-gray-200">
          <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
              <h3 class="text-xl font-semibold text-gray-900 mb-2">TSH</h3>
              <p class="text-gray-600 text-sm mb-3">Dosagem do hormônio estimulante da tireoide para avaliação da função tireoidiana.</p>
              <div class="inline-flex items-center text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full">
                <span class="w-2 h-2 bg-purple-500 rounded-full mr-1"></span>
                Hormônios
              </div>
            </div>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-2xl font-bold text-primary">R$ 35,00</span>
            <button class="bg-primary text-white hover:bg-primary-dark px-4 py-2 rounded-lg font-medium transition-colors">
              Adicionar ao Orçamento
            </button>
          </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all border border-gray-200">
          <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
              <h3 class="text-xl font-semibold text-gray-900 mb-2">Urocultura</h3>
              <p class="text-gray-600 text-sm mb-3">Cultura de urina para identificação de bactérias causadoras de infecções urinárias.</p>
              <div class="inline-flex items-center text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                Urinálise
              </div>
            </div>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-2xl font-bold text-primary">R$ 40,00</span>
            <button class="bg-primary text-white hover:bg-primary-dark px-4 py-2 rounded-lg font-medium transition-colors">
              Adicionar ao Orçamento
            </button>
          </div>
        </div>
      </div>

      <!-- Botão Ver Mais -->
      <div class="text-center mt-12" data-aos="fade-up">
        <button class="bg-primary text-white hover:bg-primary-dark px-8 py-3 rounded-lg font-medium transition-colors inline-flex items-center">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
          Ver Todos os Exames
        </button>
      </div>
    </x-container>
  </section>

  <!-- Como Agendar -->
  <section class="py-16 bg-white">
    <x-container>
      <div class="text-center mb-12" data-aos="fade-up">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Como Agendar seus Exames</h2>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
          Processo simples e rápido para agendar seus exames
        </p>
      </div>

      <div class="grid md:grid-cols-3 gap-8">
        <div class="text-center" data-aos="zoom-in">
          <div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold shadow-lg">
            1
          </div>
          <h3 class="text-lg font-semibold mb-2">Escolha os Exames</h3>
          <p class="text-gray-600">Selecione os exames desejados ou solicite orientação via chat IA.</p>
        </div>

        <div class="text-center" data-aos="zoom-in" data-aos-delay="100">
          <div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold shadow-lg">
            2
          </div>
          <h3 class="text-lg font-semibold mb-2">Solicite Orçamento</h3>
          <p class="text-gray-600">Receba valores detalhados e formas de pagamento disponíveis.</p>
        </div>

        <div class="text-center" data-aos="zoom-in" data-aos-delay="200">
          <div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold shadow-lg">
            3
          </div>
          <h3 class="text-lg font-semibold mb-2">Agende na Unidade</h3>
          <p class="text-gray-600">Confirme data e horário na unidade mais próxima de você.</p>
        </div>
      </div>
    </x-container>
  </section>

  <!-- CTA WhatsApp -->
  <livewire:orcamento.cta-whatsapp />

  @push('scripts')
  <!-- Importando a biblioteca lodash para facilitar o debounce -->
  <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.15/lodash.min.js"></script>
  <!-- Importando o script de busca compilado via Laravel Mix -->
  <script src="{{ asset('js/app/search.js') }}"></script>

  <script>
    // Gerenciador de busca de exames modernizado
    class ExamSearchManager {
      constructor() {
        this.initializeElements();
        this.setupEventListeners();
      }

      initializeElements() {
        this.elements = {
          searchInput: document.getElementById('searchInput'),
          resultsContainer: document.getElementById('resultsContainer'),
          loadingIndicator: document.getElementById('loadingIndicator')
        };
      }

      setupEventListeners() {
        if (this.elements.searchInput) {
          this.elements.searchInput.addEventListener('input',
            _.debounce((e) => this.searchExams(e.target.value), 300)
          );
        }
      }

      searchExams(query) {
        if (query.length < 2) {
          this.elements.resultsContainer.innerHTML = '';
          return;
        }

        this.showLoading(true);

        // Simular busca (substituir pela API real)
        setTimeout(() => {
          this.showSearchResults(query);
          this.showLoading(false);
        }, 500);
      }

      showLoading(show) {
        if (show) {
          this.elements.loadingIndicator.classList.remove('hidden');
        } else {
          this.elements.loadingIndicator.classList.add('hidden');
        }
      }

      showSearchResults(query) {
        const mockResults = [{
            id: 1,
            name: 'Hemograma Completo',
            price: 25.00,
            description: 'Análise completa do sangue',
            category: 'Hematologia'
          },
          {
            id: 2,
            name: 'Glicose',
            price: 15.00,
            description: 'Dosagem de glicose no sangue',
            category: 'Bioquímica'
          },
          {
            id: 3,
            name: 'Colesterol Total',
            price: 18.00,
            description: 'Dosagem de colesterol total',
            category: 'Bioquímica'
          },
          {
            id: 4,
            name: 'TSH',
            price: 35.00,
            description: 'Hormônio estimulante da tireoide',
            category: 'Hormônios'
          },
          {
            id: 5,
            name: 'Urina Rotina',
            price: 20.00,
            description: 'Exame básico de urina',
            category: 'Urinálise'
          }
        ].filter(exam => exam.name.toLowerCase().includes(query.toLowerCase()));

        let html = '';
        if (mockResults.length > 0) {
          html = '<div class="bg-white border border-gray-200 rounded-lg mt-2 shadow-lg overflow-hidden">';
          mockResults.forEach(exam => {
            const categoryColors = {
              'Hematologia': 'bg-red-100 text-red-800',
              'Bioquímica': 'bg-blue-100 text-blue-800',
              'Hormônios': 'bg-purple-100 text-purple-800',
              'Urinálise': 'bg-green-100 text-green-800'
            };
            html += `
              <div class="p-4 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 cursor-pointer" 
                   onclick="window.location.href='/orcamento?exame=${exam.id}'">
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <div class="flex items-center mb-2">
                      <h4 class="font-medium text-gray-900 mr-2">${exam.name}</h4>
                      <span class="inline-flex items-center text-xs ${categoryColors[exam.category]} px-2 py-1 rounded-full">
                        ${exam.category}
                      </span>
                    </div>
                    <p class="text-sm text-gray-600">${exam.description}</p>
                  </div>
                  <div class="text-right ml-4">
                    <div class="text-lg font-semibold text-primary">R$ ${exam.price.toFixed(2).replace('.', ',')}</div>
                    <div class="text-sm text-gray-500">Clique para orçar</div>
                  </div>
                </div>
              </div>
            `;
          });
          html += '</div>';
        } else {
          html = '<div class="bg-white border border-gray-200 rounded-lg mt-2 p-4 text-center text-gray-500">Nenhum exame encontrado</div>';
        }

        this.elements.resultsContainer.innerHTML = html;
      }
    }

    // Inicializar quando o DOM estiver pronto
    document.addEventListener('DOMContentLoaded', function() {
      new ExamSearchManager();
    });
  </script>
  @endpush
</x-app-layout>