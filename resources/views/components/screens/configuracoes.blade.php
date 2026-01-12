{{-- 
    Página: Configurações
    Persistência de preferências do usuário
--}}
<section id="page-configuracoes" class="page" data-level="primary">
    {{-- Header --}}
    <header class="app-header">
        <button class="icon-btn" data-back>
            <i data-lucide="arrow-left"></i>
        </button>
        
        <div class="header-title">Configurações</div>
        
        <div class="w-11"></div>
    </header>

    {{-- Content --}}
    <div class="p-6 space-y-6">
        {{-- Seção: Aparência --}}
        <div>
            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">Aparência</h3>
            
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100">
                {{-- Animação de Transição --}}
                <div class="p-4 border-b border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center">
                                <i data-lucide="sparkles" class="w-5 h-5 text-rose-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">Animação de Transição</p>
                                <p class="text-sm text-slate-500">Estilo ao navegar entre páginas</p>
                            </div>
                        </div>
                    </div>
                    
                    <select id="animation-select" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 font-medium focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-100" onchange="mudarAnimacao(this.value)">
                        <option value="fade">Fade (Padrão)</option>
                        
                    </select>
                </div>

                {{-- Tema (Futuro) --}}
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                                <i data-lucide="moon" class="w-5 h-5 text-slate-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">Modo Escuro</p>
                                <p class="text-sm text-slate-500">Em breve</p>
                            </div>
                        </div>
                        <div class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-full">
                            Em breve
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Seção: Notificações --}}
        <div>
            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">Notificações</h3>
            
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100">
                {{-- Push Notifications --}}
                <div class="p-4 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                                <i data-lucide="bell" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">Notificações Push</p>
                                <p class="text-sm text-slate-500">Receba alertas de resultados</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="push-notifications-toggle" class="sr-only peer" onchange="toggleNotifications(this.checked)">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-rose-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600"></div>
                        </label>
                    </div>
                </div>

                {{-- Email Notifications --}}
                <div class="p-4 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                                <i data-lucide="mail" class="w-5 h-5 text-purple-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">Email</p>
                                <p class="text-sm text-slate-500">Notificações por email</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="email-notifications-toggle" class="sr-only peer" onchange="toggleEmailNotifications(this.checked)">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-rose-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600"></div>
                        </label>
                    </div>
                </div>

                {{-- WhatsApp Notifications --}}
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                                <i data-lucide="message-circle" class="w-5 h-5 text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">WhatsApp</p>
                                <p class="text-sm text-slate-500">Receba via WhatsApp</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="whatsapp-notifications-toggle" class="sr-only peer" onchange="toggleWhatsAppNotifications(this.checked)">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-rose-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Seção: Privacidade --}}
        <div>
            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">Privacidade</h3>
            
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100">
                <button id="btn-termos-privacidade" class="w-full p-4 flex items-center justify-between text-left hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                            <i data-lucide="shield-check" class="w-5 h-5 text-amber-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">Termos e Privacidade</p>
                            <p class="text-sm text-slate-500">LGPD e uso de dados</p>
                        </div>
                    </div>
                    <i data-lucide="chevron-right" class="w-5 h-5 text-slate-400"></i>
                </button>
            </div>
        </div>

        {{-- Seção: Sobre --}}
        <div>
            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">Sobre</h3>
            
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100">
                <div class="p-4 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="flask-conical" class="w-8 h-8 text-white"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-lg">Laboratório Lamarck</h4>
                    <p class="text-sm text-slate-500 mt-1">Versão 2.0.0</p>
                    <p class="text-xs text-slate-400 mt-2">© 2024 Todos os direitos reservados</p>
                </div>
            </div>
        </div>

        {{-- Botão Limpar Dados --}}
        <button id="btn-limpar-dados" class="w-full p-4 bg-red-50 text-red-600 rounded-2xl font-semibold hover:bg-red-100 transition-colors border-2 border-red-100">
            <i data-lucide="trash-2" class="inline w-5 h-5 mr-2"></i>
            Limpar Todos os Dados
        </button>
    </div>
</section>
