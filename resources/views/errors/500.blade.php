<x-error-layout title="500 - Erro Interno do Servidor">
    <div class="text-center px-6 py-12">
        <h1 class="text-9xl font-bold text-text-secondary mb-6 animate-fadeIn">500</h1>
        <p class="text-2xl text-text mb-4">Erro Interno do Servidor</p>
        <p class="text-md text-text mb-8">
            Algo deu errado no servidor. Estamos trabalhando para corrigir isso.
        </p>
        <a href="{{ url('/') }}" class="inline-block px-8 py-3 bg-button text-white rounded-md hover:bg-gray-700 transition-all duration-300">
            Voltar para Home
        </a>
    </div>
</x-error-layout>