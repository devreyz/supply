<x-error-layout title="503 - Serviço Indisponível">
    <div class="text-center px-6 py-12">
        <h1 class="text-9xl font-bold text-text-secondary mb-6 animate-fadeIn">503</h1>
        <p class="text-2xl text-text mb-4">Serviço Indisponível</p>
        <p class="text-md text-text mb-8">
            O servidor está temporariamente indisponível. Tente novamente mais tarde.
        </p>
        <a href="{{ url('/') }}" class="inline-block px-8 py-3 bg-button text-white rounded-md hover:bg-gray-700 transition-all duration-300">
            Voltar para Home
        </a>
    </div>
</x-error-layout>