<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encuesta_opciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregunta_id')->constrained('encuesta_preguntas')->cascadeOnDelete();
            $table->smallInteger('orden')->default(1);
            $table->string('texto');
            $table->timestamps();

            $table->index(['pregunta_id', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encuesta_opciones');
    }
};
