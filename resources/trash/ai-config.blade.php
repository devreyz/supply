<x-app-layout>
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-12">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-12">
      <h1 class="text-4xl font-bold text-gray-900 mb-4">
        🤖 Configuração da IA
      </h1>
      <p class="text-xl text-gray-600">
        Configure o Google Gemini para usar o chat inteligente
      </p>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
      <div class="flex items-center mb-6">
        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
          <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900">
          Como obter sua chave da API do Google Gemini
        </h2>
      </div>

      <div class="space-y-6">
        <div class="flex items-start">
          <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-4">
            1
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
              Acesse o Google AI Studio
            </h3>
            <p class="text-gray-600 mb-3">
              Vá para o site oficial do Google AI Studio
            </p>
            <a href="https://makersuite.google.com/app/apikey"
              target="_blank"
              class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
              </svg>
              Abrir Google AI Studio
            </a>
          </div>
        </div>

        <div class="flex items-start">
          <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-4">
            2
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
              Faça login com sua conta Google
            </h3>
            <p class="text-gray-600">
              Use sua conta pessoal ou profissional do Google
            </p>
          </div>
        </div>

        <div class="flex items-start">
          <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-4">
            3
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
              Clique em "Create API Key"
            </h3>
            <p class="text-gray-600">
              Você será direcionado para a página de criação de chaves
            </p>
          </div>
        </div>

        <div class="flex items-start">
          <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-4">
            4
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
              Copie sua chave API
            </h3>
            <p class="text-gray-600">
              Guarde a chave em um local seguro - você só poderá vê-la uma vez
            </p>
          </div>
        </div>

        <div class="flex items-start">
          <div class="flex-shrink-0 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-4">
            5
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
              Configure no sistema
            </h3>
            <p class="text-gray-600 mb-3">
              Abra o arquivo <code class="bg-gray-100 px-2 py-1 rounded">.env</code> no projeto e substitua:
            </p>
            <div class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm">
              GEMINI_API_KEY=YOUR_GEMINI_API_KEY_HERE
            </div>
            <p class="text-gray-600 mt-3">
              Por sua chave real:
            </p>
            <div class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm">
              GEMINI_API_KEY=AIza...sua_chave_aqui
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-8">
      <div class="flex items-center mb-4">
        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
          <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-green-800">
          Totalmente gratuito!
        </h3>
      </div>
      <p class="text-green-700">
        O Google Gemini oferece uma cota generosa gratuita para uso pessoal e de desenvolvimento.
        Perfeito para testes e projetos pequenos.
      </p>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
      <div class="flex items-center mb-4">
        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-blue-800">
          Testando a configuração
        </h3>
      </div>
      <p class="text-blue-700 mb-4">
        Após configurar a chave, você pode testar se está funcionando executando:
      </p>
      <div class="bg-blue-900 text-blue-100 p-4 rounded-lg font-mono text-sm">
        php artisan ai:test
      </div>
    </div>

    <div class="text-center mt-8">
      <a href="{{ route('chat.index') }}"
        class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.991 8.991 0 01-4.41-1.165L3 21l2.165-5.59A8.989 8.989 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
        </svg>
        Ir para o Chat
      </a>
    </div>
  </div>
</div>
</x-app-layout>