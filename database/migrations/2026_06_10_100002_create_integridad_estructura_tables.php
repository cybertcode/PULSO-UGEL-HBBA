<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integridad_etapas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->year('anio');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['anio', 'activo']);
        });

        Schema::create('integridad_componentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etapa_id')->constrained('integridad_etapas')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('icono', 80)->nullable();
            $table->text('descripcion')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['etapa_id', 'activo']);
        });

        Schema::create('integridad_preguntas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('componente_id')->constrained('integridad_componentes')->cascadeOnDelete();
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
        Schema::dropIfExists('integridad_preguntas');
        Schema::dropIfExists('integridad_componentes');
        Schema::dropIfExists('integridad_etapas');
    }
};
