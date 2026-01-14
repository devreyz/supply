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
        Schema::table('users', function (Blueprint $table) {
            // Remover campos antigos do zepocket se existirem
            if (Schema::hasColumn('users', 'zepocket_id')) {
                $table->dropColumn(['zepocket_id', 'zepocket_token', 'zepocket_refresh_token']);
            }

            // Adicionar campos do ZeTools
            $table->unsignedBigInteger('zetools_id')->nullable()->unique()->after('email');
            $table->text('zetools_token')->nullable()->after('avatar');
            $table->text('zetools_refresh_token')->nullable()->after('zetools_token');
            $table->timestamp('token_expires_at')->nullable()->after('zetools_refresh_token');
            $table->json('subscriptions_cache')->nullable()->after('token_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'zetools_id',
                'zetools_token',
                'zetools_refresh_token',
                'token_expires_at',
                'subscriptions_cache',
            ]);
        });
    }
};
