<!-- resources/views/contato.blade.php -->
<x-app-layout title="Contato">
  <x-breadcrumb :links="[
    ['url' => '', 'label' => 'Contato']]" />

  <section class="py-20 bg-white">
    <x-container>
      <div class="max-w-lg mx-auto text-center">
        <h1 class="text-3xl font-bold mb-4">Fale Conosco</h1>
        <p class="text-text-secondary mb-8">
          Entre em contato conosco via WhatsApp para tirar dúvidas ou agendar um atendimento.
        </p>
        <livewire:components.secondary-button-contact />
      </div>
    </x-container>
  </section>

  <livewire:orcamento.cta-whatsapp />
</x-app-layout>