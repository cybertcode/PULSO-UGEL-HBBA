<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sci_ejes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->year('anio');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['anio', 'activo']);
        });

        Schema::create('sci_componentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eje_id')->constrained('sci_ejes')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('icono', 80)->nullable();
            $table->text('descripcion')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['eje_id', 'activo']);
        });

        Schema::create('sci_preguntas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('componente_id')->constrained('sci_componentes')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('link_ficha')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['componente_id', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sci_preguntas');
        Schema::dropIfExists('sci_componentes');
        Schema::dropIfExists('sci_ejes');
    }
};
