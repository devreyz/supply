<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabela pivô de cotações: preços de custo por fornecedor/produto
     */
    public function up(): void
    {
        Schema::create('product_supplier_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('cost_price', 10, 2); // Preço de custo do fornecedor
            $table->decimal('previous_price', 10, 2)->nullable(); // Preço anterior (para variação)
            $table->timestamp('last_quoted_at')->useCurrent();
            $table->timestamps();
            
            // Unique: apenas um preço ativo por fornecedor/produto
            $table->unique(['supplier_id', 'product_id']);
            $table->index(['product_id', 'cost_price']); // Para ordenar por menor preço
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_supplier_prices');
    }
};
