@props(['title' => "Lamarck"])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ $title ? $title : config('app.name', 'Lamarck') }}</title>
  <link
    rel="manifest"
    href="manifest.json" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">


  <!-- CSS -->
  @vite(['resources/css/spa.css', 'resources/css/app.css'])


  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
</head>

<body class="font-sans antialiased overflow-x-hidden bg-background">

<main id="app">{{ $slot }}</main>

  
  


  <!-- Modal de Resultados -->
  <div id="resultsModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
      <div class="p-6">
        <!-- Header do Modal -->
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-bold text-gray-900 flex items-center">
            <svg class="w-6 h-6 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.991 8.991 0 01-4.255-1.165L3 21l2.165-5.59A8.989 8.989 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z" />
            </svg>
            Acessar Resultados
          </h3>
          <button onclick="closeResultsModal()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Conteúdo do Modal -->
        <div class="space-y-4 mb-6">
          <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <h4 class="text-sm font-medium text-blue-800">Como acessar seus resultados:</h4>
                <div class="mt-2 text-sm text-blue-700">
                  <ol class="list-decimal list-inside space-y-1">
                    <li>Tenha em mãos seu <strong>código de acesso</strong> (6 dígitos)</li>
                    <li>Informe sua <strong>data de nascimento</strong></li>
                    <li>Clique no botão abaixo para acessar</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 rounded-lg p-4">
            <h5 class="font-medium text-gray-900 mb-2">Onde encontrar o código?</h5>
            <p class="text-sm text-gray-600">
              O código de 6 dígitos está presente no comprovante de coleta ou foi enviado por SMS/WhatsApp.
            </p>
          </div>
        </div>

        <!-- Botão de Acesso -->
        <div class="flex space-x-3">
          <button onclick="closeResultsModal()"
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            Cancelar
          </button>
          <a href="https://lamarck.sisvida.com.br/resultados/login"
            target="_blank"
            class="flex-1 bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors text-center font-medium">
            Acessar Portal
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts Gerais -->
  <!-- Lucide icons (global) -->
  <script src="https://unpkg.com/lucide@latest" onerror="console.error('❌ Falha ao carregar Lucide do CDN')"></script>

  <!-- Verificação de libs carregadas (Html5Qrcode será fornecido pelo bundle agora) -->
  <script>
    window.addEventListener('DOMContentLoaded', function() {
      setTimeout(() => {
        if (typeof lucide === 'undefined') {
          console.error('❌ LUCIDE não disponível - ícones não serão renderizados');
        } else {
          console.log('✅ Lucide carregado:', typeof lucide);
        }
        if (typeof Html5Qrcode === 'undefined') {
          console.error('❌ Html5Qrcode não disponível - scanner não funcionará (bundle não forneceu)');
        } else {
          console.log('✅ Html5Qrcode disponível via bundle:', typeof Html5Qrcode);
        }
      }, 500);
    });
  </script>

  @vite(['resources/js/app.js'])

  <script>
    AOS.init({
      duration: 700, // Duração das animações em milissegundos
      once: true // Para a animação ser executada apenas uma vez
    });
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener("click", function(e) {
        e.preventDefault(); // Impede o comportamento padrão do link

        const targetId = this.getAttribute("href").substring(1);
        const targetElement = document.getElementById(targetId);

        if (targetElement) {
          window.scrollTo({
            top: targetElement.offsetTop + 10, // Ajusta a posição do scroll (ex: -50px para compensar menu fixo)
            behavior: "smooth"
          });
        }
      });
    });
  </script>
  @stack('scripts')

</body>

</html>