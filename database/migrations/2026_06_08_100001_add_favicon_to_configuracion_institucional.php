<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion_institucional', function (Blueprint $table) {
            $table->string('favicon_ruta', 500)->nullable()->after('logo_ruta');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion_institucional', function (Blueprint $table) {
            $table->dropColumn('favicon_ruta');
        });
    }
};
