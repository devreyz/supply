{{-- 
    Widget: Acesso Rápido
    Input para código de acesso ao resultado
--}}
<div class="bento-widget span-full" style="cursor: default;">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center">
            <i data-lucide="file-search" class="w-5 h-5 text-rose-600"></i>
        </div>
        <div>
            <h3 class="font-semibold text-slate-900">Resultado Online</h3>
            <p class="text-sm text-slate-500">Digite seu código de acesso ou escaneie o QR</p>
        </div>
    </div>
    
    <form onsubmit="buscarResultado(event)" class="space-y-3">
        <div class="flex gap-2">
            <input 
                type="text" 
                id="codigo-acesso"
                class="input-field input-field-lg flex-1" 
                placeholder="ABC123"
                maxlength="10"
                autocomplete="off"
                autocapitalize="characters"
            >

            <button type="button" class="btn btn-secondary w-16 p-3 rounded-xl" onclick="app.openSheet('tpl-sheet-scanner')" title="Abrir scanner">
                <i data-lucide="qr-code" class="w-6 h-6"></i>
            </button>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block btn-lg">
            <i data-lucide="search"></i>
            Ver Resultado
        </button>
    </form>
</div>

@push('scripts')
<script>
    async function buscarResultado(event) {
        event.preventDefault();
        
        const codigo = document.getElementById('codigo-acesso').value.trim().toUpperCase();
        
        if (!codigo) {
            app.toast('Digite o código de acesso', 'warning');
            return;
        }

        if (codigo.length < 4) {
            app.toast('Código muito curto', 'warning');
            return;
        }

        app.showLoading();

        try {
            // Simula busca na API
            await new Promise(resolve => setTimeout(resolve, 1500));

            // Simula resultado encontrado ou não (50% de chance)
            const found = Math.random() > 0.3;

            app.hideLoading();

            if (found) {
                // Salva código no state
                if (app.vdom) {
                    app.vdom.setState('resultado', { 
                        code: codigo,
                        patient: 'Maria da Silva',
                        date: new Date().toLocaleDateString('pt-BR')
                    });
                }
                
                // Navega para resultado
                app.go('resultado');
                
                // Limpa input
                document.getElementById('codigo-acesso').value = '';
            } else {
                // Mostra modal de erro
                app.openModal('tpl-modal-erro');
            }

        } catch (error) {
            app.hideLoading();
            app.toast('Erro ao buscar resultado', 'error');
        }
    }
</script>
@endpush
