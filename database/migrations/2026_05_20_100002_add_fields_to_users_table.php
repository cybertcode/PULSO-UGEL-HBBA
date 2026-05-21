<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('dni', 8)->nullable()->after('name');
            $table->string('cargo')->nullable()->after('dni');
            $table->foreignId('unidad_organica_id')->nullable()->after('cargo')
                ->constrained('unidades_organicas')->nullOnDelete();
            $table->enum('estado', ['activo', 'inactivo', 'pendiente'])->default('pendiente')->after('unidad_organica_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['unidad_organica_id']);
            $table->dropColumn(['dni', 'cargo', 'unidad_organica_id', 'estado']);
        });
    }
};
