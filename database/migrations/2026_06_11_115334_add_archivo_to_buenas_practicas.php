<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buenas_practicas', function (Blueprint $table) {
            $table->string('archivo_proyecto')->nullable()->after('evidencias');
        });
    }

    public function down(): void
    {
        Schema::table('buenas_practicas', function (Blueprint $table) {
            $table->dropColumn('archivo_proyecto');
        });
    }
};
