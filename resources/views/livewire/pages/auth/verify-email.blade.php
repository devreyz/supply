<?php

use App\Livewire\Actions\Logout;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout(\'layouts.guest\')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: RouteServiceProvider::HOME, navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash(\'status\', \'verification-link-sent\');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect(\'/\', navigate: true);
    }
}; ?>

<div class="space-y-6">
    <div class="text-center">
        <h2 class="text-xl font-semibold text-slate-800">Verifique seu e-mail</h2>
        <p class="text-sm text-slate-500 mt-2">Obrigado por se juntar a nós! Por favor, verifique seu endereço de e-mail clicando no link que acabamos de enviar para você.</p>
    </div>

    @if (session(\'status\') == \'verification-link-sent\')
    <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 text-sm text-emerald-700 leading-relaxed">
        <p>Um novo link de verificação foi enviado para o endereço de e-mail fornecido.</p>
    </div>
    @endif

    <div class="flex flex-col gap-3 mt-6">
        <button wire:click="sendVerification" class="flex items-center justify-center gap-2 w-full px-6 py-4 bg-gradient-to-r from-rose-500 to-pink-500 text-white rounded-2xl font-bold shadow-lg hover:shadow-xl transition-all hover:scale-[1.02] active:scale-[0.98]">
            <i data-lucide="mail" class="w-5 h-5"></i>
            Reenviar e-mail de verificação
        </button>

        <button wire:click="logout" class="text-sm text-slate-500 hover:text-rose-600 font-medium tracking-tight mt-2">
            Sair da conta
        </button>
    </div>
</div>