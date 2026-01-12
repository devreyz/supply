<x-app-layout title="Orçamentos - Solicitação de Exames">
  <x-breadcrumb :links="[
    ['url' => '', 'label' => 'Orçamento']]" />

  <!-- Hero Section Modernizada -->
  <section class="relative py-20 bg-gradient-to-br from-primary/5 to-primary/10">
    <div class="absolute inset-0 bg-gradient-to-t from-background via-background/80 to-transparent"></div>
    <x-container class="relative text-center">
      <div class="max-w-2xl mx-auto space-y-6" data-aos="fade-in">
        <h1 class="text-4xl md:text-5xl font-bold text-text leading-tight mb-4">
          Seu Orçamento de Exames
          <span class="text-primary">Personalizado</span>
        </h1>
        <p class="text-xl text-text-secondary max-w-3xl mx-auto">
          Compare preços, obtenha descontos exclusivos e agende seus exames com praticidade
        </p>
      </div>
    </x-container>
  </section>


  <livewire:orcamento.cta-whatsapp /> <!-- Nova Seção: CTA WhatsApp -->
  <!-- Nova Seção: CTA WhatsApp -->
  <livewire:orcamento.cta-whatsapp />

  <!-- Seção de Orçamento Atualizada -->
  <section class="py-12 px-6 bg-background">
    <x-container>


      <!-- Tabela Modernizada -->
      <div class="bg-background rounded-2xl shadow-md overflow-hidden mb-6 border-2 border-border" data-aos="fade-up">
        <div class="p-6 border-b border-gray-100">
          <h3 class="text-xl font-semibold flex items-center">
            <svg class="w-6 h-6 mr-2 text-primary">
              <use href="#icon-budget" />
            </svg>
            Exames Selecionados
          </h3>
          <!-- Campo de Busca para Orçamentos -->
          <div id="quoteSearchContainer" class=" mx-auto relative transition-all duration-300 z-10" data-aos="scale-up">
            <div class="relative">
              <input type="text" id="quoteSearchInput" autocomplete="off" placeholder="Buscar exame..."
                class="w-full bg-input px-6 py-4 rounded-xl border border-border focus:ring-2 focus:ring-primary focus:border-transparent">
              <!-- Ícone de busca -->
              <svg class="w-6 h-6 absolute right-4 top-4 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              <!-- (Opcional) Você pode adicionar um ícone de loading se desejar -->
            </div>
            <!-- Container de Resultados da Busca -->
            <div id="quoteResultsContainer" class="mt-2 absolute bg-card border-2 border-secondary shadow-md rounded-md z-50 w-full max-h-10 overflow-y-scroll"></div>
          </div>
        </div>
        <div id="quoteTable" class="divide-y divide-card"></div>
        <div class="p-6 bg-card border-t border-border">
          <div class="flex justify-between items-center">
            <span class="text-gray-600">Total estimado:</span>
            <span id="quoteTotal" class="text-2xl font-bold text-primary">R$ 0,00</span>
          </div>
        </div>
      </div>

      <!-- Seção de Descontos Aprimorada -->
      <div class="grid md:grid-cols-2 gap-6" data-aos="fade-up">
        <div class="bg-card p-6 rounded-2xl shadow-sm">
          <h4 class="text-lg font-semibold mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-primary">
              <use href="#icon-discount" />
            </svg>
            Descontos por Convênio
          </h4>
          <select id="convenio" class="w-full px-4 py-3 bg-input text-input-on rounded-lg border border-b-border focus:ring-primary">
            <option value="0">Nenhum Convênio</option>
            <option value="10">Convênio A - 10% de desconto</option>
            <option value="15">Convênio B - 15% de desconto</option>
            <option value="20">Convênio C - 20% de desconto</option>
          </select>
        </div>

        <div class="bg-input p-6 rounded-2xl shadow-sm">
          <h4 class="text-lg font-semibold mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-primary">
              <use href="#icon-package" />
            </svg>
            Pacotes Promocionais
          </h4>
          <div class="flex items-center space-x-3">
            <input type="checkbox" id="packageOption" class="form-checkbox h-5 w-5 text-primary">
            <label class="text-gray-700">Aplicar desconto para pacote (+5%)</label>
          </div>
        </div>
      </div>
      </div>
    </x-container>
  </section>

  <!-- Nova Seção: Passo a Passo -->
  <section class="py-16 bg-background">
    <x-container>
      <div class="max-w-5xl mx-auto text-center mb-16">
        <h2 class="text-3xl font-bold text-primary mb-6" data-aos="fade-up">Como Funciona</h2>
        <p class="text-text-secondary text-lg" data-aos="fade-up" data-aos-delay="100">
          Solicitar seu orçamento é simples! Siga os passos abaixo:
        </p>
      </div>

      <div class="grid md:grid-cols-4 gap-8">
        <div class="p-6 bg-card rounded-xl shadow-lg" data-aos="zoom-in">
          <div class="w-14 h-14 bg-primary text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold shadow-md">
            1
          </div>
          <h3 class="text-lg font-semibold mb-2 text-center">Selecione os Exames</h3>
          <p class="text-text-secondary text-center">Escolha os exames desejados na nossa lista completa.</p>
        </div>

        <div class="p-6 bg-card rounded-xl shadow-lg" data-aos="zoom-in" data-aos-delay="100">
          <div class="w-14 h-14 bg-primary text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold shadow-md">
            2
          </div>
          <h3 class="text-lg font-semibold mb-2 text-center">Preencha Seus Dados</h3>
          <p class="text-text-secondary text-center">Informe seus dados para personalizar seu orçamento.</p>
        </div>

        <div class="p-6 bg-card rounded-xl shadow-lg" data-aos="zoom-in" data-aos-delay="200">
          <div class="w-14 h-14 bg-primary text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold shadow-md">
            3
          </div>
          <h3 class="text-lg font-semibold mb-2 text-center">Envie a Solicitação</h3>
          <p class="text-text-secondary text-center">Nos envie o pedido e aguarde nossa resposta rápida.</p>
        </div>

        <div class="p-6 bg-card rounded-xl shadow-lg" data-aos="zoom-in" data-aos-delay="300">
          <div class="w-14 h-14 bg-primary text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold shadow-md">
            4
          </div>
          <h3 class="text-lg font-semibold mb-2 text-center">Receba Seu Orçamento</h3>
          <p class="text-text-secondary text-center">Você receberá o orçamento detalhado no seu e-mail ou WhatsApp.</p>
        </div>
      </div>
    </x-container>
  </section>


  <!-- FAQ -->
  <section class="py-20 bg-white">
    <x-container>
      <div class="max-w-3xl mx-auto">
        <div class="text-center mb-16" data-aos="fade-up">
          <h2 class="text-3xl font-bold mb-4">Dúvidas Frequentes</h2>
          <p class="text-text-secondary">Encontre respostas para as principais perguntas</p>
        </div>

        <div class="space-y-6">
          <!-- Pergunta 1 -->
          <div class="border rounded-xl" data-aos="fade-up">
            <div x-data="{ open: false }" class="p-6 cursor-pointer" @click="open = !open">
              <div class="flex justify-between items-center">
                <h3 class="font-semibold">Preciso de autorização do convênio?</h3>
                <svg class="w-6 h-6 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
              <p x-show="open" class="mt-4 text-text-secondary">Depende do tipo de exame e do seu plano. Consulte nossa equipe para orientações específicas.</p>
            </div>
          </div>

          <!-- Mais perguntas... -->
        </div>
      </div>
    </x-container>
  </section>

  @push('scripts')
  <style>
    /* Estilos Customizados */
    #quoteResultsContainer {
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
    }

    [data-aos="scale-up"] {
      transform: scale(0.95);
      opacity: 0;
      transition: all 0.6s ease;
    }

    [data-aos="scale-up"].aos-animate {
      transform: scale(1);
      opacity: 1;
    }
  </style>
  <!-- Importando a biblioteca lodash para facilitar o debounce -->
  <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.15/lodash.min.js"></script>
  <!-- Importando o script de orçamentos compilado via Laravel Mix -->
  <script src="{{ asset('js/app/quote.js') }}"></script>
  @endpush
</x-app-layout>