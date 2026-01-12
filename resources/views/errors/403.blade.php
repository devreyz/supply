<x-error-layout title="403 - Acesso Negado">
    <div class="text-center px-6 py-12">
        <h1 class="text-9xl font-bold text-text-secondary mb-6 animate-fadeIn">403</h1>
        <p class="text-2xl text-text mb-4">Acesso Negado</p>
        <p class="text-md text-text mb-8">
            Você não tem permissão para acessar esta página.
        </p>
        <a href="{{ url('/') }}" class="inline-block px-8 py-3 bg-button text-white rounded-md hover:bg-gray-700 transition-all duration-300">
            Voltar para Home
        </a>
    </div>
</x-error-layout>