<x-spa-layout>
  <x-home />


  <!-- drawer -->
  <x-drawer.menu />


  <!-- sheet -->
  <x-sheet.user-settings /> 


  
  <!-- =================== BACKDROP =================== -->
  <div id="backdrop"></div>

  <!-- =================== TOAST CONTAINER =================== -->
  <div id="toast-container" class="toast-container toast-bottom-right"></div>

  <!-- =================== LOADING OVERLAY =================== -->
  <div id="loading-overlay">
    <div class="loading-spinner"></div>
    <p class="loading-text">Carregando...</p>
  </div>
</x-spa-layout>