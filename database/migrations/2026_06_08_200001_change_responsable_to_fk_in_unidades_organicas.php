<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Primero agregamos la nueva columna FK
        Schema::table('unidades_organicas', function (Blueprint $table) {
            $table->foreignId('responsable_id')->nullable()->after('responsable')
                ->constrained('users')->nullOnDelete();
        });

        // Migramos datos: intentamos hacer match por nombre con usuarios existentes
        DB::statement("
            UPDATE unidades_organicas u
            JOIN users usr ON usr.name = u.responsable
            SET u.responsable_id = usr.id
            WHERE u.responsable IS NOT NULL AND u.responsable_id IS NULL
        ");

        // Eliminamos la columna string antigua
        Schema::table('unidades_organicas', function (Blueprint $table) {
            $table->dropColumn('responsable');
        });
    }

    public function down(): void
    {
        Schema::table('unidades_organicas', function (Blueprint $table) {
            $table->string('responsable')->nullable()->after('sigla');
        });

        // Restaurar nombres desde la FK
        DB::statement("
            UPDATE unidades_organicas u
            JOIN users usr ON usr.id = u.responsable_id
            SET u.responsable = usr.name
            WHERE u.responsable_id IS NOT NULL
        ");

        Schema::table('unidades_organicas', function (Blueprint $table) {
            $table->dropForeign(['responsable_id']);
            $table->dropColumn('responsable_id');
        });
    }
};
