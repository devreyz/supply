{{-- 
    Template: Modal Termo LGPD
    Aceite de termos de privacidade (primeiro acesso)
--}}
<template id="tpl-modal-termo" data-close-on-backdrop="false">
    <div class="p-6">
        {{-- Ícone --}}
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-blue-100 flex items-center justify-center">
            <i data-lucide="shield-check" class="w-8 h-8 text-blue-600"></i>
        </div>

        {{-- Título --}}
        <h2 class="text-xl font-bold text-slate-900 text-center mb-4">Privacidade e Proteção de Dados</h2>
        
        {{-- Conteúdo --}}
        <div class="max-h-60 overflow-y-auto text-sm text-slate-600 space-y-3 mb-6">
            <p>
                O <strong>Laboratório Lamarck</strong> está comprometido com a proteção dos seus dados pessoais, em conformidade com a Lei Geral de Proteção de Dados (LGPD - Lei nº 13.709/2018).
            </p>
            
            <p>
                <strong>Dados coletados:</strong> Nome, CPF, data de nascimento, contato e informações de saúde relacionadas aos exames realizados.
            </p>
            
            <p>
                <strong>Finalidade:</strong> Os dados são utilizados exclusivamente para a realização de exames laboratoriais, entrega de resultados e comunicação sobre seus exames.
            </p>
            
            <p>
                <strong>Compartilhamento:</strong> Seus dados não são compartilhados com terceiros, exceto quando necessário para a prestação do serviço (ex: convênios de saúde) ou por determinação legal.
            </p>
            
            <p>
                <strong>Segurança:</strong> Utilizamos criptografia e protocolos de segurança para proteger seus dados contra acesso não autorizado.
            </p>
            
            <p>
                <strong>Seus direitos:</strong> Você pode solicitar acesso, correção ou exclusão dos seus dados a qualquer momento através do nosso atendimento.
            </p>
        </div>

        {{-- Checkbox de aceite --}}
        <label class="flex items-start gap-3 p-4 bg-slate-50 rounded-xl cursor-pointer mb-6">
            <input type="checkbox" class="w-5 h-5 mt-0.5 rounded border-slate-300 text-rose-600 focus:ring-rose-500" id="termo-aceite">
            <span class="text-sm text-slate-700">
                Li e concordo com a <a href="#" class="text-rose-600 underline">Política de Privacidade</a> e os <a href="#" class="text-rose-600 underline">Termos de Uso</a> do Laboratório Lamarck.
            </span>
        </label>

        {{-- Ações --}}
        <button class="btn btn-primary btn-block btn-lg" id="btn-aceitar" data-action="confirm">
            <i data-lucide="check"></i>
            Aceitar e Continuar
        </button>
    </div>
</template>
