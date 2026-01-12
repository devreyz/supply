{{-- 
    Drawer: Menu Principal
    Menu lateral de navegação
--}}
<div id="tpl-drawer-menu" class="drawer" data-template-id="drawer-menu">
    <div class="drawer-content">
        {{-- Header --}}
        <div class="p-6 pt-8 bg-gradient-to-br from-rose-600 to-rose-700 text-white">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                    <i data-lucide="flask-conical" class="w-7 h-7"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold">Laboratório Lamarck</h2>
                    <p class="text-sm text-rose-100">Excelência em Diagnósticos</p>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto p-4">
            {{-- Menu Principal --}}
            <nav class="space-y-1">
                <button class="w-full flex items-center gap-3 p-4 text-left rounded-xl hover:bg-slate-100 transition-colors" onclick="app.go('home'); app.closeTopOverlay();">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="home" class="w-5 h-5 text-rose-600"></i>
                    </div>
                    <span class="font-medium text-slate-900">Início</span>
                </button>

                <button class="w-full flex items-center gap-3 p-4 text-left rounded-xl hover:bg-slate-100 transition-colors" onclick="app.go('exames-lista'); app.closeTopOverlay();">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="flask-conical" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <span class="font-medium text-slate-900">Nossos Exames</span>
                </button>

                <button class="w-full flex items-center gap-3 p-4 text-left rounded-xl hover:bg-slate-100 transition-colors" onclick="app.go('agendamento'); app.closeTopOverlay();">
                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="calendar" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <span class="font-medium text-slate-900">Agendar Exame</span>
                </button>

                <button class="w-full flex items-center gap-3 p-4 text-left rounded-xl hover:bg-slate-100 transition-colors" onclick="app.openSheet('tpl-sheet-contato'); app.closeTopOverlay();">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="map-pin" class="w-5 h-5 text-purple-600"></i>
                    </div>
                    <span class="font-medium text-slate-900">Localização</span>
                </button>
            </nav>

            {{-- Divisor --}}
            <div class="my-4 border-t border-slate-200"></div>

            {{-- Informações --}}
            <nav class="space-y-1">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 mb-2">Informações</p>

                <button class="w-full flex items-center gap-3 p-4 text-left rounded-xl hover:bg-slate-100 transition-colors" onclick="app.openDrawer('tpl-drawer-convenios'); app.closeDrawer('tpl-drawer-menu');">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="shield-check" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <span class="font-medium text-slate-900">Convênios Aceitos</span>
                </button>

                <button class="w-full flex items-center gap-3 p-4 text-left rounded-xl hover:bg-slate-100 transition-colors" onclick="app.toast('Em breve!', 'info'); app.closeTopOverlay();">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="building-2" class="w-5 h-5 text-slate-600"></i>
                    </div>
                    <span class="font-medium text-slate-900">Sobre o Laboratório</span>
                </button>

                <button class="w-full flex items-center gap-3 p-4 text-left rounded-xl hover:bg-slate-100 transition-colors" onclick="app.toast('Em breve!', 'info'); app.closeTopOverlay();">
                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="map" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <span class="font-medium text-slate-900">Unidades</span>
                </button>
            </nav>

            {{-- Divisor --}}
            <div class="my-4 border-t border-slate-200"></div>

            {{-- Redes Sociais --}}
            <nav class="space-y-1">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider px-4 mb-2">Redes Sociais</p>

                <div class="flex items-center gap-2 px-4">
                    <a href="https://instagram.com/lamarcklab" target="_blank" class="w-10 h-10 rounded-xl bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center hover:scale-110 transition-transform">
                        <i data-lucide="instagram" class="w-5 h-5 text-white"></i>
                    </a>
                    <a href="https://facebook.com/lamarcklab" target="_blank" class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center hover:scale-110 transition-transform">
                        <i data-lucide="facebook" class="w-5 h-5 text-white"></i>
                    </a>
                    <a href="https://linkedin.com/company/lamarcklab" target="_blank" class="w-10 h-10 rounded-xl bg-blue-700 flex items-center justify-center hover:scale-110 transition-transform">
                        <i data-lucide="linkedin" class="w-5 h-5 text-white"></i>
                    </a>
                    <a href="https://youtube.com/@lamarcklab" target="_blank" class="w-10 h-10 rounded-xl bg-red-600 flex items-center justify-center hover:scale-110 transition-transform">
                        <i data-lucide="youtube" class="w-5 h-5 text-white"></i>
                    </a>
                </div>
            </nav>

            {{-- Divisor --}}
            <div class="my-4 border-t border-slate-200"></div>

            {{-- Configurações --}}
            <button class="w-full flex items-center gap-3 p-4 text-left rounded-xl hover:bg-slate-100 transition-colors" onclick="app.go('configuracoes'); app.closeTopOverlay();">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="settings" class="w-5 h-5 text-amber-600"></i>
                </div>
                <span class="font-medium text-slate-900">Configurações</span>
            </button>
        </div>
    </div>
</div>

        {{-- Legal --}}
        <nav class="space-y-1">
            <button class="list-item w-full text-left rounded-xl" onclick="openPrivacidade()">
                <div class="list-item-icon">
                    <i data-lucide="lock" class="text-slate-500"></i>
                </div>
                <span class="list-item-title text-slate-600">Política de Privacidade</span>
            </button>

            <button class="list-item w-full text-left rounded-xl" onclick="openTermos()">
                <div class="list-item-icon">
                    <i data-lucide="file-text" class="text-slate-500"></i>
                </div>
                <span class="list-item-title text-slate-600">Termos de Uso</span>
            </button>
        </nav>

        {{-- Área Médica --}}
        <div class="mt-6 p-4 bg-slate-50 rounded-xl">
            <p class="text-sm text-slate-600 mb-3">Área restrita para profissionais de saúde</p>
            <a href="/admin" class="btn btn-secondary btn-block text-sm">
                <i data-lucide="user-cog"></i>
                Área Médica
            </a>
        </div>

        {{-- Versão --}}
        <p class="text-center text-xs text-slate-400 mt-6">
            Versão 1.0.0 • © 2024 Lamarck
        </p>
    </div>
</template>

@push('scripts')
<script>
    function openConvenios() {
        app.closeTopOverlay();
        setTimeout(() => app.openSheet('tpl-drawer-convenios'), 350);
    }

    function openSobre() {
        app.closeTopOverlay();
        app.openModal({
            type: 'alert',
            title: 'Sobre o Lamarck',
            message: 'Há mais de 20 anos oferecendo exames laboratoriais com qualidade, precisão e atendimento humanizado. Contamos com equipamentos de última geração e uma equipe de profissionais altamente qualificados.',
            confirmText: 'Entendi'
        });
    }

    function openUnidades() {
        app.closeTopOverlay();
        app.toast('Em breve: lista de unidades', 'info');
    }

    function openPrivacidade() {
        app.closeTopOverlay();
        app.openModal({
            type: 'alert',
            title: 'Política de Privacidade',
            message: 'Seus dados são protegidos conforme a Lei Geral de Proteção de Dados (LGPD). Utilizamos suas informações apenas para a prestação dos nossos serviços de análises clínicas.',
            confirmText: 'Entendi'
        });
    }

    function openTermos() {
        app.closeTopOverlay();
        app.openModal({
            type: 'alert',
            title: 'Termos de Uso',
            message: 'Ao utilizar nossos serviços, você concorda com os termos e condições estabelecidos pelo Laboratório Lamarck para a realização de exames e acesso aos resultados online.',
            confirmText: 'Entendi'
        });
    }
</script>
@endpush
