<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Itens dos pedidos de compra
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_cost_snapshot', 10, 2); // Preço do fornecedor no momento
            $table->decimal('unit_sale_snapshot', 10, 2)->nullable(); // Preço de venda no momento
            $table->decimal('subtotal', 10, 2); // quantity * unit_cost_snapshot
            $table->decimal('profit_snapshot', 10, 2)->nullable(); // Lucro previsto
            $table->timestamps();
            
            $table->index('order_id');
            $table->unique(['order_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
