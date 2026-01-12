<?php

namespace App\Console\Commands;

use App\Services\AIService;
use Illuminate\Console\Command;

class TestAI extends Command
{
  protected $signature = 'ai:test';
  protected $description = 'Testar o serviço de IA';

  public function handle()
  {
    try {
      $aiService = new AIService();

      $this->info("Provider atual: " . $aiService->getProvider());
      $this->info("Configurado: " . ($aiService->isConfigured() ? 'Sim' : 'Não'));

      if (!$aiService->isConfigured()) {
        $this->error('Serviço de IA não está configurado corretamente!');
        $this->warn('Para usar o Gemini, obtenha uma chave API gratuita em: https://makersuite.google.com/app/apikey');
        $this->warn('E atualize a variável GEMINI_API_KEY no arquivo .env');
        return 1;
      }

      $this->info('Testando resposta do AI...');
      $response = $aiService->generateChatResponse('Olá, você está funcionando?');

      if ($response['success']) {
        $this->info('Resposta: ' . $response['message']);
        $this->info('✅ Serviço de IA funcionando corretamente!');
      } else {
        $this->error('❌ Erro: ' . $response['message']);
        if (isset($response['error'])) {
          $this->error('Detalhes: ' . $response['error']);
        }
      }
    } catch (\Exception $e) {
      $this->error('Erro ao testar o serviço: ' . $e->getMessage());
      return 1;
    }

    return 0;
  }
}
