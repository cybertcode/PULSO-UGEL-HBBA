<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recomendaciones', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('tipo')->default('recomendacion'); // observacion, recomendacion, mejora
            $table->foreignId('actividad_id')->nullable()->constrained('actividades')->nullOnDelete();
            $table->foreignId('unidad_organica_id')->nullable()->constrained('unidades_organicas')->nullOnDelete();
            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('estado')->default('pendiente'); // pendiente, en_proceso, atendida, rechazada
            $table->string('prioridad')->default('media'); // alta, media, baja
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_limite')->nullable();
            $table->date('fecha_atencion')->nullable();
            $table->string('numero_sgd')->nullable();
            $table->string('origen')->nullable(); // SCI, OCI, DRE, Auditoría, Autocontrol
            $table->string('modulo')->default('sci'); // sci, integridad
            $table->text('observaciones')->nullable();
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recomendaciones');
    }
};
