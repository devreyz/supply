<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind & Lucide -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
    </style>

</head>

<body class="font-sans antialiased text-slate-900 bg-gradient-to-br from-rose-50 via-pink-50 to-purple-50 min-h-screen flex items-center justify-center p-6">
    <div class="relative w-full max-w-md">
        <!-- Background Blobs -->
        <div class="absolute -top-20 -left-20 w-64 h-64 bg-rose-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float"></div>
        <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float" style="animation-delay: 2s;"></div>

        <div class="relative z-10 glass-effect rounded-3xl p-8 shadow-2xl">
            <!-- Logo area -->
            <div class="text-center mb-8">
                <a href="/" wire:navigate class="inline-flex flex-col items-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-rose-500 to-pink-500 rounded-2xl shadow-lg mb-4">
                        <i data-lucide="flask-conical" class="w-8 h-8 text-white"></i>
                    </div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-rose-600 to-pink-600 bg-clip-text text-transparent">Lamarck</h1>
                    <p class="text-slate-600 text-sm mt-1">Laboratório de Análises Clínicas</p>
                </a>
            </div>

            <div class="space-y-6">
                {{ $slot }}
            </div>

            <!-- Simple Footer -->
            <div class="mt-8 pt-6 border-t border-slate-200 text-center">
                <p class="text-xs text-slate-500">&copy; {{ date('Y') }} Lamarck. Tudo pela sua saúde.</p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>