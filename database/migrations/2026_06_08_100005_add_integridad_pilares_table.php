<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integridad_compromisos', function (Blueprint $table) {
            $table->id();
            $table->enum('pilar', ['compromiso', 'cultura', 'regulacion', 'control']);
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->tinyInteger('avance')->default(0);
            $table->enum('estado', ['pendiente', 'en_proceso', 'completado'])->default('pendiente');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->unsignedBigInteger('responsable_id')->nullable();
            $table->text('evidencia')->nullable();
            $table->text('observaciones')->nullable();
            $table->year('anio')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('responsable_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integridad_compromisos');
    }
};
