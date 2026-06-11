<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            // Módulo al que pertenece la actividad
            $table->enum('modulo', ['sci', 'integridad'])->default('sci')->after('codigo');

            // Año de la actividad (hereda del eje/etapa seleccionado)
            $table->year('anio')->nullable()->after('modulo');

            // FK a la pregunta SCI
            $table->foreignId('sci_pregunta_id')
                  ->nullable()
                  ->after('anio')
                  ->constrained('sci_preguntas')
                  ->nullOnDelete();

            // FK a la pregunta de Integridad
            $table->foreignId('integridad_pregunta_id')
                  ->nullable()
                  ->after('sci_pregunta_id')
                  ->constrained('integridad_preguntas')
                  ->nullOnDelete();

            $table->index(['modulo', 'estado']);
            $table->index(['modulo', 'anio']);
        });

        // Eliminar FK y columna componente_id (ya no se usa)
        Schema::table('actividades', function (Blueprint $table) {
            $table->dropForeign(['componente_id']);
            $table->dropIndex(['componente_id_estado']); // índice compuesto anterior
            $table->dropColumn('componente_id');
        });
    }

    public function down(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->dropForeign(['sci_pregunta_id']);
            $table->dropForeign(['integridad_pregunta_id']);
            $table->dropColumn(['modulo', 'anio', 'sci_pregunta_id', 'integridad_pregunta_id']);
            $table->foreignId('componente_id')->constrained('componentes')->restrictOnDelete();
        });
    }
};
