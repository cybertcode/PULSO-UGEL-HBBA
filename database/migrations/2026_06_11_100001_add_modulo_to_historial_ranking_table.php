<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('historial_ranking', function (Blueprint $table) {
            $table->enum('modulo', ['sci', 'integridad', 'ambos'])->default('ambos')->after('mes');
            $table->index(['modulo', 'anio', 'mes']);
        });
    }

    public function down(): void
    {
        Schema::table('historial_ranking', function (Blueprint $table) {
            $table->dropIndex(['modulo', 'anio', 'mes']);
            $table->dropColumn('modulo');
        });
    }
};
