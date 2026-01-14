<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ZePocket Gôndola</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Card de Login -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 mb-4">
                    <i class="ph ph-shopping-cart text-3xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Gôndola</h1>
                <p class="text-gray-600 mt-2">Sistema de Gestão de Cotações</p>
            </div>

            <!-- Mensagens -->
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    <div class="flex items-start gap-2">
                        <i class="ph ph-warning-circle text-lg mt-0.5"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                    <div class="flex items-start gap-2">
                        <i class="ph ph-check-circle text-lg mt-0.5"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Botão de Login com ZeTools -->
            <a href="{{ route('auth.zetools') }}" 
               class="flex items-center justify-center gap-3 w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                    <path d="M3 9h18M9 21V9" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span>Entrar com ZeTools</span>
            </a>

            <!-- Divider - Removido login tradicional -->
            <!-- 
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500">ou continue com</span>
                </div>
            </div>

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                               placeholder="seu@email.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Senha</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                               placeholder="••••••••">
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="remember" class="rounded text-purple-600">
                            <span class="text-gray-600">Lembrar-me</span>
                        </label>
                        <a href="#" class="text-purple-600 hover:text-purple-700 font-medium">Esqueci a senha</a>
                    </div>

                    <button type="submit" 
                            class="w-full bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200">
                        Entrar
                    </button>
                </div>
            </form>
            -->

            <!-- Footer -->
            <divFaça login através do ZeTools para acessar o sistema
                <a href="#" class="text-purple-600 hover:text-purple-700 font-medium">Criar conta</a>
            </div>
        </div>

        <!-- Info Card -->
        <div class="mt-6 bg-white/10 backdrop-blur-sm rounded-xl p-4 text-white text-center text-sm">
            <p class="flex items-center justify-center gap-2">
                <i class="ph ph-shield-check text-lg"></i>
                <span>Autenticação segura via OAuth 2.0</span>
            </p>
        </div>
    </div>
</body>
</html>
