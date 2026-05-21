<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actividad_id')->nullable()->constrained('actividades')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('unidad_organica_id')->nullable()->constrained('unidades_organicas')->nullOnDelete();
            $table->string('titulo');
            $table->text('mensaje');
            $table->enum('tipo', [
                'vencimiento',      // actividad por vencer
                'avance_bajo',      // avance por debajo del umbral
                'evidencia_falta',  // actividad sin evidencia
                'sistema',          // alerta manual del sistema
            ])->default('sistema');
            $table->enum('prioridad', ['alta', 'media', 'baja'])->default('media');
            $table->boolean('leida')->default(false);
            $table->timestamp('leida_at')->nullable();
            $table->timestamps();

            $table->index(['usuario_id', 'leida']);
            $table->index(['prioridad', 'leida']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
