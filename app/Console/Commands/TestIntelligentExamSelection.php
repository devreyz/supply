<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\QuoteService;
use ReflectionClass;

class TestIntelligentExamSelection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:intelligent-exam-selection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa o sistema inteligente de seleção de exames';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== TESTE DO SISTEMA INTELIGENTE DE SELEÇÃO DE EXAMES ===');
        $this->newLine();

        try {
            $quoteService = app(QuoteService::class);

            // Usar reflexão para acessar métodos privados
            $reflectionClass = new ReflectionClass($quoteService);
            $findAllPossibleExamsMethod = $reflectionClass->getMethod('findAllPossibleExams');
            $findAllPossibleExamsMethod->setAccessible(true);

            // Teste 1: TSH
            $this->info('Teste 1: Buscar todos os possíveis exames para TSH');
            $this->line('-----------------------------------------------');

            $possibleExams = $findAllPossibleExamsMethod->invoke($quoteService, ['TSH']);

            $this->info("Exames encontrados para 'TSH': " . count($possibleExams));
            foreach ($possibleExams as $exam) {
                $this->line("  - {$exam['name']} (Código: {$exam['code']}) - Busca: {$exam['original_search']} - Tipo: {$exam['match_type']}");
            }

            $this->newLine();

            // Teste 2: hemograma
            $this->info('Teste 2: Buscar todos os possíveis exames para hemograma');
            $this->line('----------------------------------------------------');

            $possibleExams2 = $findAllPossibleExamsMethod->invoke($quoteService, ['hemograma']);

            $this->info("Exames encontrados para 'hemograma': " . count($possibleExams2));
            foreach ($possibleExams2 as $exam) {
                $this->line("  - {$exam['name']} (Código: {$exam['code']}) - Busca: {$exam['original_search']} - Tipo: {$exam['match_type']}");
            }

            $this->newLine();

            // Teste 3: múltiplos exames
            $this->info('Teste 3: Buscar todos os possíveis exames para múltiplos nomes');
            $this->line('-----------------------------------------------------------');

            $possibleExams3 = $findAllPossibleExamsMethod->invoke($quoteService, ['TSH', 'hemograma', 'glicose']);

            $this->info("Exames encontrados para múltiplas buscas: " . count($possibleExams3));
            foreach ($possibleExams3 as $exam) {
                $this->line("  - {$exam['name']} (Código: {$exam['code']}) - Busca: {$exam['original_search']} - Tipo: {$exam['match_type']}");
            }

            $this->newLine();
            $this->info('=== Teste concluído com sucesso! ===');
        } catch (\Exception $e) {
            $this->error("ERRO: " . $e->getMessage());
            $this->error("Arquivo: " . $e->getFile() . " (Linha: " . $e->getLine() . ")");
        }

        return Command::SUCCESS;
    }
}
