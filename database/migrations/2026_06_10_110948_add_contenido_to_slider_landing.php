<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('slider_landing', function (Blueprint $table) {
            $table->longText('contenido')->nullable()->after('descripcion');
            $table->string('autor', 100)->nullable()->after('contenido');
            $table->string('imagen_portada_url', 500)->nullable()->after('autor');
        });
    }

    public function down(): void
    {
        Schema::table('slider_landing', function (Blueprint $table) {
            $table->dropColumn(['contenido', 'autor', 'imagen_portada_url']);
        });
    }
};
