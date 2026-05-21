<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reconocimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_organica_id')->constrained('unidades_organicas')->cascadeOnDelete();
            $table->year('anio');
            $table->unsignedTinyInteger('mes')->nullable();    // null = anual
            $table->unsignedTinyInteger('posicion');           // lugar en el ranking (1, 2, 3...)
            $table->decimal('puntaje', 5, 2)->default(0);      // 0.00 - 100.00
            $table->unsignedTinyInteger('avance_global')->default(0); // % promedio
            $table->unsignedSmallInteger('actividades_total')->default(0);
            $table->unsignedSmallInteger('actividades_completadas')->default(0);
            $table->string('medalla', 20)->nullable();          // oro, plata, bronce, mencion
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['unidad_organica_id', 'anio', 'mes']);
            $table->index(['anio', 'mes', 'posicion']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reconocimientos');
    }
};
