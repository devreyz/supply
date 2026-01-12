{{-- 
    Widget: Chat (col-span-1)
    Acesso ao chat de atendimento
--}}
<div class="bento-widget bg-gradient-to-br from-green-600 to-green-700 text-white cursor-pointer hover:scale-105 transition-transform" 
     onclick="app.go('chat')">
    <div class="flex flex-col h-full justify-between">
        <div class="flex items-center justify-between">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                <i data-lucide="message-circle" class="w-5 h-5"></i>
            </div>
            <i data-lucide="chevron-right" class="w-5 h-5 text-white/70"></i>
        </div>
        
        <div>
            <h3 class="font-bold text-lg mb-1">Chat</h3>
            <p class="text-sm text-white/80">Converse conosco</p>
        </div>
    </div>
</div>
