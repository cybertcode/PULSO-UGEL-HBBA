<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL no permite ALTER ENUM directamente de forma limpia, usamos MODIFY COLUMN
        DB::statement("ALTER TABLE encuesta_preguntas MODIFY COLUMN tipo ENUM(
            'opcion_multiple',
            'seleccion_multiple',
            'escala',
            'texto_libre',
            'si_no',
            'verdadero_falso',
            'desplegable'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE encuesta_preguntas MODIFY COLUMN tipo ENUM(
            'opcion_multiple',
            'seleccion_multiple',
            'escala',
            'texto_libre'
        ) NOT NULL");
    }
};
