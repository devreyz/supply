{{-- 
    Widget: Boas Vindas
    Mensagem de boas vindas com horário
--}}
<div class="bento-widget span-full primary">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-rose-100 text-sm font-medium" id="greeting-time">Bom dia!</p>
            <h2 class="text-2xl font-bold mt-1">Bem-vindo ao Lamarck</h2>
            <p class="text-rose-100 text-sm mt-2">Seu resultado online em poucos cliques</p>
        </div>
        <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center">
            <i data-lucide="flask-conical" class="w-8 h-8"></i>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Atualiza saudação baseado no horário
    function updateGreeting() {
        const hour = new Date().getHours();
        const el = document.getElementById('greeting-time');
        if (!el) return;

        if (hour >= 5 && hour < 12) {
            el.textContent = 'Bom dia! ☀️';
        } else if (hour >= 12 && hour < 18) {
            el.textContent = 'Boa tarde! 🌤️';
        } else {
            el.textContent = 'Boa noite! 🌙';
        }
    }
    
    updateGreeting();
</script>
@endpush
