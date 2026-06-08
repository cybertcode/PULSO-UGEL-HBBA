<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('autoevaluaciones', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->year('anio');
            $table->enum('periodo', ['I_trimestre', 'II_trimestre', 'III_trimestre', 'IV_trimestre', 'semestral', 'anual'])->default('anual');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_cierre')->nullable();
            $table->enum('estado', ['abierta', 'en_proceso', 'cerrada'])->default('abierta');
            $table->tinyInteger('puntaje_total')->nullable();
            $table->text('conclusiones')->nullable();
            $table->text('recomendaciones')->nullable();
            $table->unsignedBigInteger('elaborado_por')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('elaborado_por')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('autoevaluacion_respuestas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('autoevaluacion_id');
            $table->unsignedBigInteger('componente_id');
            // Pregunta del cuestionario
            $table->string('pregunta');
            // Respuesta: si/no/parcial
            $table->enum('respuesta', ['si', 'no', 'parcial', 'no_aplica'])->nullable();
            // Puntaje 0-3
            $table->tinyInteger('puntaje')->default(0);
            $table->text('evidencia')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->foreign('autoevaluacion_id')->references('id')->on('autoevaluaciones')->cascadeOnDelete();
            $table->foreign('componente_id')->references('id')->on('componentes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('autoevaluacion_respuestas');
        Schema::dropIfExists('autoevaluaciones');
    }
};
