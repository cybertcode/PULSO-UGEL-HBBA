<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buenas_practicas', function (Blueprint $table) {
            $table->foreignId('propuesto_por')
                  ->nullable()
                  ->after('creado_por')
                  ->constrained('users')
                  ->nullOnDelete();
            // estado agrega 'propuesta' — lo manejamos como string, ya era string
        });
    }

    public function down(): void
    {
        Schema::table('buenas_practicas', function (Blueprint $table) {
            $table->dropForeign(['propuesto_por']);
            $table->dropColumn('propuesto_por');
        });
    }
};
