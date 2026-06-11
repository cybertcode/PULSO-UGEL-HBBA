<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\Alerta;
use App\Models\IntegridadComponente;
use App\Models\IntegridadEtapa;
use App\Models\IntegridadPregunta;
use App\Models\SciComponente;
use App\Models\SciEje;
use App\Models\SciPregunta;
use App\Models\UnidadOrganica;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefactorizacionSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        SciPregunta::truncate();
        SciComponente::truncate();
        SciEje::truncate();
        IntegridadPregunta::truncate();
        IntegridadComponente::truncate();
        IntegridadEtapa::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $anio = 2026;

        // ── SCI ───────────────────────────────────────────────────────────────
        $sciFichaBase = 'https://www.gob.pe/institucion/pcm/normas-legales/ficha/';

        $ejes = [
            [
                'nombre'      => 'Eje 1: Ambiente de Control',
                'descripcion' => 'Compromisos éticos y estructura organizacional del SCI',
                'componentes' => [
                    [
                        'nombre' => 'Filosofía de la Dirección',
                        'icono'  => 'tabler-bulb',
                        'preguntas' => [
                            ['nombre' => '¿La alta dirección demuestra compromiso con el SCI?', 'link_ficha' => $sciFichaBase . '1'],
                            ['nombre' => '¿Existe código de ética aprobado?',                   'link_ficha' => $sciFichaBase . '2'],
                            ['nombre' => '¿Se socializa el código de ética al personal?',        'link_ficha' => $sciFichaBase . '3'],
                        ],
                    ],
                    [
                        'nombre' => 'Integridad y Valores Éticos',
                        'icono'  => 'tabler-heart-handshake',
                        'preguntas' => [
                            ['nombre' => '¿Existen mecanismos de denuncia de irregularidades?', 'link_ficha' => $sciFichaBase . '4'],
                            ['nombre' => '¿Se promueven valores institucionales?',               'link_ficha' => $sciFichaBase . '5'],
                            ['nombre' => '¿Se sanciona el incumplimiento ético?',                'link_ficha' => $sciFichaBase . '6'],
                        ],
                    ],
                    [
                        'nombre' => 'Administración de RRHH',
                        'icono'  => 'tabler-users',
                        'preguntas' => [
                            ['nombre' => '¿Existe un manual de perfiles de puestos?',             'link_ficha' => $sciFichaBase . '7'],
                            ['nombre' => '¿Se realizan evaluaciones de desempeño periódicas?',    'link_ficha' => $sciFichaBase . '8'],
                            ['nombre' => '¿Existe plan de capacitación anual ejecutado?',         'link_ficha' => $sciFichaBase . '9'],
                        ],
                    ],
                ],
            ],
            [
                'nombre'      => 'Eje 2: Evaluación de Riesgos',
                'descripcion' => 'Identificación y valoración de riesgos institucionales',
                'componentes' => [
                    [
                        'nombre' => 'Planeamiento de la Gestión de Riesgos',
                        'icono'  => 'tabler-chart-arrows',
                        'preguntas' => [
                            ['nombre' => '¿Existe un plan de gestión de riesgos aprobado?',      'link_ficha' => $sciFichaBase . '10'],
                            ['nombre' => '¿Se identifican riesgos por procesos?',                 'link_ficha' => $sciFichaBase . '11'],
                            ['nombre' => '¿Se valoran los riesgos identificados?',                'link_ficha' => $sciFichaBase . '12'],
                        ],
                    ],
                    [
                        'nombre' => 'Respuesta al Riesgo',
                        'icono'  => 'tabler-shield-bolt',
                        'preguntas' => [
                            ['nombre' => '¿Se implementan controles para cada riesgo crítico?', 'link_ficha' => $sciFichaBase . '13'],
                            ['nombre' => '¿Se realiza seguimiento a los controles implementados?', 'link_ficha' => $sciFichaBase . '14'],
                            ['nombre' => '¿Se actualiza la matriz de riesgos al menos una vez al año?', 'link_ficha' => $sciFichaBase . '15'],
                        ],
                    ],
                    [
                        'nombre' => 'Actividades de Control Gerencial',
                        'icono'  => 'tabler-adjustments',
                        'preguntas' => [
                            ['nombre' => '¿Existen procedimientos documentados por proceso?',    'link_ficha' => $sciFichaBase . '16'],
                            ['nombre' => '¿Se realizan arqueos y conciliaciones periódicas?',    'link_ficha' => $sciFichaBase . '17'],
                            ['nombre' => '¿Los sistemas de información apoyan el control?',      'link_ficha' => $sciFichaBase . '18'],
                        ],
                    ],
                ],
            ],
        ];

        $sciEjes = [];
        foreach ($ejes as $orden => $ejeData) {
            $eje = SciEje::create([
                'nombre'      => $ejeData['nombre'],
                'descripcion' => $ejeData['descripcion'],
                'anio'        => $anio,
                'orden'       => $orden + 1,
                'activo'      => true,
            ]);
            foreach ($ejeData['componentes'] as $co => $compData) {
                $comp = SciComponente::create([
                    'eje_id'      => $eje->id,
                    'nombre'      => $compData['nombre'],
                    'icono'       => $compData['icono'],
                    'descripcion' => '',
                    'orden'       => $co + 1,
                    'activo'      => true,
                ]);
                foreach ($compData['preguntas'] as $po => $pregData) {
                    SciPregunta::create([
                        'componente_id' => $comp->id,
                        'nombre'        => $pregData['nombre'],
                        'link_ficha'    => $pregData['link_ficha'],
                        'orden'         => $po + 1,
                        'activo'        => true,
                    ]);
                }
            }
            $sciEjes[] = $eje;
        }

        // ── INTEGRIDAD ────────────────────────────────────────────────────────
        $intFichaBase = 'https://www.gob.pe/institucion/pcm/normas-legales/integridad/ficha/';

        $etapas = [
            [
                'nombre'      => 'Etapa 1: Diagnóstico',
                'descripcion' => 'Evaluación inicial del estado de integridad institucional',
                'componentes' => [
                    [
                        'nombre' => 'Diagnóstico Institucional',
                        'icono'  => 'tabler-microscope',
                        'preguntas' => [
                            ['nombre' => '¿Se realizó el diagnóstico de integridad institucional?',   'link_ficha' => $intFichaBase . '1'],
                            ['nombre' => '¿Participó la alta dirección en el diagnóstico?',           'link_ficha' => $intFichaBase . '2'],
                            ['nombre' => '¿Se documentaron los resultados del diagnóstico?',          'link_ficha' => $intFichaBase . '3'],
                        ],
                    ],
                    [
                        'nombre' => 'Mapeo de Actores',
                        'icono'  => 'tabler-network',
                        'preguntas' => [
                            ['nombre' => '¿Se identificaron los grupos de interés de la entidad?',    'link_ficha' => $intFichaBase . '4'],
                            ['nombre' => '¿Se documentó el mapa de actores institucionales?',         'link_ficha' => $intFichaBase . '5'],
                            ['nombre' => '¿Se socializó el mapa de actores?',                         'link_ficha' => $intFichaBase . '6'],
                        ],
                    ],
                    [
                        'nombre' => 'Análisis de Riesgos de Integridad',
                        'icono'  => 'tabler-radar',
                        'preguntas' => [
                            ['nombre' => '¿Se identificaron riesgos de corrupción por proceso?',      'link_ficha' => $intFichaBase . '7'],
                            ['nombre' => '¿Se priorizaron los riesgos de integridad?',                'link_ficha' => $intFichaBase . '8'],
                            ['nombre' => '¿Existe un registro actualizado de riesgos de integridad?', 'link_ficha' => $intFichaBase . '9'],
                        ],
                    ],
                ],
            ],
            [
                'nombre'      => 'Etapa 2: Implementación',
                'descripcion' => 'Puesta en marcha de medidas de integridad',
                'componentes' => [
                    [
                        'nombre' => 'Política de Integridad',
                        'icono'  => 'tabler-certificate',
                        'preguntas' => [
                            ['nombre' => '¿Existe una política de integridad aprobada?',              'link_ficha' => $intFichaBase . '10'],
                            ['nombre' => '¿Fue aprobada por la alta dirección?',                      'link_ficha' => $intFichaBase . '11'],
                            ['nombre' => '¿Se publicó la política en el portal de transparencia?',    'link_ficha' => $intFichaBase . '12'],
                        ],
                    ],
                    [
                        'nombre' => 'Capacitación en Integridad',
                        'icono'  => 'tabler-school',
                        'preguntas' => [
                            ['nombre' => '¿Se ejecutó el plan de capacitación en integridad?',        'link_ficha' => $intFichaBase . '13'],
                            ['nombre' => '¿Se capacitó a más del 80% del personal?',                  'link_ficha' => $intFichaBase . '14'],
                            ['nombre' => '¿Se evaluó la efectividad de las capacitaciones?',          'link_ficha' => $intFichaBase . '15'],
                        ],
                    ],
                    [
                        'nombre' => 'Canal de Denuncias',
                        'icono'  => 'tabler-speakerphone',
                        'preguntas' => [
                            ['nombre' => '¿Existe un canal de denuncias operativo?',                  'link_ficha' => $intFichaBase . '16'],
                            ['nombre' => '¿Se garantiza la confidencialidad del denunciante?',        'link_ficha' => $intFichaBase . '17'],
                            ['nombre' => '¿Se da seguimiento a las denuncias recibidas?',             'link_ficha' => $intFichaBase . '18'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($etapas as $orden => $etapaData) {
            $etapa = IntegridadEtapa::create([
                'nombre'      => $etapaData['nombre'],
                'descripcion' => $etapaData['descripcion'],
                'anio'        => $anio,
                'orden'       => $orden + 1,
                'activo'      => true,
            ]);
            foreach ($etapaData['componentes'] as $co => $compData) {
                $comp = IntegridadComponente::create([
                    'etapa_id'    => $etapa->id,
                    'nombre'      => $compData['nombre'],
                    'icono'       => $compData['icono'],
                    'descripcion' => '',
                    'orden'       => $co + 1,
                    'activo'      => true,
                ]);
                foreach ($compData['preguntas'] as $po => $pregData) {
                    IntegridadPregunta::create([
                        'componente_id' => $comp->id,
                        'nombre'        => $pregData['nombre'],
                        'link_ficha'    => $pregData['link_ficha'],
                        'orden'         => $po + 1,
                        'activo'        => true,
                    ]);
                }
            }
        }

        // ── Actividades de prueba SCI ─────────────────────────────────────────
        $sciPreguntas = SciPregunta::inRandomOrder()->take(10)->get();
        $unidades     = UnidadOrganica::where('activo', true)->pluck('id');
        $usuarios     = User::where('estado', 'activo')->pluck('id');

        $estadosSci   = ['pendiente','en_proceso','en_proceso','completada','completada','pendiente','vencida','observado','en_proceso','completada'];
        $prioridades  = ['alta','media','baja','media','alta','baja','alta','media','baja','media'];

        $sciOffset = Actividad::where('codigo', 'like', 'SCI-' . $anio . '-%')->withTrashed()->count();

        foreach ($sciPreguntas as $idx => $pregunta) {
            $estado = $estadosSci[$idx] ?? 'pendiente';
            $act = Actividad::create([
                'codigo'           => 'SCI-' . $anio . '-' . str_pad($sciOffset + $idx + 1, 3, '0', STR_PAD_LEFT),
                'modulo'           => 'sci',
                'anio'             => $anio,
                'sci_pregunta_id'  => $pregunta->id,
                'nombre'           => 'Actividad de prueba SCI #' . ($idx + 1) . ': ' . mb_substr($pregunta->nombre, 0, 60),
                'descripcion'      => 'Actividad generada por seeder para pruebas del sistema.',
                'unidad_organica_id' => $unidades->random(),
                'creado_por'       => $usuarios->first(),
                'fecha_inicio'     => now()->subDays(rand(5, 30))->format('Y-m-d'),
                'fecha_limite'     => now()->addDays(rand(-5, 30))->format('Y-m-d'),
                'avance'           => match($estado) { 'completada' => 100, 'en_proceso' => rand(20, 80), 'vencida' => rand(0, 40), default => 0 },
                'estado'           => $estado,
                'prioridad'        => $prioridades[$idx],
            ]);
            if ($usuarios->isNotEmpty()) {
                $act->responsables()->attach($usuarios->random(), ['tipo' => 'principal']);
            }
        }

        // ── Actividades de prueba INTEGRIDAD ──────────────────────────────────
        $intOffset    = Actividad::where('codigo', 'like', 'INTEGRIDAD-' . $anio . '-%')->withTrashed()->count();
        $intPreguntas = IntegridadPregunta::inRandomOrder()->take(5)->get();
        $estadosInt   = ['pendiente','en_proceso','completada','observado','pendiente'];

        foreach ($intPreguntas as $idx => $pregunta) {
            $estado = $estadosInt[$idx] ?? 'pendiente';
            $act = Actividad::create([
                'codigo'                  => 'INTEGRIDAD-' . $anio . '-' . str_pad($intOffset + $idx + 1, 3, '0', STR_PAD_LEFT),
                'modulo'                  => 'integridad',
                'anio'                    => $anio,
                'integridad_pregunta_id'  => $pregunta->id,
                'nombre'                  => 'Actividad Integridad #' . ($idx + 1) . ': ' . mb_substr($pregunta->nombre, 0, 60),
                'descripcion'             => 'Actividad generada por seeder para pruebas del Modelo de Integridad.',
                'unidad_organica_id'      => $unidades->isNotEmpty() ? $unidades->random() : null,
                'creado_por'              => $usuarios->first(),
                'fecha_inicio'            => now()->subDays(rand(3, 20))->format('Y-m-d'),
                'fecha_limite'            => now()->addDays(rand(-3, 20))->format('Y-m-d'),
                'avance'                  => match($estado) { 'completada' => 100, 'en_proceso' => rand(30, 70), default => 0 },
                'estado'                  => $estado,
                'prioridad'               => ['alta','media','baja'][rand(0, 2)],
            ]);
            if ($usuarios->isNotEmpty()) {
                $act->responsables()->attach($usuarios->random(), ['tipo' => 'principal']);
            }
        }

        // ── Alertas de muestra ────────────────────────────────────────────────
        $actividadesSci = Actividad::where('modulo', 'sci')->whereIn('estado', ['pendiente', 'vencida'])->take(3)->get();
        foreach ($actividadesSci as $act) {
            Alerta::create([
                'actividad_id'      => $act->id,
                'modulo'            => 'sci',
                'titulo'            => 'Alerta de prueba SCI: ' . mb_substr($act->nombre, 0, 50),
                'mensaje'           => 'Esta es una alerta de prueba generada por el seeder.',
                'tipo'              => $act->estado === 'vencida' ? 'vencimiento' : 'vencimiento_proximo',
                'prioridad'         => 'media',
                'dias_anticipacion' => $act->estado === 'vencida' ? null : 5,
                'leida'             => false,
            ]);
        }

        $this->command->info('✓ RefactorizacionSeeder completado: ejes SCI, etapas Integridad, actividades y alertas de prueba.');
    }
}
