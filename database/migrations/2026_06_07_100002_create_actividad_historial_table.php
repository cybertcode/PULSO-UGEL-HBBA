<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividad_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('campo', 60);            // nombre del campo modificado
            $table->text('valor_anterior')->nullable();
            $table->text('valor_nuevo')->nullable();
            $table->string('descripcion')->nullable(); // resumen legible
            $table->timestamps();

            $table->index(['actividad_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividad_historial');
    }
};
