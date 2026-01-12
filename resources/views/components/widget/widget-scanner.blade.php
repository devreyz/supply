{{-- 
    Widget: Scanner QR Code
    Botão para abrir o scanner de QR
--}}
<div class="bento-widget secondary tall flex flex-col items-center justify-center text-center" onclick="app.openSheet('tpl-sheet-scanner')">
    <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center mb-4">
        <i data-lucide="qr-code" class="w-8 h-8"></i>
    </div>
    <h3 class="font-bold text-lg">Scanner QR</h3>
    <p class="text-slate-300 text-sm mt-1">Escaneie o código do seu resultado</p>
    
    {{-- Decoração animada --}}
    <div class="absolute top-4 right-4 w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
</div>
