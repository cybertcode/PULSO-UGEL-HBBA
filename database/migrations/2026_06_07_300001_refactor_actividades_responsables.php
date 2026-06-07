<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reestructura la asignación de responsables:
 * - Crea tabla pivote actividad_responsables (N:M con tipo)
 * - Elimina columna responsable_id de actividades
 * - Mantiene compatibilidad: migra datos existentes al pivote
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividad_responsables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('tipo', ['principal', 'colaborador', 'supervisor'])->default('principal');
            $table->timestamps();

            $table->unique(['actividad_id', 'user_id']); // un usuario, un rol por actividad
            $table->index(['actividad_id', 'tipo']);
        });

        // Migrar datos existentes antes de eliminar la columna
        if (Schema::hasColumn('actividades', 'responsable_id')) {
            \Illuminate\Support\Facades\DB::table('actividades')
                ->whereNotNull('responsable_id')
                ->get(['id', 'responsable_id'])
                ->each(function ($row) {
                    \Illuminate\Support\Facades\DB::table('actividad_responsables')->insertOrIgnore([
                        'actividad_id' => $row->id,
                        'user_id'      => $row->responsable_id,
                        'tipo'         => 'principal',
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                });

            Schema::table('actividades', function (Blueprint $table) {
                $table->dropForeign(['responsable_id']);
                $table->dropColumn('responsable_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
        });

        // Restaurar el primer responsable principal como responsable_id
        \Illuminate\Support\Facades\DB::table('actividad_responsables')
            ->where('tipo', 'principal')
            ->get(['actividad_id', 'user_id'])
            ->each(function ($row) {
                \Illuminate\Support\Facades\DB::table('actividades')
                    ->where('id', $row->actividad_id)
                    ->update(['responsable_id' => $row->user_id]);
            });

        Schema::dropIfExists('actividad_responsables');
    }
};
