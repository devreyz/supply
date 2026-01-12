<x-error-layout title="419 - Sessão Expirada">
    <div class="text-center px-6 py-12">
        <h1 class="text-9xl font-bold text-text-secondary mb-6 animate-fadeIn">419</h1>
        <p class="text-2xl text-text mb-4">Sessão Expirada</p>
        <p class="text-md text-text mb-8">
            Sua sessão expirou. Por favor, faça login novamente.
        </p>
        <a href="{{ route('login') }}" class="inline-block px-8 py-3 bg-button text-white rounded-md hover:bg-gray-700 transition-all duration-300">
            Fazer Login
        </a>
    </div>
</x-error-layout>