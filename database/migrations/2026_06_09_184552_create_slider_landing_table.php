<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('slider_landing', function (Blueprint $table) {
            $table->id();
            $table->string('tipo')->default('noticia');       // noticia | evento | normativa
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('imagen_url')->nullable();
            $table->string('color_gradiente')->default('linear-gradient(135deg,#0a0a2e,#1a1a6e 40%,#7367f0)');
            $table->string('etiqueta')->nullable();
            $table->string('url_accion')->nullable();
            $table->string('texto_accion')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slider_landing');
    }
};
