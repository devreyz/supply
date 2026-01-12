<x-app-layout title="Unidades - Laboratório Lamarck">
  <x-breadcrumb :links="[
    ['url' => '', 'label' => 'Unidades']]" />

  <!-- Hero Section Modernizado -->
  <section class="bg-gradient-to-br from-primary/10 to-primary-light/10 py-20">
    <x-container class="text-center">
      <div class="max-w-4xl mx-auto" data-aos="fade-up">
        <div class="inline-flex items-center bg-primary/10 px-4 py-2 rounded-full mb-6">
          <svg class="w-5 h-5 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <span class="text-primary font-medium">Próximo de você</span>
        </div>
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
          Nossas <span class="text-primary">Unidades</span>
        </h1>
        <p class="text-xl text-gray-600 mb-8">
          Encontre a unidade mais próxima de você e agende seus exames com comodidade
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <x-link href="#mapa" class="bg-primary text-white hover:bg-primary-dark px-6 py-3 rounded-lg font-medium transition-colors inline-flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
            Ver no Mapa
          </x-link>
          <x-link href="#unidades" variant="outline" class="border-2 border-primary text-primary hover:bg-primary hover:text-white px-6 py-3 rounded-lg font-medium transition-colors inline-flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            Lista de Unidades
          </x-link>
        </div>
      </div>
    </x-container>
  </section>

  <!-- Filtro de Busca -->
  <section class="py-8 bg-white border-b border-gray-100">
    <x-container>
      <div class="max-w-2xl mx-auto" data-aos="fade-up">
        <div class="relative">
          <input type="text"
            id="searchUnits"
            placeholder="Buscar por cidade, bairro ou nome da unidade..."
            class="w-full px-6 py-4 pl-14 rounded-xl border-2 border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary shadow-sm">
          <svg class="w-6 h-6 absolute left-4 top-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>
    </x-container>
  </section>

  <!-- Lista de Unidades -->
  <section id="unidades" class="py-20 bg-white">
    <x-container>
      <div class="text-center mb-16" data-aos="fade-up">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Todas as Unidades</h2>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
          Escolha a unidade mais conveniente para você
        </p>
      </div>

      <!-- Grid de Unidades Modernizado -->
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8"
        x-data="{
             unidades: {{json_encode($unidades ?? [])}},
             search: '',
             filteredUnidades() {
               if (!this.search) return this.unidades;
               return this.unidades.filter(u => 
                 u.nome.toLowerCase().includes(this.search.toLowerCase()) || 
                 u.endereco.toLowerCase().includes(this.search.toLowerCase()) ||
                 u.cidade.toLowerCase().includes(this.search.toLowerCase()) ||
                 u.bairro.toLowerCase().includes(this.search.toLowerCase())
               );
             }
           }"
        x-init="
          document.getElementById('searchUnits').addEventListener('input', (e) => {
            search = e.target.value;
          })
        ">
        <template x-for="unidade in filteredUnidades()" :key="unidade.id">
          <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-lg transition-all border border-gray-200 group"
            data-aos="zoom-in">

            <!-- Header da Unidade -->
            <div class="flex items-start justify-between mb-4">
              <div class="flex-1">
                <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-primary transition-colors"
                  x-text="unidade.nome"></h3>
                <div class="inline-flex items-center bg-primary/10 text-primary px-2 py-1 rounded-full text-sm">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span x-text="unidade.status || 'Funcionando'"></span>
                </div>
              </div>
            </div>

            <!-- Informações da Unidade -->
            <div class="space-y-3 mb-6">
              <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <div>
                  <p class="text-gray-700 font-medium" x-text="unidade.endereco"></p>
                  <p class="text-gray-500 text-sm" x-text="unidade.cidade + ' - ' + unidade.estado"></p>
                </div>
              </div>

              <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <a :href="'tel:' + unidade.telefone" class="text-primary hover:text-primary-dark font-medium" x-text="unidade.telefone"></a>
              </div>

              <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                  <p class="text-gray-700 font-medium">Horário de Funcionamento</p>
                  <p class="text-gray-500 text-sm" x-text="unidade.horario"></p>
                </div>
              </div>
            </div>

            <!-- Ações -->
            <div class="flex flex-col sm:flex-row gap-3">
              <button @click="$dispatch('focus-map', unidade.coordenadas)"
                class="flex-1 bg-primary text-white hover:bg-primary-dark px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Ver no Mapa
              </button>
              <a :href="'https://wa.me/' + unidade.whatsapp + '?text=Olá, gostaria de agendar um exame na unidade ' + unidade.nome"
                target="_blank"
                class="flex-1 border-2 border-green-500 text-green-600 hover:bg-green-500 hover:text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                </svg>
                WhatsApp
              </a>
            </div>

            <!-- Serviços Disponíveis -->
            <div class="mt-4 pt-4 border-t border-gray-100">
              <p class="text-sm text-gray-500 mb-2">Serviços disponíveis:</p>
              <div class="flex flex-wrap gap-1">
                <span class="inline-flex items-center text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                  Coleta
                </span>
                <span class="inline-flex items-center text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                  Resultados
                </span>
                <span class="inline-flex items-center text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full">
                  Atendimento
                </span>
              </div>
            </div>
          </div>
        </template>

        <!-- Mensagem quando não há resultados -->
        <div x-show="filteredUnidades().length === 0" class="col-span-full text-center py-12">
          <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Nenhuma unidade encontrada</h3>
          <p class="text-gray-500">Tente ajustar os termos de busca</p>
        </div>
      </div>
    </x-container>
  </section>

  <!-- Mapa das Unidades -->
  <section id="mapa" class="py-20 bg-gray-50">
    <x-container>
      <div class="text-center mb-16" data-aos="fade-up">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Localização das Unidades</h2>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
          Clique nos marcadores para ver detalhes de cada unidade
        </p>
      </div>

      <!-- Componente do Mapa -->
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden" data-aos="fade-up">
        <x-map-unidades :unidades="$unidades ?? []" />
      </div>
    </x-container>
  </section>

  <!-- Informações Adicionais -->
  <section class="py-16 bg-white">
    <x-container>
      <div class="grid md:grid-cols-3 gap-8">
        <div class="text-center" data-aos="zoom-in">
          <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold mb-2">Horários Flexíveis</h3>
          <p class="text-gray-600">Funcionamento de segunda a sábado com horários estendidos para sua comodidade.</p>
        </div>

        <div class="text-center" data-aos="zoom-in" data-aos-delay="100">
          <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold mb-2">Localização Estratégica</h3>
          <p class="text-gray-600">Unidades bem localizadas com fácil acesso e estacionamento disponível.</p>
        </div>

        <div class="text-center" data-aos="zoom-in" data-aos-delay="200">
          <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold mb-2">Atendimento de Qualidade</h3>
          <p class="text-gray-600">Equipe treinada e equipamentos modernos em todas as nossas unidades.</p>
        </div>
      </div>
    </x-container>
  </section>

  <!-- CTA Final -->
  <section class="py-20 bg-gradient-to-br from-primary to-primary-dark text-white">
    <x-container class="text-center">
      <div data-aos="fade-up">
        <h2 class="text-4xl font-bold mb-6">Precisa de Ajuda para Escolher?</h2>
        <p class="text-lg mb-8 opacity-90 max-w-2xl mx-auto">
          Nossa equipe está pronta para orientar você sobre a unidade mais conveniente
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <x-link href="/orcamento" class="bg-white text-primary hover:bg-gray-100 px-8 py-3 rounded-lg font-semibold transition-colors inline-flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Agendar Exames
          </x-link>
          <x-link href="/chat" class="border-2 border-white text-white hover:bg-white hover:text-primary px-8 py-3 rounded-lg font-semibold transition-colors inline-flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.991 8.991 0 01-4.255-1.165L3 21l2.165-5.59A8.989 8.989 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z" />
            </svg>
            Chat com Assistente
          </x-link>
        </div>
      </div>
    </x-container>
  </section>
</x-app-layout>