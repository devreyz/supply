<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cotações (Quotes)
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'open', 'closed', 'cancelled'])->default('draft');
            $table->date('deadline')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'created_by']);
        });

        // Itens da cotação (produtos solicitados)
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index('quote_id');
            $table->unique(['quote_id', 'product_id']);
        });

        // Respostas dos fornecedores
        Schema::create('quote_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_value', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'submitted', 'accepted', 'rejected'])->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['quote_id', 'supplier_id']);
            $table->unique(['quote_id', 'supplier_id']);
        });

        // Itens das respostas (preços por produto)
        Schema::create('quote_response_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_response_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quote_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('unit_price', 10, 2);
            $table->decimal('quantity', 10, 2)->nullable()->comment('Quantidade disponível ou mínima');
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index('quote_response_id');
            $table->unique(['quote_response_id', 'quote_item_id']);
        });

        // Tabela auxiliar para comparação de cotações (histórico)
        Schema::create('quote_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('selected_response_id')->nullable()->constrained('quote_responses')->nullOnDelete();
            $table->json('comparison_data')->nullable()->comment('Dados da comparação de preços');
            $table->timestamp('compared_at')->useCurrent();
            $table->timestamps();

            $table->index('quote_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_comparisons');
        Schema::dropIfExists('quote_response_items');
        Schema::dropIfExists('quote_responses');
        Schema::dropIfExists('quote_items');
        Schema::dropIfExists('quotes');
    }
};
