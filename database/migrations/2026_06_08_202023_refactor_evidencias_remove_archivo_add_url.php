<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evidencias', function (Blueprint $table) {
            $table->dropColumn(['archivo_ruta', 'archivo_nombre', 'archivo_tipo', 'archivo_tamanio']);
        });

        Schema::table('evidencias', function (Blueprint $table) {
            $table->string('url_documento', 500)->nullable()->after('descripcion');
        });
    }

    public function down(): void
    {
        Schema::table('evidencias', function (Blueprint $table) {
            $table->dropColumn('url_documento');
        });

        Schema::table('evidencias', function (Blueprint $table) {
            $table->string('archivo_ruta', 500)->default('');
            $table->string('archivo_nombre')->default('');
            $table->string('archivo_tipo', 100)->nullable();
            $table->unsignedInteger('archivo_tamanio')->nullable();
        });
    }
};
