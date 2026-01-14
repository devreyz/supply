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

  
  



  <!-- Scripts Gerais -->
  <!-- Lucide icons (global) -->
  <script src="https://unpkg.com/lucide@latest" onerror="console.error('❌ Falha ao carregar Lucide do CDN')"></script>

  <!-- Verificação de libs carregadas (Html5Qrcode será fornecido pelo bundle agora) -->
  <script>
     window.APP_USER = {
            id: "{{ auth()->id() }}",
            name: "{{ auth()->user()?->name }}",
            email: "{{ auth()->user()?->email }}",
        }
        window.CSRF_TOKEN = '{{ csrf_token() }}';
        window.API_BASE = '/api/zepocket';

  
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