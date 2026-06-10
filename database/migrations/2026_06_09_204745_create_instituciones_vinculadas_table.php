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
        Schema::create('instituciones_vinculadas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('sigla', 30);
            $table->string('logo_ruta')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('color_acento', 20)->default('#1a237e');
            $table->string('url_sitio')->nullable();
            $table->string('descripcion')->nullable();
            $table->unsignedTinyInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instituciones_vinculadas');
    }
};
