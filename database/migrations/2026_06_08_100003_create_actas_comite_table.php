<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actas_comite', function (Blueprint $table) {
            $table->id();
            $table->string('numero_acta', 50);
            $table->string('titulo');
            $table->date('fecha_sesion');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->string('lugar', 200)->nullable();
            $table->enum('tipo_sesion', ['ordinaria', 'extraordinaria'])->default('ordinaria');
            $table->text('agenda')->nullable();
            $table->text('desarrollo')->nullable();
            $table->text('acuerdos')->nullable();
            $table->text('compromisos')->nullable();
            $table->enum('estado', ['convocada', 'realizada', 'cancelada'])->default('convocada');
            $table->unsignedBigInteger('secretario_id')->nullable();
            $table->string('archivo_acta', 500)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('secretario_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('acta_participantes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('acta_id');
            $table->unsignedBigInteger('usuario_id');
            $table->boolean('asistio')->default(false);
            $table->string('cargo_en_comite', 100)->nullable();
            $table->timestamps();

            $table->foreign('acta_id')->references('id')->on('actas_comite')->cascadeOnDelete();
            $table->foreign('usuario_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['acta_id', 'usuario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acta_participantes');
        Schema::dropIfExists('actas_comite');
    }
};
