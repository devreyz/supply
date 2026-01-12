<div x-data="{ loading: true }" x-init="$watch('loading', value => value ? $refs.loadingContainer.style.display = 'flex' : $refs.loadingContainer.style.display = 'none')">
    <!-- Container de Loading -->
    <div x-ref="loadingContainer" class="fixed inset-0 bg-white bg-opacity-80 z-50 flex items-center justify-center hidden">
        <div class="text-center">
            <!-- Loader -->
            <div class="w-12 h-12 border-4 border-primary-dark border-t-transparent rounded-full animate-spin mb-4"></div>
            <p class="text-gray-600 font-medium">Carregando...</p>
        </div>
    </div>

    <!-- Conteúdo Principal -->
    <div x-ref="contentContainer" x-show="!loading" class="transition-opacity duration-300">
        {{ $slot }}
    </div>

    <!-- Script para simular carregamento -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const delay = {{ $delay ?? 2000 }}; // Tempo de carregamento em milissegundos
            setTimeout(() => {
                document.querySelector('[x-data]').__x.$data.loading = false;
            }, delay);
        });
    </script>
</div>