<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\Alerta;
use App\Models\Componente;
use App\Models\Evidencia;
use App\Models\Reconocimiento;
use App\Models\UnidadOrganica;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatosSeeder extends Seeder
{
    public function run(): void
    {
        $unidades   = UnidadOrganica::all();
        $componentes = Componente::all();
        $usuarios   = User::where('estado', 'activo')->get();

        if ($unidades->isEmpty() || $componentes->isEmpty() || $usuarios->isEmpty()) {
            $this->command->warn('Faltan datos base. Ejecuta primero los otros seeders.');
            return;
        }

        // Actividades: ~5 por unidad orgánica
        foreach ($unidades as $unidad) {
            $responsable = $usuarios->where('unidad_organica_id', $unidad->id)->first()
                        ?? $usuarios->random();

            Actividad::factory(5)->create([
                'unidad_organica_id' => $unidad->id,
                'responsable_id'     => $responsable->id,
                'creado_por'         => $responsable->id,
                'componente_id'      => $componentes->random()->id,
            ]);

            // 1 actividad completada por unidad
            Actividad::factory()->completada()->create([
                'unidad_organica_id' => $unidad->id,
                'responsable_id'     => $responsable->id,
                'creado_por'         => $responsable->id,
                'componente_id'      => $componentes->random()->id,
            ]);

            // 1 actividad vencida por unidad
            Actividad::factory()->vencida()->create([
                'unidad_organica_id' => $unidad->id,
                'responsable_id'     => $responsable->id,
                'creado_por'         => $responsable->id,
                'componente_id'      => $componentes->random()->id,
            ]);
        }

        // Evidencias: 2-3 por actividad completada o en proceso
        $actividades = Actividad::whereIn('estado', ['completada', 'en_proceso'])->get();
        foreach ($actividades as $actividad) {
            $subidoPor = $usuarios->random();
            Evidencia::factory(rand(1, 3))->create([
                'actividad_id' => $actividad->id,
                'subido_por'   => $subidoPor->id,
            ]);
        }

        // Alertas: ~3 por usuario activo
        foreach ($usuarios->take(15) as $usuario) {
            $actividad = Actividad::where('unidad_organica_id', $usuario->unidad_organica_id)
                ->inRandomOrder()->first()
                ?? Actividad::inRandomOrder()->first();

            Alerta::factory(rand(2, 4))->create([
                'usuario_id'        => $usuario->id,
                'unidad_organica_id'=> $usuario->unidad_organica_id ?? $unidades->random()->id,
                'actividad_id'      => $actividad?->id,
            ]);
        }

        // Reconocimientos: para los últimos 3 meses
        $meses = [
            ['anio' => 2026, 'mes' => 3],
            ['anio' => 2026, 'mes' => 4],
            ['anio' => 2026, 'mes' => 5],
        ];

        foreach ($meses as $periodo) {
            // Calcular avance real de cada unidad para ese mes
            $rankingUnidades = $unidades->map(function ($unidad) use ($periodo) {
                $total      = Actividad::where('unidad_organica_id', $unidad->id)->count();
                $completadas = Actividad::where('unidad_organica_id', $unidad->id)
                    ->where('estado', 'completada')->count();
                $puntaje    = $total > 0 ? round(($completadas / $total) * 100) : rand(20, 80);

                return [
                    'unidad'     => $unidad,
                    'puntaje'    => $puntaje,
                    'completadas'=> $completadas,
                    'total'      => $total,
                ];
            })->sortByDesc('puntaje')->values();

            foreach ($rankingUnidades as $pos => $item) {
                $medalla = match(true) {
                    $pos === 0 => 'oro',
                    $pos === 1 => 'plata',
                    $pos === 2 => 'bronce',
                    default    => null,
                };

                Reconocimiento::updateOrCreate(
                    [
                        'unidad_organica_id' => $item['unidad']->id,
                        'anio'               => $periodo['anio'],
                        'mes'                => $periodo['mes'],
                    ],
                    [
                        'posicion'             => $pos + 1,
                        'puntaje'              => $item['puntaje'],
                        'avance_global'        => $item['puntaje'],
                        'actividades_total'    => $item['total'],
                        'actividades_completadas' => $item['completadas'],
                        'medalla'              => $medalla,
                    ]
                );
            }
        }

        $this->command->info('Datos de prueba generados: actividades, evidencias, alertas y reconocimientos.');
    }
}
