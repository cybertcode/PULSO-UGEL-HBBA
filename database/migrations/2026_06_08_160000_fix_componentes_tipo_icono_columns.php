<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cambiar directamente via raw SQL para evitar restricciones del enum
        DB::statement("ALTER TABLE componentes MODIFY COLUMN tipo VARCHAR(100) NULL DEFAULT NULL");
        DB::statement("ALTER TABLE componentes MODIFY COLUMN icono VARCHAR(80) NULL DEFAULT NULL");
        // Limpiar valores del enum antiguo que ya no son categorías válidas
        DB::table('componentes')->whereIn('tipo', ['sci', 'integridad', 'ambos'])->update(['tipo' => null]);
    }

    public function down(): void
    {
        DB::table('componentes')->update(['tipo' => 'ambos']);

        Schema::table('componentes', function (Blueprint $table) {
            $table->enum('tipo', ['sci', 'integridad', 'ambos'])->default('ambos')->change();
            $table->string('icono', 60)->nullable()->change();
        });
    }
};
