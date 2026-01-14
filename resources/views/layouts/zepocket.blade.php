@props(['title' => "ZePocket - Gestor de Compras"])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0ea5e9">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <title>{{ $title }}</title>
    
    <link rel="manifest" href="/manifest.json" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- CSS -->
    @vite(['resources/css/spa.css', 'resources/css/app.css', 'resources/css/zepocket.css'])

    <!-- User Data for JS -->
    <script>
        window.APP_USER = @json([
            'id' => auth()->id(),
            'name' => auth()->user()?->name,
            'email' => auth()->user()?->email,
        ]);
        window.CSRF_TOKEN = '{{ csrf_token() }}';
        window.API_BASE = '/api/zepocket';
    </script>
</head>

<body class="font-sans antialiased overflow-x-hidden bg-background">

    <main id="app">{{ $slot }}</main>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Init Lucide -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>

    <!-- SPA Framework + ZePocket -->
    @vite(['resources/js/app.js', 'resources/js/modules/zepocket/main.js'])

    @stack('scripts')
</body>
</html>
