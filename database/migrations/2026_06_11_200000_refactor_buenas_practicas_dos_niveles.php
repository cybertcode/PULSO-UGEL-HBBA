<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buenas_practicas', function (Blueprint $table) {
            // Campos para concurso externo (MINEDU / DRE)
            $table->string('nivel_externo')->nullable()->after('puntaje_comision'); // 'minedu' | 'dre'
            $table->date('fecha_concurso_externo')->nullable()->after('nivel_externo');
            $table->string('resultado_externo')->nullable()->after('fecha_concurso_externo'); // texto libre con resultado
        });

        // Renombrar estados legacy del concurso de un nivel a dos niveles:
        // en_concurso → elegible  (era "admitido al concurso UGEL")
        // ganador     → ganador_ugel
        DB::table('buenas_practicas')
            ->where('estado', 'en_concurso')
            ->update(['estado' => 'elegible']);

        DB::table('buenas_practicas')
            ->where('estado', 'ganador')
            ->update(['estado' => 'ganador_ugel']);
    }

    public function down(): void
    {
        DB::table('buenas_practicas')
            ->where('estado', 'elegible')
            ->update(['estado' => 'en_concurso']);
        DB::table('buenas_practicas')
            ->where('estado', 'ganador_ugel')
            ->update(['estado' => 'ganador']);

        Schema::table('buenas_practicas', function (Blueprint $table) {
            $table->dropColumn(['nivel_externo', 'fecha_concurso_externo', 'resultado_externo']);
        });
    }
};
