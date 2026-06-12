<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('normativas', function (Blueprint $table) {
            $table->id();

            // Identificación
            $table->string('nombre');
            $table->string('codigo', 100)->nullable();           // Ej: D.S. 054-2018-PCM
            $table->text('descripcion')->nullable();

            // Clasificación
            $table->string('tipo', 50)->default('otro');         // ley, decreto, resolucion, directiva, manual, otro
            $table->string('alcance', 30)->default('nacional');  // nacional, regional, institucional
            $table->string('modulo', 30)->default('general');    // sci, integridad, general

            // Recurso: archivo O link (uno o ambos)
            $table->string('archivo_path')->nullable();          // ruta en storage/public
            $table->string('archivo_nombre_original')->nullable();
            $table->string('link_externo')->nullable();          // URL externa (web, drive, etc.)

            // Tutorial / video
            $table->string('tutorial_url')->nullable();          // enlace YouTube u otro
            $table->string('tutorial_tipo', 20)->nullable();     // youtube, pdf, link

            // Meta
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_vigencia')->nullable();          // null = vigente sin fecha límite
            $table->boolean('vigente')->default(true);
            $table->string('entidad_emisora')->nullable();       // PCM, CGBV, MINEDU...

            // Observación interna
            $table->text('observacion')->nullable();

            // Orden de presentación
            $table->unsignedInteger('orden')->default(0);

            $table->unsignedBigInteger('creado_por')->nullable();
            $table->foreign('creado_por')->references('id')->on('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('normativas');
    }
};
