<!-- resources/views/convenios.blade.php -->
<x-app-layout title="Convênios e Planos de Saúde">
  <!-- Breadcrumb -->
  <x-breadcrumb :links="[
    ['url' => '', 'label' => 'Convênios']]" />


  <!-- Hero Convênios -->
  <section class="relative py-28 bg-gradient-to-b from-primary/10 to-white">
    <div class="absolute inset-0  bg-cover opacity-20"></div>
    <x-container class="relative text-center">
      <div class="max-w-4xl mx-auto" data-aos="fade-up">
        <h1 class="text-5xl font-bold text-text mb-6">Convênios e <span class="bg-gradient-to-r from-primary to-primary-dark bg-clip-text text-transparent">Planos de Saúde</span></h1>
        <p class="text-xl text-text-secondary mb-8">Atendemos os principais planos de saúde do Brasil com qualidade e agilidade</p>
        <div class="flex justify-center gap-4">
          <x-link href="#lista-convenios" class="btn-primary">Convênios</x-link>
          <livewire:components.secondary-button-contact />

        </div>
      </div>
    </x-container>
  </section>

  <!-- Lista de Convênios -->
  <section id="lista-convenios" class="py-20 bg-white">
    <x-container>
      <div class="text-center mb-16" data-aos="fade-up">
        <h2 class="text-3xl font-bold mb-4">Convênios Parceiros</h2>
        <p class="text-text-secondary max-w-2xl mx-auto">Nossos principais parceiros em planos de saúde</p>
      </div>


      <!-- Grid de Convênios -->
      <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-6"
        x-data="{
                     convenios: [
                         { nome: 'Bom Pastor', logo: 'bom-pastor.png', categoria: 'Regional' },
                         // Adicionar mais convênios
                     ],
                     search: ''
                 }"
        x-init="filtered = convenios">
        <template x-for="convenio in convenios.filter(c => c.nome.toLowerCase().includes(search.toLowerCase()))" :key="convenio.nome">
          <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-all border border-border/20"
            data-aos="zoom-in">
            <div class="h-24 flex items-center justify-center mb-4">
              <img :src="`/img/convenios/${convenio.logo}`"
                :alt="convenio.nome"
                class="max-h-full max-w-[160px] object-contain">
            </div>
            <h3 class="text-center font-semibold mb-2" x-text="convenio.nome"></h3>
            <span class="block text-center text-sm text-text-secondary" x-text="convenio.categoria"></span>
          </div>
        </template>
      </div>
    </x-container>
  </section>

  <!-- Como Funciona -->
  <section class="py-20 bg-gray-50">
    <x-container>
      <div class="max-w-5xl mx-auto">
        <div class="text-center mb-16" data-aos="fade-up">
          <h2 class="text-3xl font-bold mb-4">Como Usar Seu Convênio</h2>
          <p class="text-text-secondary">Passo a passo para utilizar seu plano de saúde</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <!-- Passo 1 -->
          <div class="bg-white p-6 rounded-xl shadow-sm" data-aos="fade-up">
            <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center text-white mb-4">1</div>
            <h3 class="text-xl font-semibold mb-2">Verifique a Cobertura</h3>
            <p class="text-text-secondary">Confira se seu exame está coberto pelo plano</p>
          </div>

          <!-- Passo 2 -->
          <div class="bg-white p-6 rounded-xl shadow-sm" data-aos="fade-up" data-aos-delay="100">
            <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center text-white mb-4">2</div>
            <h3 class="text-xl font-semibold mb-2">Agendamento</h3>
            <p class="text-text-secondary">Agende online ou por telefone</p>
          </div>

          <!-- Passo 3 -->
          <div class="bg-white p-6 rounded-xl shadow-sm" data-aos="fade-up" data-aos-delay="200">
            <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center text-white mb-4">3</div>
            <h3 class="text-xl font-semibold mb-2">Realização do Exame</h3>
            <p class="text-text-secondary">Compareça com documentos e carteirinha</p>
          </div>
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

  <!-- CTA Final -->
  <section class="py-20 bg-gradient-to-br from-primary to-primary-dark text-white">
    <x-container class="text-center">
      <h2 class="text-4xl font-bold mb-6" data-aos="fade-up">Não Encontrou Seu Convênio?</h2>
      <p class="text-lg mb-8 opacity-90" data-aos="fade-up" data-aos-delay="100">Entre em contato para verificar outras formas de atendimento</p>
      <div class="flex justify-center gap-4" data-aos="fade-up" data-aos-delay="200">
        <x-link href="/unidades" class="btn-primary">Encontrar Unidade</x-link>
        <livewire:components.secondary-button-contact />
      </div>
    </x-container>
  </section>


  <!-- CTA WhatsApp -->
  <livewire:orcamento.cta-whatsapp />
</x-app-layout>