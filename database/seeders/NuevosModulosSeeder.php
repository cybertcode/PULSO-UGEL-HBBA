<?php

namespace Database\Seeders;

use App\Models\SciEje;
use App\Models\SciComponente;
use App\Models\SciPregunta;
use App\Models\UnidadOrganica;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NuevosModulosSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Sembrar estructura SCI y actividades de prueba
        $this->seedActividadesSci($now);

        // Delegar datos de integridad al seeder especializado
        $this->call(IntegridadDatosPruebaSeeder::class);

        $this->command->info('✓ NuevosModulosSeeder: actividades SCI e integridad generadas.');
    }

    private function seedActividadesSci(Carbon $now): void
    {
        $sciUser   = User::where('email', 'sci@ugelhuacaybamba.edu.pe')->first();
        $adminUser = User::where('email', 'admin@admin.com')->first();
        $creador   = $sciUser ?? $adminUser;

        if (!$creador) {
            $this->command->warn('No se encontró usuario SCI ni admin para seedear actividades SCI.');
            return;
        }

        $unidades = UnidadOrganica::all();
        if ($unidades->isEmpty()) return;

        $preguntas = SciPregunta::with('componente.eje')->get();
        if ($preguntas->isEmpty()) {
            $this->command->warn('No hay preguntas SCI. Asegúrate que RolesPermisosSeeder o la estructura SCI esté poblada.');
            return;
        }

        $anio = 2026;

        // Eliminar actividades SCI de prueba existentes para evitar duplicados
        $existentes = DB::table('actividades')
            ->where('modulo', 'sci')
            ->where('anio', $anio)
            ->pluck('id');

        DB::table('actividad_responsables')->whereIn('actividad_id', $existentes)->delete();
        DB::table('actividades')->whereIn('id', $existentes)->delete();

        $contador = 1;
        foreach ($preguntas->take(18) as $pregunta) {
            $unidad      = $unidades->get(($contador - 1) % $unidades->count());
            $responsable = User::where('unidad_organica_id', $unidad->id)
                               ->where('estado', 'activo')
                               ->first() ?? $creador;

            $estado = match(true) {
                $contador <= 4  => 'completada',
                $contador <= 9  => 'en_proceso',
                $contador <= 12 => 'pendiente',
                $contador <= 14 => 'vencida',
                $contador <= 16 => 'observado',
                default         => 'pendiente',
            };

            $avance = match($estado) {
                'completada' => 100,
                'en_proceso' => rand(25, 85),
                'observado'  => rand(10, 50),
                'vencida'    => rand(0, 40),
                default      => 0,
            };

            $fechaInicio = $now->copy()->subDays(rand(30, 90))->toDateString();
            $fechaLimite = $estado === 'vencida'
                ? $now->copy()->subDays(rand(1, 20))->toDateString()
                : $now->copy()->addDays(rand(10, 60))->toDateString();

            $codigo = 'SCI-' . $anio . '-' . str_pad($contador, 3, '0', STR_PAD_LEFT);

            $id = DB::table('actividades')->insertGetId([
                'modulo'          => 'sci',
                'sci_pregunta_id' => $pregunta->id,
                'unidad_organica_id' => $unidad->id,
                'codigo'          => $codigo,
                'nombre'          => $pregunta->nombre,
                'anio'            => $anio,
                'fecha_inicio'    => $fechaInicio,
                'fecha_limite'    => $fechaLimite,
                'fecha_cumplimiento' => $estado === 'completada'
                    ? $now->copy()->subDays(rand(1, 15))->toDateString()
                    : null,
                'estado'          => $estado,
                'avance'          => $avance,
                'prioridad'       => $contador <= 6 ? 'alta' : ($contador <= 12 ? 'media' : 'baja'),
                'creado_por'      => $creador->id,
                'descripcion'     => null,
                'observaciones'   => $estado === 'observado'
                    ? 'Requiere corrección de evidencias antes del cierre.'
                    : null,
                'numero_sgd'      => 'SGD-' . $anio . '-' . str_pad($contador + 100, 4, '0', STR_PAD_LEFT),
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);

            DB::table('actividad_responsables')->insert([
                'actividad_id' => $id,
                'user_id'      => $responsable->id,
                'tipo'         => 'principal',
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);

            $contador++;
        }

        $this->command->info("✓ {$contador} actividades SCI insertadas.");
    }
}
