<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Crear tabla pivot
        Schema::create('cargo_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('cargo_id')->constrained('cargos')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'cargo_id']);
        });

        // 2. Migrar cargo_id actual de users a la pivot
        DB::table('users')
            ->whereNotNull('cargo_id')
            ->select('id', 'cargo_id')
            ->get()
            ->each(function ($u) {
                DB::table('cargo_user')->insert([
                    'user_id'    => $u->id,
                    'cargo_id'   => $u->cargo_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        // 3. Eliminar FK y columna cargo_id de users
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cargo_id']);
            $table->dropColumn('cargo_id');
        });
    }

    public function down(): void
    {
        // Restaurar columna cargo_id con el primer cargo de la pivot
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('cargo_id')->nullable()->after('dni');
            $table->foreign('cargo_id')->references('id')->on('cargos')->nullOnDelete();
        });

        DB::table('cargo_user')->get()->each(function ($row) {
            DB::table('users')
                ->where('id', $row->user_id)
                ->whereNull('cargo_id')
                ->update(['cargo_id' => $row->cargo_id]);
        });

        Schema::dropIfExists('cargo_user');
    }
};
