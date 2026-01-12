<?php

use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout(\'layouts.guest\')] class extends Component
{
    public string $password = \'\';

    /**
     * Confirm the current user\'s password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            \'password\' => [\'required\', \'string\'],
        ]);

        if (! Auth::guard(\'web\')->validate([
            \'email\' => Auth::user()->email,
            \'password\' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                \'password\' => __(\'auth.password\'),
            ]);
        }

        session([\'auth.password_confirmed_at\' => time()]);

        $this->redirectIntended(default: RouteServiceProvider::HOME, navigate: true);
    }
}; ?>

<div class="space-y-6">
    <div class="text-center">
        <h2 class="text-xl font-semibold text-slate-800">Área Segura</h2>
        <p class="text-sm text-slate-500 mt-2">Esta é uma área segura da aplicação. Por favor, confirme sua identidade antes de continuar.</p>
    </div>

    <form wire:submit="confirmPassword" class="space-y-6">
        <!-- Password -->
        <div class="space-y-2">
            <x-input-label for="password" :value="__(\'Senha\')" />

            <x-text-input wire:model="password"
                id="password"
                class="block mt-1 w-full rounded-2xl border-slate-200 focus:ring-rose-500 focus:border-rose-500"
                type="password"
                name="password"
                required autocomplete="current-password" />

            <x-input-error :messages="$errors->get(\'password\')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-3">
            <button class="flex items-center justify-center gap-2 w-full px-6 py-4 bg-gradient-to-r from-rose-500 to-pink-500 text-white rounded-2xl font-bold shadow-lg hover:shadow-xl transition-all hover:scale-[1.02] active:scale-[0.98]">
                Confirmar
            </button>
            <a href="{{ route(\'login\') }}" class="text-center text-sm text-slate-500 hover:text-rose-600 font-medium tracking-tight mt-2">
                Cancelar
            </a>
        </div>
    </form>
</div>