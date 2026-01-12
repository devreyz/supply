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
        // Tabela de empresas (Multi-tenancy)
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document')->unique()->comment('CNPJ');
            $table->foreignId('owner_id')->nullable()->comment('ID do usuário proprietário no ZePocket Core');
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Atualizar tabela de usuários existente (adiciona colunas novas)
        if (!Schema::hasColumn('users', 'zepocket_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('zepocket_id')->unique()->nullable()->after('email')->comment('ID remoto do usuário no ZePocket Core');
                $table->foreignId('current_company_id')->nullable()->after('zepocket_id')->constrained('companies')->nullOnDelete();
            });
        }

        // Tabela de relacionamento usuários-empresas (muitos-para-muitos)
        Schema::create('company_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('member')->comment('owner, admin, member');
            $table->timestamps();

            $table->unique(['company_id', 'user_id']);
        });

        // Tabelas de suporte para session, cache e jobs (shared hosting)
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        if (!Schema::hasTable('cache')) {
            Schema::create('cache', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->integer('expiration');
            });
        }

        if (!Schema::hasTable('cache_locks')) {
            Schema::create('cache_locks', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->string('owner');
                $table->integer('expiration');
            });
        }

        if (!Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('company_user');
        
        // Remove apenas as colunas adicionadas à tabela users
        if (Schema::hasColumn('users', 'zepocket_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['current_company_id']);
                $table->dropColumn(['zepocket_id', 'current_company_id']);
            });
        }
        
        Schema::dropIfExists('companies');
    }
};
