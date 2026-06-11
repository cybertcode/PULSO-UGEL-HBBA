<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encuesta_respuesta_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('respuesta_id')->constrained('encuesta_respuestas')->cascadeOnDelete();
            $table->foreignId('pregunta_id')->constrained('encuesta_preguntas')->cascadeOnDelete();
            $table->foreignId('opcion_id')->nullable()->constrained('encuesta_opciones')->nullOnDelete();
            $table->text('texto_respuesta')->nullable();
            $table->timestamps();

            $table->index(['respuesta_id', 'pregunta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encuesta_respuesta_detalles');
    }
};
