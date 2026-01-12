{{-- 
    Widget: Vacinas
    Destaque para serviço de vacinas
--}}
<div class="bento-widget span-2 bg-gradient-to-br from-emerald-500 to-teal-600 text-white" onclick="app.toast('Em breve: Agendamento de Vacinas!', 'info')">
    <div class="flex items-center justify-between">
        <div>
            <span class="inline-block px-2 py-1 bg-white/20 rounded-full text-xs font-medium mb-2">
                🆕 Novidade
            </span>
            <h3 class="font-bold text-lg">Centro de Vacinação</h3>
            <p class="text-emerald-100 text-sm mt-1">Vacinas para todas as idades</p>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center">
            <i data-lucide="syringe" class="w-7 h-7"></i>
        </div>
    </div>
    
    <div class="mt-4 flex items-center gap-2 text-sm text-emerald-100">
        <i data-lucide="calendar" class="w-4 h-4"></i>
        <span>Agende sua vacina</span>
        <i data-lucide="arrow-right" class="w-4 h-4 ml-auto"></i>
    </div>
</div>
