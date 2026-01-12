<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout(\'layouts.guest\')] class extends Component
{
    // Simplified to Google only
}; ?>

<div class="space-y-6">
    <div class="text-center">
        <h2 class="text-xl font-semibold text-slate-800">Crie sua conta</h2>
        <p class="text-sm text-slate-500 mt-2">A forma mais rápida de começar é utilizando sua conta Google.</p>
    </div>

    <div class="flex flex-col gap-4 mt-6">
        <a href="{{ route(\'login.google\') }}" class="flex items-center justify-center gap-3 w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl font-medium text-slate-700 shadow-sm hover:shadow-md hover:bg-slate-50 transition-all hover:scale-[1.02] active:scale-[0.98]">
            <svg class="w-6 h-6" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l3.66-2.84z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            Registrar com Google
        </a>
    </div>

    <div class="text-center mt-6">
        <a href="{{ route(\'login\') }}" class="text-sm text-rose-600 hover:text-rose-500 font-medium tracking-tight">Já tem uma conta? Entrar</a>
    </div>

    <div class="mt-8 text-xs text-center text-slate-400 leading-relaxed">
        <p>Ao se registrar, você concorda com nossos <a href="#" class="underline hover:text-rose-500">Termos de Serviço</a> e <a href="#" class="underline hover:text-rose-500">Política de Privacidade</a>.</p>
    </div>
</div>