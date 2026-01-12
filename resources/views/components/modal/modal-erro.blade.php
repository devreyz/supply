{{-- 
    Template: Modal Erro
    Exibe mensagem de erro genérica
--}}
<template id="tpl-modal-erro">
    <div class="p-6 text-center">
        {{-- Ícone de Erro --}}
        <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
            <svg class="w-10 h-10 text-red-600 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>

        {{-- Título --}}
        <h2 class="text-xl font-bold text-slate-900 mb-2">Exame não encontrado</h2>
        
        {{-- Mensagem --}}
        <p class="text-slate-600 mb-6">
            Não conseguimos localizar um resultado com o código informado. Verifique se digitou corretamente ou se o prazo de liberação já passou.
        </p>

        {{-- Dicas --}}
        <div class="bg-slate-50 rounded-xl p-4 text-left mb-6">
            <p class="text-sm font-medium text-slate-700 mb-2">Verifique:</p>
            <ul class="text-sm text-slate-600 space-y-1">
                <li class="flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4 text-green-500"></i>
                    O código está digitado corretamente
                </li>
                <li class="flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4 text-green-500"></i>
                    O exame já foi processado
                </li>
                <li class="flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4 text-green-500"></i>
                    O código é do Laboratório Lamarck
                </li>
            </ul>
        </div>

        {{-- Ações --}}
        <div class="space-y-3">
            <button class="btn btn-primary btn-block" data-close-modal="retry">
                <i data-lucide="refresh-cw"></i>
                Tentar Novamente
            </button>

            <button class="btn btn-outline btn-block" onclick="contactSupport()">
                <i data-lucide="message-circle"></i>
                Falar com Suporte
            </button>
        </div>
    </div>
</template>

@push('scripts')
<script>
    function contactSupport() {
        app.closeTopOverlay();
        window.open('https://wa.me/5511999999999?text=Olá! Preciso de ajuda para encontrar meu resultado de exame.', '_blank');
    }
</script>
@endpush
