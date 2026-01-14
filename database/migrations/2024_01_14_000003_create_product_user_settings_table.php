<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Configurações do produto específicas para cada usuário (preço de venda, estoque mínimo)
     */
    public function up(): void
    {
        Schema::create('product_user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('sale_price', 10, 2)->nullable(); // Preço de venda na gôndola
            $table->integer('min_stock')->nullable(); // Estoque mínimo para alertas
            $table->integer('current_stock')->nullable(); // Estoque atual
            $table->timestamps();
            
            // Unique: um usuário só tem uma config por produto
            $table->unique(['user_id', 'product_id']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_user_settings');
    }
};
