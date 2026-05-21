<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete();
            $table->foreignId('subido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('numero_sgd', 50)->nullable();      // N° SGDOC del documento
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('archivo_ruta', 500);               // path en storage
            $table->string('archivo_nombre');
            $table->string('archivo_tipo', 100)->nullable();   // mime type
            $table->unsignedInteger('archivo_tamanio')->nullable(); // bytes
            $table->enum('estado', [
                'pendiente',
                'validado',
                'rechazado',
            ])->default('pendiente');
            $table->foreignId('validado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validado_at')->nullable();
            $table->text('motivo_rechazo')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['actividad_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidencias');
    }
};
