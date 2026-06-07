<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unidades_organicas', function (Blueprint $table) {
            $table->string('foto_ruta', 500)->nullable()->after('responsable');
            $table->string('correo', 100)->nullable()->after('foto_ruta');
            $table->string('telefono', 20)->nullable()->after('correo');
            $table->string('descripcion')->nullable()->after('telefono');
        });
    }

    public function down(): void
    {
        Schema::table('unidades_organicas', function (Blueprint $table) {
            $table->dropColumn(['foto_ruta', 'correo', 'telefono', 'descripcion']);
        });
    }
};
