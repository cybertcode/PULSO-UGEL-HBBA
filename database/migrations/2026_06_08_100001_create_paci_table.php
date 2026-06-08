<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paci', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->year('anio');
            $table->text('descripcion')->nullable();
            $table->string('numero_resolucion', 100)->nullable();
            $table->date('fecha_aprobacion')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->enum('estado', ['borrador', 'aprobado', 'en_ejecucion', 'cerrado'])->default('borrador');
            $table->tinyInteger('avance')->default(0);
            $table->unsignedBigInteger('creado_por')->nullable();
            $table->string('archivo', 500)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('creado_por')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('paci_actividades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paci_id');
            $table->unsignedBigInteger('actividad_id');
            $table->timestamps();

            $table->foreign('paci_id')->references('id')->on('paci')->cascadeOnDelete();
            $table->foreign('actividad_id')->references('id')->on('actividades')->cascadeOnDelete();
            $table->unique(['paci_id', 'actividad_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paci_actividades');
        Schema::dropIfExists('paci');
    }
};
