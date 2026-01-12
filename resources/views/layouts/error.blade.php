@props(['title' => "Error"])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>{{ $title }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- CSS -->

    <!-- Scripts -->
    <script src="{{ asset('js/app/app.js') }}" type="text/javascript"></script>
</head>

<body class="font-sans text-text antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center bg-background">
        <div>
            <a href="/" wire:navigate class="flex items-center justify-center flex-col text-center">
                <p class="text-2xl font-bold text-text-secondary mb-6 ">Laboratório Lamarck <br> Análises Clínicas e Citopatologicas</p>
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-10 overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
</body>

</html>