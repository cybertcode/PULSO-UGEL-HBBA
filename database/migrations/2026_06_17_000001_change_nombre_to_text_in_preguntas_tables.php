<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sci_preguntas', function (Blueprint $table) {
            $table->text('nombre')->change();
            $table->text('link_ficha')->nullable()->change();
        });

        Schema::table('integridad_preguntas', function (Blueprint $table) {
            $table->text('nombre')->change();
            $table->text('link_ficha')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sci_preguntas', function (Blueprint $table) {
            $table->string('nombre', 255)->change();
            $table->string('link_ficha', 255)->nullable()->change();
        });

        Schema::table('integridad_preguntas', function (Blueprint $table) {
            $table->string('nombre', 255)->change();
            $table->string('link_ficha', 255)->nullable()->change();
        });
    }
};
