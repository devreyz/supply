<x-app-layout title="Unidades - Laboratório Lamarck">
  <x-breadcrumb :links="[
    ['url' => '', 'label' => 'Unidades']]" />

  <?php
  // Dados das unidades integrados no código
  $units = [
    [
      'id' => 1,
      'nome' => 'Unidade Centro',
      'endereco' => 'Rua Principal, 123',
      'bairro' => 'Centro',
      'cidade' => 'Sua Cidade',
      'estado' => 'MG',
      'cep' => '30000-000',
      'telefone' => '(31) 3333-4444',
      'whatsapp' => '5531999999999',
      'email' => 'centro@lamarck.com.br',
      'especialidade' => 'Análises Clínicas',
      'horarios' => [
        ['dias' => 'Segunda a Sexta', 'horas' => '06:00 às 16:00'],
        ['dias' => 'Sábado', 'horas' => '06:00 às 12:00'],
        ['dias' => 'Domingo', 'horas' => 'Fechado']
      ],
      'servicos' => ['Coleta', 'Resultados', 'Atendimento']
    ],
    [
      'id' => 2,
      'nome' => 'Unidade Bairro Norte',
      'endereco' => 'Av. Norte, 456',
      'bairro' => 'Bairro Norte',
      'cidade' => 'Sua Cidade',
      'estado' => 'MG',
      'cep' => '30100-000',
      'telefone' => '(31) 3333-5555',
      'whatsapp' => '5531888888888',
      'email' => 'norte@lamarck.com.br',
      'especialidade' => 'Análises Clínicas',
      'horarios' => [
        ['dias' => 'Segunda a Sexta', 'horas' => '07:00 às 17:00'],
        ['dias' => 'Sábado', 'horas' => '07:00 às 13:00'],
        ['dias' => 'Domingo', 'horas' => 'Fechado']
      ],
      'servicos' => ['Coleta', 'Resultados', 'Atendimento']
    ]
  ];
  ?>

  <!-- Page Header -->
  <section class="bg-gradient-to-r from-primary to-primary-dark text-white py-12">
    <div class="container mx-auto px-4">
      <div class="max-w-3xl mx-auto text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Nossas Unidades</h1>
        <p class="text-xl opacity-90">
          Encontre a unidade mais próxima de você para atendimento personalizado
        </p>
      </div>
    </div>
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

  <!-- Units Grid -->
  <section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
      <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8"
          x-data="{
               unidades: <?php echo json_encode($units); ?>,
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

          <template x-for="unit in filteredUnidades()" :key="unit.id">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
              <!-- Unit Header -->
              <div class="bg-gradient-to-r from-primary to-primary-dark text-white p-6">
                <h2 class="text-2xl font-bold mb-2" x-text="unit.nome"></h2>
                <span class="inline-block bg-white/20 px-3 py-1 rounded-full text-sm"
                  x-text="unit.especialidade">
                </span>
              </div>

              <!-- Unit Info -->
              <div class="p-6">
                <!-- Address -->
                <div class="flex items-start mb-4">
                  <svg class="w-5 h-5 text-primary mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                  </svg>
                  <div>
                    <p class="font-medium text-gray-900" x-text="unit.endereco"></p>
                    <p class="text-gray-600" x-text="unit.bairro + ' - ' + unit.cidade"></p>
                    <p class="text-gray-600" x-text="'CEP: ' + unit.cep"></p>
                  </div>
                </div>

                <!-- Phone -->
                <div class="flex items-center mb-4">
                  <svg class="w-5 h-5 text-primary mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                  </svg>
                  <a :href="'tel:' + unit.telefone"
                    class="text-gray-900 hover:text-primary font-medium"
                    x-text="unit.telefone">
                  </a>
                </div>

                <!-- Email -->
                <div class="flex items-center mb-4">
                  <svg class="w-5 h-5 text-primary mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                  <a :href="'mailto:' + unit.email"
                    class="text-gray-900 hover:text-primary"
                    x-text="unit.email">
                  </a>
                </div>

                <!-- Operating Hours -->
                <div class="mb-6">
                  <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Horários de Funcionamento
                  </h4>
                  <div class="space-y-1 text-sm">
                    <template x-for="horario in unit.horarios" :key="horario.dias">
                      <div class="flex justify-between">
                        <span class="text-gray-600" x-text="horario.dias + ':'"></span>
                        <span class="font-medium text-gray-900" x-text="horario.horas"></span>
                      </div>
                    </template>
                  </div>
                </div>

                <!-- Services -->
                <div class="mb-6">
                  <h4 class="font-semibold text-gray-900 mb-3">Serviços Disponíveis</h4>
                  <div class="flex flex-wrap gap-2">
                    <template x-for="servico in unit.servicos" :key="servico">
                      <span class="bg-primary/10 text-primary text-xs font-medium px-2.5 py-0.5 rounded"
                        x-text="servico">
                      </span>
                    </template>
                  </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3">
                  <a :href="'https://wa.me/' + unit.whatsapp + '?text=Olá! Gostaria de agendar um exame na unidade ' + unit.nome"
                    target="_blank"
                    class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center flex-1">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.108" />
                    </svg>
                    Agendar WhatsApp
                  </a>
                  <button @click="openMap(unit.endereco + ', ' + unit.cidade)"
                    class="border-2 border-primary text-primary hover:bg-primary hover:text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center flex-1">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Ver no Mapa
                  </button>
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
      </div>
    </div>
  </section>

  <!-- Interactive Map -->
  <section class="py-16 bg-white">
    <div class="container mx-auto px-4">
      <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Localização das Unidades</h2>
          <p class="text-xl text-gray-600">
            Encontre a unidade mais próxima de você no mapa interativo
          </p>
        </div>

        <div class="bg-gray-200 rounded-xl overflow-hidden" style="height: 500px;">
          <div id="units-map" class="w-full h-full flex items-center justify-center">
            <div class="text-center">
              <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7" />
              </svg>
              <p class="text-gray-600">Mapa Interativo</p>
              <p class="text-sm text-gray-500">Clique em "Ver no Mapa" para visualizar a localização específica</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact CTA -->
  <section class="py-16 bg-primary/5">
    <div class="container mx-auto px-4">
      <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Precisa de Mais Informações?</h2>
        <p class="text-xl text-gray-600 mb-8">
          Nossa equipe está pronta para esclarecer suas dúvidas e ajudar com agendamentos
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white rounded-lg p-6 shadow-md">
            <div class="bg-primary/10 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
              </svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">Central de Atendimento</h3>
            <p class="text-gray-600 text-sm mb-3">Ligue para nossa central</p>
            <a href="tel:(31)3333-4444"
              class="text-primary font-medium hover:underline">
              (31) 3333-4444
            </a>
          </div>

          <div class="bg-white rounded-lg p-6 shadow-md">
            <div class="bg-green-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.108" />
              </svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">WhatsApp</h3>
            <p class="text-gray-600 text-sm mb-3">Converse conosco</p>
            <a href="https://wa.me/5531999999999?text=Olá! Gostaria de informações sobre as unidades"
              target="_blank"
              class="text-green-600 font-medium hover:underline">
              Iniciar Conversa
            </a>
          </div>

          <div class="bg-white rounded-lg p-6 shadow-md">
            <div class="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">E-mail</h3>
            <p class="text-gray-600 text-sm mb-3">Envie sua mensagem</p>
            <a href="mailto:contato@lamarck.com.br"
              class="text-blue-600 font-medium hover:underline">
              contato@lamarck.com.br
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    function openMap(address) {
      const encodedAddress = encodeURIComponent(address);
      const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodedAddress}`;
      window.open(googleMapsUrl, '_blank');
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
      console.log('Units page loaded');
    });
  </script>
</x-app-layout>