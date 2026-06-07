<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_ranking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_organica_id')->constrained('unidades_organicas')->cascadeOnDelete();
            $table->unsignedTinyInteger('posicion');
            $table->unsignedTinyInteger('posicion_anterior')->nullable();
            $table->decimal('porcentaje', 5, 2)->default(0);
            $table->year('anio');
            $table->unsignedTinyInteger('mes');
            $table->timestamps();

            $table->index(['anio', 'mes']);
            $table->index(['unidad_organica_id', 'anio', 'mes']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_ranking');
    }
};
