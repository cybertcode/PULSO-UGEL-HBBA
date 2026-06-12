<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Renombrar columna calificacion a puntaje_comision si existe
        // y agregar columnas del concurso
        Schema::table('buenas_practicas', function (Blueprint $table) {
            // Columna para el número de expediente/registro de recepción
            $table->string('numero_expediente')->nullable()->after('numero_sgd');
            // Fecha en que SCI recepciona el documento
            $table->date('fecha_recepcion')->nullable()->after('numero_expediente');
            // Puntaje asignado por la comisión (0-100)
            $table->unsignedTinyInteger('puntaje_comision')->nullable()->after('calificacion');
            // Observaciones de la comisión al evaluar
            $table->text('observacion_comision')->nullable()->after('puntaje_comision');
        });

        // Actualizar registros con estado 'propuesta' al nuevo estado 'presentado'
        DB::table('buenas_practicas')->where('estado', 'propuesta')->update(['estado' => 'presentado']);
    }

    public function down(): void
    {
        Schema::table('buenas_practicas', function (Blueprint $table) {
            $table->dropColumn(['numero_expediente', 'fecha_recepcion', 'puntaje_comision', 'observacion_comision']);
        });
        DB::table('buenas_practicas')->where('estado', 'presentado')->update(['estado' => 'propuesta']);
    }
};
