<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\ActividadHistorial;
use App\Models\Alerta;
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
        $unidades = UnidadOrganica::all();
        $usuarios = User::where('estado', 'activo')->get();

        if ($unidades->isEmpty() || $usuarios->isEmpty()) {
            $this->command->warn('Faltan datos base. Verifica UnidadesOrganicasSeeder y UsuariosSeeder.');
            return;
        }

        $dirId = UnidadOrganica::where('codigo', 'DIR')->value('id');

        // ── 1. Actividades por unidad con escenarios variados ─────────────────
        foreach ($unidades as $unidad) {
            $principal = $usuarios->where('unidad_organica_id', $unidad->id)->first()
                      ?? $usuarios->random();

            $colaboradores = $usuarios
                ->where('unidad_organica_id', '!=', $unidad->id)
                ->where('estado', 'activo')
                ->take(4)
                ->values();

            $director = $usuarios->where('unidad_organica_id', $dirId)->first();

            // A) 4 actividades con 1 responsable principal
            Actividad::factory(4)->create([
                'unidad_organica_id' => $unidad->id,
                'creado_por'         => $principal->id,
            ])->each(fn($a) => $a->responsables()->attach($principal->id, ['tipo' => 'principal']));

            // B) 2 actividades con principal + colaborador de otra unidad
            Actividad::factory(2)->create([
                'unidad_organica_id' => $unidad->id,
                'creado_por'         => $principal->id,
            ])->each(function ($a) use ($principal, $colaboradores) {
                $a->responsables()->attach($principal->id, ['tipo' => 'principal']);
                if ($colaboradores->isNotEmpty()) {
                    $a->responsables()->attach($colaboradores->random()->id, ['tipo' => 'colaborador']);
                }
            });

            // C) 1 actividad institucional con supervisor de la Dirección
            $institucional = Actividad::factory()->create([
                'unidad_organica_id' => $unidad->id,
                'creado_por'         => $principal->id,
                'prioridad'          => 'alta',
                'nombre'             => 'Actividad institucional — seguimiento Dirección',
            ]);
            $institucional->responsables()->attach($principal->id, ['tipo' => 'principal']);
            if ($director && $director->id !== $principal->id) {
                $institucional->responsables()->syncWithoutDetaching([
                    $director->id => ['tipo' => 'supervisor'],
                ]);
            }

            // D) 1 actividad completada con historial completo de avance
            $completada = Actividad::factory()->completada()->create([
                'unidad_organica_id' => $unidad->id,
                'creado_por'         => $principal->id,
            ]);
            $completada->responsables()->attach($principal->id, ['tipo' => 'principal']);
            $this->historialAvance($completada, $principal->id);

            // E) 1 actividad vencida (fecha límite pasada, avance bajo)
            $vencida = Actividad::factory()->vencida()->create([
                'unidad_organica_id' => $unidad->id,
                'creado_por'         => $principal->id,
            ]);
            $vencida->responsables()->attach($principal->id, ['tipo' => 'principal']);

            // F) 1 actividad en proceso con supervisor de Dirección
            $enProceso = Actividad::factory()->enProceso()->create([
                'unidad_organica_id' => $unidad->id,
                'creado_por'         => $principal->id,
            ]);
            $enProceso->responsables()->attach($principal->id, ['tipo' => 'principal']);
            if ($director && $director->id !== $principal->id) {
                $enProceso->responsables()->syncWithoutDetaching([
                    $director->id => ['tipo' => 'supervisor'],
                ]);
            }

            // G) 1 actividad observada (requiere corrección)
            $observada = Actividad::factory()->create([
                'unidad_organica_id' => $unidad->id,
                'creado_por'         => $principal->id,
                'estado'             => 'observado',
                'avance'             => rand(20, 60),
                'observaciones'      => 'Falta evidencia documental. El responsable debe subsanar antes del cierre.',
            ]);
            $observada->responsables()->attach($principal->id, ['tipo' => 'principal']);
            if ($colaboradores->isNotEmpty()) {
                $observada->responsables()->syncWithoutDetaching([
                    $colaboradores->first()->id => ['tipo' => 'colaborador'],
                ]);
            }
        }

        // ── 2. Evidencias para actividades con avance ─────────────────────────
        Actividad::whereIn('estado', ['completada', 'en_proceso', 'observado'])
            ->get()
            ->each(function (Actividad $act) use ($usuarios) {
                $subidoPor = $act->responsables()->first()?->id
                          ?? $usuarios->random()->id;
                Evidencia::factory(rand(1, 3))->create([
                    'actividad_id' => $act->id,
                    'subido_por'   => $subidoPor,
                ]);
            });

        // ── 3. Alertas variadas (respeta unique constraint) ───────────────────
        $tipos           = ['vencimiento', 'avance_bajo', 'evidencia_falta', 'sistema'];
        $usuariosActivos = $usuarios->whereNotNull('unidad_organica_id')->values();

        $usuariosActivos->take(18)->each(function (User $usuario) use ($tipos) {
            $actividad = Actividad::where('unidad_organica_id', $usuario->unidad_organica_id)
                ->inRandomOrder()->first()
                ?? Actividad::inRandomOrder()->first();

            if (!$actividad) return;

            Alerta::firstOrCreate(
                ['actividad_id' => $actividad->id, 'tipo' => collect($tipos)->random(), 'leida' => false],
                [
                    'usuario_id'         => $usuario->id,
                    'unidad_organica_id' => $usuario->unidad_organica_id,
                    'titulo'             => "Alerta automática: {$actividad->nombre}",
                    'mensaje'            => 'Requiere atención. Revisa el estado de esta actividad.',
                    'prioridad'          => collect(['alta', 'media', 'baja'])->random(),
                ]
            );
        });

        // ── 4. Reconocimientos históricos (10 meses) ──────────────────────────
        $periodos = [
            ['anio' => 2025, 'mes' => 8],
            ['anio' => 2025, 'mes' => 9],
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
            $ranking = $unidades->map(function (UnidadOrganica $u) use ($periodo) {
                $total       = Actividad::where('unidad_organica_id', $u->id)->count();
                $completadas = Actividad::where('unidad_organica_id', $u->id)->where('estado', 'completada')->count();
                $bonus       = ($u->id * 7 + $periodo['mes'] * 3) % 20;
                $puntaje     = $total > 0
                    ? min(100, round(($completadas / $total) * 100) + $bonus)
                    : rand(30, 85);
                return ['unidad' => $u, 'puntaje' => $puntaje, 'completadas' => $completadas, 'total' => $total];
            })->sortByDesc('puntaje')->values();

            foreach ($ranking as $pos => $item) {
                Reconocimiento::updateOrCreate(
                    ['unidad_organica_id' => $item['unidad']->id, 'anio' => $periodo['anio'], 'mes' => $periodo['mes']],
                    [
                        'posicion'               => $pos + 1,
                        'puntaje'                => $item['puntaje'],
                        'avance_global'          => $item['puntaje'],
                        'actividades_total'      => $item['total'],
                        'actividades_completadas'=> $item['completadas'],
                        'medalla'                => match(true) {
                            $pos === 0 => 'oro',
                            $pos === 1 => 'plata',
                            $pos === 2 => 'bronce',
                            default    => null,
                        },
                    ]
                );
            }
        }

        $this->command->info('✓ DatosSeeder: actividades (7 tipos por unidad), evidencias, alertas y 10 meses de reconocimientos generados.');
    }

    // ── Historial de avance progresivo 0→25→50→75→100 ────────────────────────
    private function historialAvance(Actividad $actividad, int $usuarioId): void
    {
        $hitos    = [25, 50, 75, 100];
        $anterior = 0;
        $base     = Carbon::now()->subDays(rand(60, 120));

        foreach ($hitos as $i => $avance) {
            ActividadHistorial::create([
                'actividad_id'   => $actividad->id,
                'usuario_id'     => $usuarioId,
                'campo'          => 'avance',
                'valor_anterior' => (string) $anterior,
                'valor_nuevo'    => (string) $avance,
                'descripcion'    => "Avance actualizado al {$avance}%.",
                'created_at'     => $base->copy()->addDays($i * 12),
                'updated_at'     => $base->copy()->addDays($i * 12),
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
            'created_at'     => $base->copy()->addDays(48),
            'updated_at'     => $base->copy()->addDays(48),
        ]);
    }
}
