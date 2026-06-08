<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buenas_practicas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('categoria')->default('gestion'); // gestion, transparencia, integridad, innovacion, participacion
            $table->foreignId('unidad_organica_id')->nullable()->constrained('unidades_organicas')->nullOnDelete();
            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('estado')->default('en_implementacion'); // en_implementacion, completada, pendiente, suspendida
            $table->unsignedTinyInteger('avance')->default(0); // 0-100
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_termino')->nullable();
            $table->string('numero_sgd')->nullable();
            $table->string('impacto')->nullable(); // alto, medio, bajo
            $table->text('evidencias')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buenas_practicas');
    }
};
