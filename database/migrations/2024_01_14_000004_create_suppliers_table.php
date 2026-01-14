<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adiciona campos ZePocket à tabela suppliers existente
     */
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Campos ZePocket
            if (!Schema::hasColumn('suppliers', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('company_id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('suppliers', 'whatsapp')) {
                $table->string('whatsapp')->nullable()->after('email');
            }
            if (!Schema::hasColumn('suppliers', 'notes')) {
                $table->text('notes')->nullable()->after('settings');
            }
            if (!Schema::hasColumn('suppliers', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'whatsapp', 'notes', 'is_active']);
        });
    }
};
