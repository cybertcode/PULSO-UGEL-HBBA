<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion_institucional', function (Blueprint $table) {
            $table->string('whatsapp_sci', 20)->nullable()->after('coordinador_sci');
            $table->string('correo_sci')->nullable()->after('whatsapp_sci');
            $table->string('cargo_sci')->nullable()->after('correo_sci');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion_institucional', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_sci', 'correo_sci', 'cargo_sci']);
        });
    }
};
