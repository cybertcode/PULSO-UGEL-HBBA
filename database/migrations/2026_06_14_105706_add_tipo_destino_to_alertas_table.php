<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            // individual: un usuario específico (comportamiento actual)
            // unidad: todos los usuarios de una unidad orgánica
            // todos: todos los usuarios de la institución con permiso alertas.ver
            $table->enum('tipo_destino', ['individual', 'unidad', 'todos'])
                  ->default('individual')
                  ->after('modulo');
        });
    }

    public function down(): void
    {
        Schema::table('alertas', function (Blueprint $table) {
            $table->dropColumn('tipo_destino');
        });
    }
};
