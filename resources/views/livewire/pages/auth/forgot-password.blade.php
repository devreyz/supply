<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout(\'layouts.guest\')] class extends Component
{
    /**
     * Recovery logic for social login users.
     */
}; ?>

<div class="space-y-6">
    <div class="text-center">
        <h2 class="text-xl font-semibold text-slate-800">Recuperação de Acesso</h2>
        <p class="text-sm text-slate-500 mt-2">Como você utiliza o login via Google, sua senha deve ser gerenciada diretamente através da sua conta Google.</p>
    </div>

    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 text-sm text-amber-700 leading-relaxed">
        <div class="flex gap-3">
            <i data-lucide="info" class="w-5 h-5 flex-shrink-0"></i>
            <p>O Laboratório Lamarck não armazena sua senha quando você utiliza o acesso pelo Google.</p>
        </div>
    </div>

    <div class="text-center mt-6">
        <a href="{{ route(\'login\') }}" class="inline-flex items-center gap-2 text-sm text-rose-600 hover:text-rose-500 font-medium tracking-tight">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Voltar para o Login
        </a>
    </div>
</div>