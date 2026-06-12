<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion_institucional', function (Blueprint $table) {
            // Nuevas FK a usuarios
            $table->unsignedBigInteger('director_id')->nullable()->after('director');
            $table->unsignedBigInteger('coordinador_sci_id')->nullable()->after('coordinador_sci');

            $table->foreign('director_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('coordinador_sci_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('configuracion_institucional', function (Blueprint $table) {
            $table->dropForeign(['director_id']);
            $table->dropForeign(['coordinador_sci_id']);
            $table->dropColumn(['director_id', 'coordinador_sci_id']);
        });
    }
};
