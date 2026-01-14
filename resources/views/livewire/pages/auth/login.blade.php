<x-guest-layout >
    <div class="text-center">
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Bem-vindo de volta!</h2>
        <p class="text-slate-500 text-sm mt-2 font-medium">Acesse sua conta ZeTools para gerenciar o Gôndola.</p>
    </div>

    <!-- Mensagens -->
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-100 rounded-xl text-red-700 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-700 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col gap-4 mt-6">
        <a href="{{ route('auth.zetools') }}" class="flex items-center justify-center gap-3 w-full px-6 py-4 bg-white border border-slate-200 rounded-2xl font-semibold text-slate-700 shadow-sm hover:shadow-md hover:bg-slate-50 transition-all hover:scale-[1.02] active:scale-[0.98]">
            <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                <path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="2"/>
            </svg>
            Entrar
        </a>

        <a href="http://127.0.0.1:8000/auth/switch?callback=http://127.0.0.1:8001/auth/zetools" class="flex items-center justify-center gap-2 w-full px-6 py-3 bg-slate-50 text-slate-600 rounded-2xl font-medium hover:bg-slate-100 transition-colors border border-transparent hover:border-slate-200">
            Entrar em outra conta
        </a>

        <div class="relative py-2">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-100"></div>
            </div>
            <div class="relative flex justify-center text-xs uppercase">
                <span class="bg-white px-4 text-slate-400 font-bold tracking-widest">OU</span>
            </div>
        </div>

        <a href="{{ route('register') }}" class="flex items-center justify-center gap-2 w-full px-6 py-3 bg-slate-50 text-slate-600 rounded-2xl font-semibold hover:bg-slate-100 transition-colors border border-transparent hover:border-slate-200">
            Criar nova conta
        </a>

    </div>

    <div class="mt-6 text-xs text-center text-slate-400 leading-relaxed font-medium">
        <p>Ao entrar, você concorda com nossos <a href="#" class="text-indigo-500 hover:text-indigo-600 underline decoration-indigo-500/30">Termos de Serviço</a> e <a href="#" class="text-indigo-500 hover:text-indigo-600 underline decoration-indigo-500/30">Política de Privacidade</a>.</p>
    </div>
</x-guest-layout>