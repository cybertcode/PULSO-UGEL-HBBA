<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('componentes', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('numero')->unique(); // 1-9
            $table->string('nombre');
            $table->string('icono', 60)->nullable();         // tabler icon class
            $table->enum('tipo', ['sci', 'integridad', 'ambos'])->default('ambos');
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('componentes');
    }
};
