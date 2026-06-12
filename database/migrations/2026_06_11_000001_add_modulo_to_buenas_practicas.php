<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buenas_practicas', function (Blueprint $table) {
            // modulo: sci = Sistema de Control Interno, integridad = Modelo de Integridad
            $table->string('modulo')->default('sci')->after('categoria');
            // calificacion del SCI responsable al aprobar (1-5)
            $table->unsignedTinyInteger('calificacion')->nullable()->after('observaciones');
            // feedback del SCI al aprobar/rechazar
            $table->text('feedback_sci')->nullable()->after('calificacion');
        });
    }

    public function down(): void
    {
        Schema::table('buenas_practicas', function (Blueprint $table) {
            $table->dropColumn(['modulo', 'calificacion', 'feedback_sci']);
        });
    }
};
