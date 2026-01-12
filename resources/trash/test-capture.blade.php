<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teste - Sistema de Captura de Documentos</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- TailwindCSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Image Capture System -->
  <script src="{{ asset('js/image-capture.js') }}"></script>
</head>

<body class="bg-gray-100 min-h-screen">
  <div class="container mx-auto py-8">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">
        <i class="fas fa-camera-retro mr-2"></i>
        Sistema de Captura de Documentos Médicos
      </h1>

      <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center">
          <p class="text-gray-600 mb-6">
            Teste o novo sistema de captura de documentos com processamento em tempo real
          </p>

          <button
            onclick="openImageCaptureModal()"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200 inline-flex items-center">
            <i class="fas fa-camera mr-2"></i>
            Abrir Sistema de Captura
          </button>
        </div>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="text-center p-4 bg-blue-50 rounded-lg">
            <div class="text-blue-600 text-2xl mb-2">
              <i class="fas fa-video"></i>
            </div>
            <h3 class="font-semibold text-gray-800">Câmera ao Vivo</h3>
            <p class="text-sm text-gray-600">Visualização em tempo real com feedback de qualidade</p>
          </div>

          <div class="text-center p-4 bg-green-50 rounded-lg">
            <div class="text-green-600 text-2xl mb-2">
              <i class="fas fa-magic"></i>
            </div>
            <h3 class="font-semibold text-gray-800">Processamento</h3>
            <p class="text-sm text-gray-600">Melhoria automática de contraste e nitidez</p>
          </div>

          <div class="text-center p-4 bg-purple-50 rounded-lg">
            <div class="text-purple-600 text-2xl mb-2">
              <i class="fas fa-search"></i>
            </div>
            <h3 class="font-semibold text-gray-800">Análise</h3>
            <p class="text-sm text-gray-600">Detecção de exames e marcações automática</p>
          </div>
        </div>
      </div>

      <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">
          <i class="fas fa-info-circle mr-2"></i>
          Recursos do Sistema
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex items-start space-x-3">
            <div class="text-blue-600 mt-1">
              <i class="fas fa-check-circle"></i>
            </div>
            <div>
              <h4 class="font-medium text-gray-800">Análise em Tempo Real</h4>
              <p class="text-sm text-gray-600">Feedback instantâneo sobre qualidade da imagem</p>
            </div>
          </div>

          <div class="flex items-start space-x-3">
            <div class="text-green-600 mt-1">
              <i class="fas fa-check-circle"></i>
            </div>
            <div>
              <h4 class="font-medium text-gray-800">Processamento Avançado</h4>
              <p class="text-sm text-gray-600">Melhoria automática de contraste e nitidez</p>
            </div>
          </div>

          <div class="flex items-start space-x-3">
            <div class="text-purple-600 mt-1">
              <i class="fas fa-check-circle"></i>
            </div>
            <div>
              <h4 class="font-medium text-gray-800">Detecção de Marcações</h4>
              <p class="text-sm text-gray-600">Identifica automaticamente exames marcados</p>
            </div>
          </div>

          <div class="flex items-start space-x-3">
            <div class="text-orange-600 mt-1">
              <i class="fas fa-check-circle"></i>
            </div>
            <div>
              <h4 class="font-medium text-gray-800">Múltiplas Câmeras</h4>
              <p class="text-sm text-gray-600">Suporte para seleção entre câmeras disponíveis</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Include Image Capture Modal -->
  @include('components.image-capture-modal')

  <!-- Test Functions -->
  <script>
    // Simulação de funções do chat para teste
    function showTypingIndicator() {
      console.log('Mostrando indicador de digitação...');
    }

    function hideTypingIndicator() {
      console.log('Ocultando indicador de digitação...');
    }

    function showError(message) {
      alert('Erro: ' + message);
    }

    function handleChatResponse(data) {
      console.log('Resposta do chat:', data);
      alert('Imagem processada com sucesso! Verifique o console para detalhes.');
    }

    // Simulação do app state
    window.appState = {
      messages: []
    };
  </script>
</body>

</html>