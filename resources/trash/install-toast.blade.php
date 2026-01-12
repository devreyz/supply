<div id="install-toast" class="fixed bottom-5 right-5 max-w-xs p-4 bg-card border border-border text-text-secondary text-sm rounded-lg shadow-lg transform translate-x-full opacity-0 transition-all duration-500 ease-in-out z-10">
  <div class="flex items-center justify-between gap-3">
    <x-application-logo class="w-10 h-10 fill-primary" />
    <span class="text-text"> {{__('messages.install_pwa')}} </span>
    <div class="flex gap-2">
      <button id="install-btn" class="bg-button-primary border border-border text-input text-lg px-3 py-1 rounded-lg hover:bg-button-primary-hover transition-colors">Instalar</button>
      <button id="close-toast" class="bg-button-secondary absolute -top-2 -right-2 pt-1  text-button-primary-text hover:text-primary text-sm flex justify-center items-center rounded-full w-6 h-6"><i class="fi fi-rr-cross"></i></button>
    </div>
  </div>
</div>



<script src="/js/pwa/index.js"></script>