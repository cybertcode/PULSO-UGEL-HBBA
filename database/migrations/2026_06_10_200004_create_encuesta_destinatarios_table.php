<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encuesta_destinatarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encuesta_id')->constrained('encuestas')->cascadeOnDelete();
            $table->enum('tipo', ['todos', 'unidad_organica', 'rol', 'usuario']);
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->timestamps();

            $table->index(['encuesta_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encuesta_destinatarios');
    }
};
