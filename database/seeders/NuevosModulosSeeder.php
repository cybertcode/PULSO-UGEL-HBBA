<?php

namespace Database\Seeders;

use App\Models\SciPregunta;
use App\Models\UnidadOrganica;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NuevosModulosSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $this->seedActividadesSci($now);

        $this->call(IntegridadDatosPruebaSeeder::class);

        $this->command->info('✓ NuevosModulosSeeder: actividades SCI e Integridad generadas.');
    }

    private function seedActividadesSci(Carbon $now): void
    {
        $sciUser   = User::where('email', 'sci@ugelhuacaybamba.edu.pe')->first();
        $adminUser = User::where('email', 'admin@admin.com')->first();
        $creador   = $sciUser ?? $adminUser;

        if (!$creador) {
            $this->command->warn('No se encontró usuario SCI ni admin para actividades SCI.');
            return;
        }

        $unidades  = UnidadOrganica::all();
        $preguntas = SciPregunta::with('componente.eje')->where('activo', true)->get();

        if ($unidades->isEmpty() || $preguntas->isEmpty()) {
            $this->command->warn('Faltan unidades o preguntas SCI. Verifica EstructuraSciIntegridadSeeder.');
            return;
        }

        $anio = 2026;

        // Limpiar actividades SCI previas de este año para evitar duplicados
        $existentes = DB::table('actividades')
            ->where('modulo', 'sci')
            ->where('anio', $anio)
            ->pluck('id');

        DB::table('actividad_responsables')->whereIn('actividad_id', $existentes)->delete();
        DB::table('actividades')->whereIn('id', $existentes)->delete();

        // Distribución de estados realista para un sistema en funcionamiento
        $estadosDistribucion = [
            'completada', 'completada', 'completada', 'completada',   // 4 completadas
            'en_proceso', 'en_proceso', 'en_proceso', 'en_proceso', 'en_proceso', // 5 en proceso
            'pendiente', 'pendiente', 'pendiente',                    // 3 pendientes
            'vencida', 'vencida',                                     // 2 vencidas
            'observado', 'observado',                                 // 2 observadas
            'pendiente', 'pendiente',                                 // 2 más pendientes
        ];

        $contador = 1;
        foreach ($preguntas as $pregunta) {
            $unidad = $unidades->get(($contador - 1) % $unidades->count());

            $responsable = DB::table('users')
                ->where('unidad_organica_id', $unidad->id)
                ->where('estado', 'activo')
                ->first() ?? (object)['id' => $creador->id];

            $estado = $estadosDistribucion[$contador - 1] ?? 'pendiente';

            $avance = match($estado) {
                'completada' => 100,
                'en_proceso' => rand(25, 85),
                'observado'  => rand(10, 50),
                'vencida'    => rand(0, 40),
                default      => 0,
            };

            $prioridad   = $contador <= 6 ? 'alta' : ($contador <= 12 ? 'media' : 'baja');
            $diasInicio  = rand(30, 90);
            $fechaInicio = $now->copy()->subDays($diasInicio)->toDateString();
            $fechaLimite = $estado === 'vencida'
                ? $now->copy()->subDays(rand(1, 20))->toDateString()
                : $now->copy()->addDays(rand(10, 60))->toDateString();

            $id = DB::table('actividades')->insertGetId([
                'modulo'             => 'sci',
                'sci_pregunta_id'    => $pregunta->id,
                'unidad_organica_id' => $unidad->id,
                'codigo'             => 'SCI-' . $anio . '-' . str_pad($contador, 3, '0', STR_PAD_LEFT),
                'nombre'             => $pregunta->nombre,
                'descripcion'        => 'Actividad de Control Interno vinculada al ' . ($pregunta->componente->eje->nombre ?? 'eje SCI') . '.',
                'anio'               => $anio,
                'numero_sgd'         => 'SGD-' . $anio . '-' . str_pad($contador + 100, 4, '0', STR_PAD_LEFT),
                'fecha_inicio'       => $fechaInicio,
                'fecha_limite'       => $fechaLimite,
                'fecha_cumplimiento' => $estado === 'completada'
                    ? $now->copy()->subDays(rand(1, 15))->toDateString()
                    : null,
                'estado'             => $estado,
                'avance'             => $avance,
                'prioridad'          => $prioridad,
                'observaciones'      => $estado === 'observado'
                    ? 'Requiere corrección de evidencias antes del cierre del período.'
                    : null,
                'creado_por'         => $creador->id,
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);

            DB::table('actividad_responsables')->insert([
                'actividad_id' => $id,
                'user_id'      => $responsable->id,
                'tipo'         => 'principal',
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);

            // Agregar colaborador en actividades en_proceso y observado
            if (in_array($estado, ['en_proceso', 'observado'])) {
                $colaborador = DB::table('users')
                    ->where('estado', 'activo')
                    ->where('id', '!=', $responsable->id)
                    ->inRandomOrder()
                    ->first();

                if ($colaborador) {
                    DB::table('actividad_responsables')->insertOrIgnore([
                        'actividad_id' => $id,
                        'user_id'      => $colaborador->id,
                        'tipo'         => 'colaborador',
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]);
                }
            }

            $contador++;
        }

        $total = $contador - 1;
        $this->command->info("✓ {$total} actividades SCI insertadas (anio {$anio}).");
    }
}
