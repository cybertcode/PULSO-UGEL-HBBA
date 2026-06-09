<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Crear cargos faltantes a partir de los valores actuales en users.cargo
        $nombresExistentes = DB::table('cargos')->pluck('nombre')->map(fn($n) => strtolower(trim($n)));

        DB::table('users')
            ->whereNotNull('cargo')
            ->where('cargo', '!=', '')
            ->pluck('cargo')
            ->unique()
            ->each(function ($nombre) use ($nombresExistentes) {
                if (!$nombresExistentes->contains(strtolower(trim($nombre)))) {
                    DB::table('cargos')->insert([
                        'nombre'     => trim($nombre),
                        'activo'     => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

        // 2. Añadir columna cargo_id
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('cargo_id')->nullable()->after('cargo');
        });

        // 3. Poblar cargo_id a partir del nombre en cargo
        DB::table('cargos')->get(['id', 'nombre'])->each(function ($c) {
            DB::table('users')
                ->where('cargo', $c->nombre)
                ->update(['cargo_id' => $c->id]);
        });

        // 4. Agregar FK
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('cargo_id')->references('id')->on('cargos')->nullOnDelete();
        });

        // 5. Eliminar columna cargo (string)
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('cargo');
        });
    }

    public function down(): void
    {
        // Restaurar columna cargo string
        Schema::table('users', function (Blueprint $table) {
            $table->string('cargo')->nullable()->after('dni');
        });

        // Recuperar nombres desde la FK
        DB::table('users')->whereNotNull('cargo_id')->each(function ($u) {
            $cargo = DB::table('cargos')->find($u->cargo_id);
            if ($cargo) {
                DB::table('users')->where('id', $u->id)->update(['cargo' => $cargo->nombre]);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cargo_id']);
            $table->dropColumn('cargo_id');
        });
    }
};
