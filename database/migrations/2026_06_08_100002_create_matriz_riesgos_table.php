<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matriz_riesgos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->nullable();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('componente_id')->nullable();
            $table->unsignedBigInteger('unidad_organica_id')->nullable();
            // Tipo de riesgo
            $table->enum('tipo', ['estrategico', 'operativo', 'cumplimiento', 'reporte', 'tecnologico'])->default('operativo');
            // Probabilidad e impacto (1-5)
            $table->tinyInteger('probabilidad')->default(1);
            $table->tinyInteger('impacto')->default(1);
            // Nivel calculado = probabilidad * impacto
            $table->tinyInteger('nivel_riesgo')->storedAs('probabilidad * impacto');
            // Clasificación automática basada en nivel_riesgo
            $table->enum('clasificacion', ['bajo', 'moderado', 'alto', 'critico'])->default('bajo');
            // Controles
            $table->text('controles_existentes')->nullable();
            $table->text('acciones_tratamiento')->nullable();
            $table->enum('tipo_tratamiento', ['mitigar', 'aceptar', 'transferir', 'evitar'])->default('mitigar');
            // Responsable
            $table->unsignedBigInteger('responsable_id')->nullable();
            $table->date('fecha_revision')->nullable();
            $table->enum('estado', ['activo', 'mitigado', 'aceptado', 'cerrado'])->default('activo');
            $table->text('observaciones')->nullable();
            $table->year('anio')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('componente_id')->references('id')->on('componentes')->nullOnDelete();
            $table->foreign('unidad_organica_id')->references('id')->on('unidades_organicas')->nullOnDelete();
            $table->foreign('responsable_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matriz_riesgos');
    }
};
