<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adiciona campos ZePocket à tabela products existente
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Campos ZePocket
            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable()->after('name');
            }
            if (!Schema::hasColumn('products', 'ean')) {
                $table->string('ean', 20)->nullable()->unique()->after('unit');
            }
            if (!Schema::hasColumn('products', 'image_url')) {
                $table->string('image_url')->nullable()->after('photo_path');
            }
            if (!Schema::hasColumn('products', 'is_global')) {
                $table->boolean('is_global')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('products', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('is_global')->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['brand', 'ean', 'image_url', 'is_global', 'created_by']);
        });
    }
};
