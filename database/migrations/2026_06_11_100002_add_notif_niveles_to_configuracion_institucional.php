<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion_institucional', function (Blueprint $table) {
            // Niveles de anticipación (reemplaza el campo único por 3 toggles)
            $table->boolean('notif_10dias')->default(true)->after('notif_dias_anticipacion');
            $table->boolean('notif_5dias')->default(true)->after('notif_10dias');
            $table->boolean('notif_1dia')->default(true)->after('notif_5dias');

            // Aplicar notificaciones por módulo
            $table->boolean('notif_modulo_sci')->default(true)->after('notif_1dia');
            $table->boolean('notif_modulo_integridad')->default(true)->after('notif_modulo_sci');

            // Configuración SMTP para envío de correo
            $table->string('mail_host', 255)->nullable()->after('notif_email');
            $table->unsignedSmallInteger('mail_port')->default(587)->after('mail_host');
            $table->string('mail_username', 255)->nullable()->after('mail_port');
            $table->string('mail_password', 255)->nullable()->after('mail_username');
            $table->string('mail_encryption', 20)->default('tls')->after('mail_password');
            $table->string('mail_from_name', 255)->nullable()->after('mail_encryption');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion_institucional', function (Blueprint $table) {
            $table->dropColumn([
                'notif_10dias', 'notif_5dias', 'notif_1dia',
                'notif_modulo_sci', 'notif_modulo_integridad',
                'mail_host', 'mail_port', 'mail_username', 'mail_password',
                'mail_encryption', 'mail_from_name',
            ]);
        });
    }
};
