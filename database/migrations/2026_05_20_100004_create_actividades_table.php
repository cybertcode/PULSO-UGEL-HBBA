<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->nullable()->unique(); // ej: SCI-2024-001
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->foreignId('componente_id')->constrained('componentes')->restrictOnDelete();
            $table->foreignId('unidad_organica_id')->nullable()->constrained('unidades_organicas')->nullOnDelete();
            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('numero_sgd', 50)->nullable();      // N° expediente SGDOC
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_limite');
            $table->date('fecha_cumplimiento')->nullable();
            $table->unsignedTinyInteger('avance')->default(0); // 0-100 %
            $table->enum('estado', [
                'pendiente',
                'en_proceso',
                'completada',
                'vencida',
                'cancelada',
            ])->default('pendiente');
            $table->enum('prioridad', ['alta', 'media', 'baja'])->default('media');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['componente_id', 'estado']);
            $table->index(['unidad_organica_id', 'estado']);
            $table->index('fecha_limite');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
