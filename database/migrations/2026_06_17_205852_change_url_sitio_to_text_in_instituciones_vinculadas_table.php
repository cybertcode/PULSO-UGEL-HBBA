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
        Schema::table('instituciones_vinculadas', function (Blueprint $table) {
            $table->text('url_sitio')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('instituciones_vinculadas', function (Blueprint $table) {
            $table->string('url_sitio', 255)->nullable()->change();
        });
    }
};
