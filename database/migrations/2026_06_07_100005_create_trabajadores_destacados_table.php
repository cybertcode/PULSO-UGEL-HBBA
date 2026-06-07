<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trabajadores_destacados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_organica_id')->nullable()->constrained('unidades_organicas')->nullOnDelete();
            $table->string('nombre');
            $table->string('cargo')->nullable();
            $table->string('dni', 8)->nullable();
            $table->string('correo', 100)->nullable();
            $table->string('foto_ruta', 500)->nullable();
            // Indicadores de evaluación individual (0-100)
            $table->decimal('puntaje_cumplimiento',  5, 2)->default(0);
            $table->decimal('puntaje_puntualidad',   5, 2)->default(0);
            $table->decimal('puntaje_participacion', 5, 2)->default(0);
            $table->decimal('puntaje_responsabilidad',5, 2)->default(0);
            $table->decimal('puntaje_total',         5, 2)->storedAs(
                '(puntaje_cumplimiento + puntaje_puntualidad + puntaje_participacion + puntaje_responsabilidad) / 4'
            );
            $table->year('anio');
            $table->unsignedTinyInteger('mes')->nullable(); // null = evaluación anual
            $table->string('categoria', 60)->nullable();    // Control Interno, Modelo Integridad, Buenas Prácticas
            $table->string('motivo')->nullable();
            $table->string('numero_resolucion', 60)->nullable(); // RD N° 1457-2024
            $table->string('resolucion_ruta', 500)->nullable();  // PDF de la RD
            $table->boolean('activo')->default(true);
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['anio', 'mes']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajadores_destacados');
    }
};
