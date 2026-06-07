<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\ActividadHistorial;
use App\Models\Alerta;
use App\Models\Componente;
use App\Models\Evidencia;
use App\Models\Reconocimiento;
use App\Models\UnidadOrganica;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatosSeeder extends Seeder
{
    public function run(): void
    {
        $unidades    = UnidadOrganica::all();
        $componentes = Componente::all();
        $usuarios    = User::where('estado', 'activo')->get();

        if ($unidades->isEmpty() || $componentes->isEmpty() || $usuarios->isEmpty()) {
            $this->command->warn('Faltan datos base. Verifica que UnidadesOrganicasSeeder, ComponentesSeeder y UsuariosSeeder hayan corrido primero.');
            return;
        }

        // ─── 1. Actividades: variedad de escenarios reales por unidad ─────────
        foreach ($unidades as $unidad) {
            $responsableUnidad = $usuarios->where('unidad_organica_id', $unidad->id)->first()
                              ?? $usuarios->random();

            // Colaboradores disponibles de otras unidades
            $colaboradores = $usuarios->whereNotIn('id', [$responsableUnidad->id])->take(5)->values();

            // A) 4 actividades normales — 1 responsable principal
            $normales = Actividad::factory(4)->create([
                'unidad_organica_id' => $unidad->id,
                'creado_por'         => $responsableUnidad->id,
                'componente_id'      => $componentes->random()->id,
            ]);
            foreach ($normales as $act) {
                $act->responsables()->attach($responsableUnidad->id, ['tipo' => 'principal']);
            }

            // B) 2 actividades con múltiples responsables (principal + colaborador)
            $multiResp = Actividad::factory(2)->create([
                'unidad_organica_id' => $unidad->id,
                'creado_por'         => $responsableUnidad->id,
                'componente_id'      => $componentes->random()->id,
            ]);
            foreach ($multiResp as $act) {
                $act->responsables()->attach($responsableUnidad->id, ['tipo' => 'principal']);
                if ($colaboradores->isNotEmpty()) {
                    $act->responsables()->attach(
                        $colaboradores->random()->id,
                        ['tipo' => 'colaborador']
                    );
                }
            }

            // C) 1 actividad institucional — asignada a todos los responsables de unidad
            $institucional = Actividad::factory()->create([
                'unidad_organica_id' => $unidad->id,
                'creado_por'         => $responsableUnidad->id,
                'componente_id'      => $componentes->where('numero', 1)->first()?->id
                                        ?? $componentes->first()->id,
                'prioridad'          => 'alta',
            ]);
            $responsablesUnidades = $usuarios->whereIn(
                'unidad_organica_id',
                $unidades->pluck('id')->toArray()
            )->take(3)->values();
            foreach ($responsablesUnidades as $resp) {
                $tipo = $resp->id === $responsableUnidad->id ? 'principal' : 'supervisor';
                $institucional->responsables()->syncWithoutDetaching([$resp->id => ['tipo' => $tipo]]);
            }

            // D) 1 actividad completada — con historial de avance
            $completada = Actividad::factory()->completada()->create([
                'unidad_organica_id' => $unidad->id,
                'creado_por'         => $responsableUnidad->id,
                'componente_id'      => $componentes->random()->id,
            ]);
            $completada->responsables()->attach($responsableUnidad->id, ['tipo' => 'principal']);
            $this->generarHistorialAvance($completada, $responsableUnidad->id);

            // E) 1 actividad vencida
            $vencida = Actividad::factory()->vencida()->create([
                'unidad_organica_id' => $unidad->id,
                'creado_por'         => $responsableUnidad->id,
                'componente_id'      => $componentes->random()->id,
            ]);
            $vencida->responsables()->attach($responsableUnidad->id, ['tipo' => 'principal']);

            // F) 1 actividad en proceso — con supervisor de la dirección
            $enProceso = Actividad::factory()->enProceso()->create([
                'unidad_organica_id' => $unidad->id,
                'creado_por'         => $responsableUnidad->id,
                'componente_id'      => $componentes->random()->id,
            ]);
            $enProceso->responsables()->attach($responsableUnidad->id, ['tipo' => 'principal']);
            $director = $usuarios->where('unidad_organica_id',
                UnidadOrganica::where('codigo', 'DIR')->value('id')
            )->first();
            if ($director && $director->id !== $responsableUnidad->id) {
                $enProceso->responsables()->syncWithoutDetaching([
                    $director->id => ['tipo' => 'supervisor'],
                ]);
            }
        }

        // ─── 2. Evidencias ────────────────────────────────────────────────────
        Actividad::whereIn('estado', ['completada', 'en_proceso', 'observado'])
            ->get()
            ->each(function (Actividad $actividad) use ($usuarios) {
                $subidoPor = $actividad->responsables->first()?->id
                          ?? $usuarios->random()->id;

                Evidencia::factory(rand(1, 4))->create([
                    'actividad_id' => $actividad->id,
                    'subido_por'   => $subidoPor,
                ]);
            });

        // ─── 3. Alertas ───────────────────────────────────────────────────────
        $usuariosConUnidad = $usuarios->whereNotNull('unidad_organica_id')->values();
        $usuariosConUnidad->take(20)->each(function (User $usuario) use ($unidades) {
            $actividad = Actividad::where('unidad_organica_id', $usuario->unidad_organica_id)
                ->inRandomOrder()->first()
                ?? Actividad::inRandomOrder()->first();

            Alerta::factory(rand(2, 5))->create([
                'usuario_id'         => $usuario->id,
                'unidad_organica_id' => $usuario->unidad_organica_id ?? $unidades->random()->id,
                'actividad_id'       => $actividad?->id,
            ]);
        });

        // ─── 4. Reconocimientos: 6 meses de histórico ────────────────────────
        $periodos = [
            ['anio' => 2025, 'mes' => 10],
            ['anio' => 2025, 'mes' => 11],
            ['anio' => 2025, 'mes' => 12],
            ['anio' => 2026, 'mes' => 1],
            ['anio' => 2026, 'mes' => 2],
            ['anio' => 2026, 'mes' => 3],
            ['anio' => 2026, 'mes' => 4],
            ['anio' => 2026, 'mes' => 5],
        ];

        foreach ($periodos as $periodo) {
            $rankingUnidades = $unidades->map(function (UnidadOrganica $unidad) use ($periodo) {
                $total       = Actividad::where('unidad_organica_id', $unidad->id)->count();
                $completadas = Actividad::where('unidad_organica_id', $unidad->id)
                    ->where('estado', 'completada')->count();
                // Simular variación por período con un offset aleatorio fijo por unidad+mes
                $bonus   = ($unidad->id * 7 + $periodo['mes'] * 3) % 20;
                $puntaje = $total > 0
                    ? min(100, round(($completadas / $total) * 100) + $bonus)
                    : rand(30, 85);

                return [
                    'unidad'      => $unidad,
                    'puntaje'     => $puntaje,
                    'completadas' => $completadas,
                    'total'       => $total,
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
                        'posicion'                => $pos + 1,
                        'puntaje'                 => $item['puntaje'],
                        'avance_global'           => $item['puntaje'],
                        'actividades_total'        => $item['total'],
                        'actividades_completadas'  => $item['completadas'],
                        'medalla'                 => $medalla,
                    ]
                );
            }
        }

        $this->command->info('✓ Datos generados: actividades con responsables múltiples, evidencias, alertas y reconocimientos (8 meses).');
    }

    // ── Helper: historial de avance progresivo para actividades completadas ──

    private function generarHistorialAvance(Actividad $actividad, int $usuarioId): void
    {
        $hitos    = [25, 50, 75, 100];
        $anterior = 0;
        $base     = Carbon::now()->subDays(rand(45, 120));

        foreach ($hitos as $i => $avance) {
            ActividadHistorial::create([
                'actividad_id'   => $actividad->id,
                'usuario_id'     => $usuarioId,
                'campo'          => 'avance',
                'valor_anterior' => (string) $anterior,
                'valor_nuevo'    => (string) $avance,
                'descripcion'    => "Avance actualizado al {$avance}%.",
                'created_at'     => $base->copy()->addDays($i * 10),
                'updated_at'     => $base->copy()->addDays($i * 10),
            ]);
            $anterior = $avance;
        }

        ActividadHistorial::create([
            'actividad_id'   => $actividad->id,
            'usuario_id'     => $usuarioId,
            'campo'          => 'estado',
            'valor_anterior' => 'en_proceso',
            'valor_nuevo'    => 'completada',
            'descripcion'    => 'Actividad completada. Todas las evidencias validadas y aprobadas.',
            'created_at'     => $base->copy()->addDays(40),
            'updated_at'     => $base->copy()->addDays(40),
        ]);
    }
}
