<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->boolean('email_enviado')->default(false)->after('leida_at');
            $table->timestamp('email_enviado_at')->nullable()->after('email_enviado');
            $table->string('destinatario_email', 150)->nullable()->after('email_enviado_at');
        });
    }

    public function down(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->dropColumn(['email_enviado', 'email_enviado_at', 'destinatario_email']);
        });
    }
};
