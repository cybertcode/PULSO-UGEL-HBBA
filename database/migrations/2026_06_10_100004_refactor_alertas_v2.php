<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->enum('modulo', ['sci', 'integridad'])->default('sci')->after('id');
            $table->unsignedTinyInteger('dias_anticipacion')->nullable()->after('tipo');
            $table->boolean('notificacion_enviada')->default(false)->after('email_enviado_at');
            $table->timestamp('notificacion_enviada_at')->nullable()->after('notificacion_enviada');

            $table->index(['modulo', 'leida']);
        });
    }

    public function down(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->dropColumn(['modulo', 'dias_anticipacion', 'notificacion_enviada', 'notificacion_enviada_at']);
        });
    }
};
