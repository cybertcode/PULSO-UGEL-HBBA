<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar duplicados de alertas NO leídas (mismo actividad_id + tipo), conservar el más reciente
        DB::statement("
            DELETE a1 FROM alertas a1
            INNER JOIN alertas a2
            WHERE a1.id < a2.id
              AND a1.actividad_id  = a2.actividad_id
              AND a1.tipo          = a2.tipo
              AND a1.leida         = 0
              AND a2.leida         = 0
              AND a1.actividad_id IS NOT NULL
        ");

        // Eliminar duplicados de alertas YA leídas (mismo actividad_id + tipo), conservar el más reciente
        DB::statement("
            DELETE a1 FROM alertas a1
            INNER JOIN alertas a2
            WHERE a1.id < a2.id
              AND a1.actividad_id  = a2.actividad_id
              AND a1.tipo          = a2.tipo
              AND a1.leida         = 1
              AND a2.leida         = 1
              AND a1.actividad_id IS NOT NULL
        ");

        // Agregar columna virtual para índice único sólo en pendientes (leida=0)
        // MySQL no soporta partial index, usamos columna generada
        DB::statement("
            ALTER TABLE alertas
            ADD COLUMN actividad_tipo_pendiente VARCHAR(80)
            GENERATED ALWAYS AS (
                IF(leida = 0 AND actividad_id IS NOT NULL,
                   CONCAT(actividad_id, '-', tipo),
                   NULL)
            ) VIRTUAL
        ");

        Schema::table('alertas', function (Blueprint $table) {
            $table->unique('actividad_tipo_pendiente', 'alertas_actividad_tipo_pendiente_unique');
        });
    }

    public function down(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->dropUnique('alertas_actividad_tipo_pendiente_unique');
            $table->dropColumn('actividad_tipo_pendiente');
        });
    }
};
