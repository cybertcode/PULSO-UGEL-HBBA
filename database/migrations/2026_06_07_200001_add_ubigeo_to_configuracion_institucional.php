<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion_institucional', function (Blueprint $table) {
            $table->string('departamento', 100)->nullable()->after('provincia');
            $table->string('distrito', 100)->nullable()->after('departamento');
            $table->string('ubigeo', 10)->nullable()->after('distrito');
            $table->string('direccion', 255)->nullable()->after('ubigeo');
            $table->string('sitio_web', 255)->nullable()->after('direccion');
            $table->string('timezone', 50)->default('America/Lima')->after('sitio_web');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion_institucional', function (Blueprint $table) {
            $table->dropColumn(['departamento', 'distrito', 'ubigeo', 'direccion', 'sitio_web', 'timezone']);
        });
    }
};
