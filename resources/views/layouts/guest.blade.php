<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZeTools') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=geist-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind & Lucide -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --primary: 238.7 83.5% 66.7%;
            --primary-foreground: 210 40% 98%;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        .gradient-text {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .bento-card {
            background: white;
            border-radius: 2rem;
            border: 1px solid #e2e8f0;
            padding: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
        }
    </style>

</head>

<body class="font-sans antialiased text-slate-900 bg-slate-50 min-h-screen flex items-center justify-center p-4 sm:p-6">
    <!-- Background Decoration -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-indigo-100 rounded-full blur-[120px] opacity-50 animate-float"></div>
        <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-purple-100 rounded-full blur-[120px] opacity-50 animate-float" style="animation-delay: 1.5s;"></div>
    </div>

    <div class="relative w-full max-w-[440px] z-10">
        <div class="bento-card glass-effect">
            <!-- Logo area -->
            <div class="text-center mb-10">
                <a href="/" wire:navigate class="inline-flex flex-col items-center group">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-xl shadow-indigo-500/20 group-hover:scale-110 transition-transform duration-300 mb-4">
                        <i data-lucide="pocket" class="w-8 h-8 text-white"></i>
                    </div>
                    <h1 class="text-3xl font-bold gradient-text tracking-tight">{{ config('app.name', 'ZeTools') }}</h1>
                    <p class="text-slate-500 text-sm mt-1 font-medium">Suas ferramentas inteligentes</p>
                </a>
            </div>

            <div class="space-y-6">
                {{ $slot ?? '' }}
                @yield('content')
            </div>

            <!-- Simple Footer -->
            <div class="mt-10 pt-6 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-400 font-medium tracking-wide uppercase">
                    &copy; {{ date('Y') }} {{ config('app.name', 'ZeTools') }} &bull; Feito com <i data-lucide="heart" class="inline w-3 h-3 text-rose-500 fill-rose-500"></i>
                </p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>