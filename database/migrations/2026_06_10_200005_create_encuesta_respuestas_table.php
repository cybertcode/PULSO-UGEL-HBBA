<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encuesta_respuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encuesta_id')->constrained('encuestas')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('completada')->default(false);
            $table->timestamp('iniciada_at')->nullable();
            $table->timestamp('completada_at')->nullable();
            $table->timestamps();

            $table->unique(['encuesta_id', 'usuario_id']);
            $table->index(['encuesta_id', 'completada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encuesta_respuestas');
    }
};
