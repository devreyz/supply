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
            // Data de expiração do acesso ao serviço Gôndola
            $table->timestamp('service_access_expires_at')->nullable()->after('token_expires_at');
            // Última verificação de acesso no ZeTools (para revalidação periódica)
            $table->timestamp('last_access_check_at')->nullable()->after('service_access_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['service_access_expires_at', 'last_access_check_at']);
        });
    }
};
