<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EstructuraSciIntegridadSeeder extends Seeder
{
    public function run(): void
    {
        $now  = Carbon::now();
        $anio = 2026;

        // ── ESTRUCTURA SCI ────────────────────────────────────────────────────
        $ejes = [
            [
                'nombre'      => 'Ambiente de Control',
                'descripcion' => 'Establece el tono de la organización e influye en la conciencia del control de su personal.',
                'orden'       => 1,
                'componentes' => [
                    ['nombre' => 'Filosofía de la Dirección', 'icono' => 'ti-bulb', 'orden' => 1, 'preguntas' => [
                        '¿La dirección demuestra compromiso con los valores éticos e integridad institucional?',
                        '¿Existe un código de ética aprobado y difundido a todo el personal?',
                        '¿Se realizan acciones disciplinarias ante incumplimientos éticos?',
                    ]],
                    ['nombre' => 'Integridad y Valores Éticos', 'icono' => 'ti-shield-check', 'orden' => 2, 'preguntas' => [
                        '¿El personal conoce y aplica los valores institucionales en su trabajo diario?',
                        '¿Existe un canal de comunicación para reportar comportamientos contrarios a la ética?',
                    ]],
                    ['nombre' => 'Administración Estratégica', 'icono' => 'ti-chart-arrows', 'orden' => 3, 'preguntas' => [
                        '¿La entidad cuenta con un Plan Estratégico Institucional (PEI) vigente?',
                        '¿Los objetivos estratégicos están alineados con la misión y visión institucional?',
                        '¿Se realiza seguimiento periódico al cumplimiento del PEI?',
                    ]],
                ],
            ],
            [
                'nombre'      => 'Evaluación de Riesgos',
                'descripcion' => 'Proceso para identificar y analizar los riesgos relevantes para el logro de los objetivos.',
                'orden'       => 2,
                'componentes' => [
                    ['nombre' => 'Planeamiento de la Gestión de Riesgos', 'icono' => 'ti-list-check', 'orden' => 1, 'preguntas' => [
                        '¿Existe un plan de gestión de riesgos aprobado para el ejercicio vigente?',
                        '¿El plan de gestión de riesgos incluye metodología, responsables y cronograma?',
                    ]],
                    ['nombre' => 'Identificación de Riesgos', 'icono' => 'ti-alert-triangle', 'orden' => 2, 'preguntas' => [
                        '¿Se han identificado los riesgos que pueden afectar el logro de los objetivos?',
                        '¿Existe una Matriz de Riesgos institucional actualizada?',
                        '¿Se involucra al personal en la identificación de riesgos de sus procesos?',
                    ]],
                    ['nombre' => 'Valoración de Riesgos', 'icono' => 'ti-scale', 'orden' => 3, 'preguntas' => [
                        '¿Los riesgos identificados han sido valorados según probabilidad e impacto?',
                        '¿Se han priorizado los riesgos críticos y altos para su tratamiento?',
                    ]],
                ],
            ],
            [
                'nombre'      => 'Actividades de Control',
                'descripcion' => 'Políticas y procedimientos que ayudan a asegurar que se llevan a cabo las directrices de la dirección.',
                'orden'       => 3,
                'componentes' => [
                    ['nombre' => 'Procedimientos de Autorización y Aprobación', 'icono' => 'ti-checkmark', 'orden' => 1, 'preguntas' => [
                        '¿Están definidos los niveles de autorización para los principales procesos?',
                        '¿Las aprobaciones se realizan por personal con competencia y autoridad asignada?',
                    ]],
                    ['nombre' => 'Evaluación Costo-Beneficio', 'icono' => 'ti-coin', 'orden' => 2, 'preguntas' => [
                        '¿Se evalúa el costo-beneficio de los controles antes de implementarlos?',
                        '¿Los controles implementados son proporcionales a los riesgos identificados?',
                    ]],
                ],
            ],
            [
                'nombre'      => 'Información y Comunicación',
                'descripcion' => 'La información pertinente debe ser identificada, capturada y comunicada de manera oportuna.',
                'orden'       => 4,
                'componentes' => [
                    ['nombre' => 'Información y Responsabilidad', 'icono' => 'ti-info-circle', 'orden' => 1, 'preguntas' => [
                        '¿Existen sistemas de información que apoyen el logro de los objetivos institucionales?',
                        '¿La información generada es confiable, oportuna y relevante para la toma de decisiones?',
                    ]],
                    ['nombre' => 'Canales de Comunicación', 'icono' => 'ti-message', 'orden' => 2, 'preguntas' => [
                        '¿Existen canales formales de comunicación interna entre las áreas?',
                        '¿Se comunica al personal sobre sus responsabilidades de control interno?',
                        '¿Se cuenta con mecanismos de comunicación externa con ciudadanos y partes interesadas?',
                    ]],
                ],
            ],
            [
                'nombre'      => 'Supervisión',
                'descripcion' => 'Proceso que evalúa la calidad del control interno a lo largo del tiempo.',
                'orden'       => 5,
                'componentes' => [
                    ['nombre' => 'Actividades de Prevención y Monitoreo', 'icono' => 'ti-eye', 'orden' => 1, 'preguntas' => [
                        '¿Se realizan evaluaciones periódicas del sistema de control interno?',
                        '¿Se identifican y corrigen oportunamente las deficiencias de control?',
                    ]],
                    ['nombre' => 'Seguimiento de Resultados', 'icono' => 'ti-trending-up', 'orden' => 2, 'preguntas' => [
                        '¿Se da seguimiento a las recomendaciones de auditoría interna y externa?',
                        '¿Se reporta a la alta dirección sobre el avance del SCI?',
                        '¿El Comité de Control Interno sesiona con la periodicidad establecida?',
                    ]],
                ],
            ],
        ];

        foreach ($ejes as $ejeData) {
            $ejeId = DB::table('sci_ejes')->insertGetId([
                'nombre'      => $ejeData['nombre'],
                'descripcion' => $ejeData['descripcion'],
                'anio'        => $anio,
                'orden'       => $ejeData['orden'],
                'activo'      => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);

            foreach ($ejeData['componentes'] as $compData) {
                $compId = DB::table('sci_componentes')->insertGetId([
                    'eje_id'      => $ejeId,
                    'nombre'      => $compData['nombre'],
                    'icono'       => $compData['icono'],
                    'descripcion' => null,
                    'orden'       => $compData['orden'],
                    'activo'      => true,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);

                foreach ($compData['preguntas'] as $orden => $pregunta) {
                    DB::table('sci_preguntas')->insert([
                        'componente_id' => $compId,
                        'nombre'        => $pregunta,
                        'orden'         => $orden + 1,
                        'activo'        => true,
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ]);
                }
            }
        }

        // ── ESTRUCTURA INTEGRIDAD ─────────────────────────────────────────────
        $etapas = [
            [
                'nombre'      => 'Diagnóstico y Planificación',
                'descripcion' => 'Fase inicial de análisis institucional y planificación del Modelo de Integridad.',
                'orden'       => 1,
                'componentes' => [
                    ['nombre' => 'Diagnóstico Institucional', 'icono' => 'ti-search', 'orden' => 1, 'preguntas' => [
                        '¿Se ha elaborado el diagnóstico de la situación de integridad institucional?',
                        '¿El diagnóstico identifica brechas y oportunidades de mejora en integridad?',
                        '¿Se han sistematizado los resultados del diagnóstico?',
                    ]],
                    ['nombre' => 'Mapeo de Actores', 'icono' => 'ti-users', 'orden' => 2, 'preguntas' => [
                        '¿Se han identificado los grupos de interés internos y externos?',
                        '¿Existe un mapa de actores actualizado con niveles de influencia e interés?',
                        '¿Se socializa el mapa de actores con el personal involucrado?',
                    ]],
                    ['nombre' => 'Análisis de Riesgos de Corrupción', 'icono' => 'ti-alert-circle', 'orden' => 3, 'preguntas' => [
                        '¿Se han identificado riesgos de corrupción en los procesos institucionales?',
                        '¿Los riesgos de corrupción han sido priorizados y valorados?',
                        '¿Existe un registro actualizado de riesgos de integridad?',
                    ]],
                ],
            ],
            [
                'nombre'      => 'Implementación',
                'descripcion' => 'Ejecución de las medidas de integridad y acciones del Modelo.',
                'orden'       => 2,
                'componentes' => [
                    ['nombre' => 'Política de Integridad', 'icono' => 'ti-file-certificate', 'orden' => 1, 'preguntas' => [
                        '¿Se ha elaborado y aprobado la Política de Integridad institucional?',
                        '¿La Política de Integridad ha sido aprobada por la alta dirección mediante resolución?',
                        '¿La Política de Integridad es pública y está publicada en el portal web?',
                    ]],
                    ['nombre' => 'Capacitación en Integridad', 'icono' => 'ti-school', 'orden' => 2, 'preguntas' => [
                        '¿Se ejecuta el plan de capacitación en ética e integridad pública?',
                        '¿El 80% del personal ha recibido capacitación en valores institucionales?',
                        '¿Se evalúa la efectividad de las capacitaciones realizadas?',
                    ]],
                    ['nombre' => 'Canal de Denuncias', 'icono' => 'ti-bell', 'orden' => 3, 'preguntas' => [
                        '¿Está habilitado y operativo el canal de denuncias institucional?',
                        '¿Existe protocolo de confidencialidad y protección al denunciante?',
                        '¿Se da seguimiento y respuesta oportuna a las denuncias recibidas?',
                    ]],
                ],
            ],
            [
                'nombre'      => 'Monitoreo y Mejora',
                'descripcion' => 'Seguimiento, evaluación y mejora continua del Modelo de Integridad.',
                'orden'       => 3,
                'componentes' => [
                    ['nombre' => 'Seguimiento del Modelo', 'icono' => 'ti-chart-bar', 'orden' => 1, 'preguntas' => [
                        '¿Se realiza seguimiento periódico al avance del Modelo de Integridad?',
                        '¿Se generan reportes de avance del Modelo para la alta dirección?',
                    ]],
                    ['nombre' => 'Mejora Continua', 'icono' => 'ti-refresh', 'orden' => 2, 'preguntas' => [
                        '¿Se identifican lecciones aprendidas y buenas prácticas de integridad?',
                        '¿Se actualizan las medidas de integridad en base a los resultados del monitoreo?',
                        '¿Se comparten las buenas prácticas con otras entidades del sector?',
                    ]],
                ],
            ],
        ];

        foreach ($etapas as $etapaData) {
            $etapaId = DB::table('integridad_etapas')->insertGetId([
                'nombre'      => $etapaData['nombre'],
                'descripcion' => $etapaData['descripcion'],
                'anio'        => $anio,
                'orden'       => $etapaData['orden'],
                'activo'      => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);

            foreach ($etapaData['componentes'] as $compData) {
                $compId = DB::table('integridad_componentes')->insertGetId([
                    'etapa_id'    => $etapaId,
                    'nombre'      => $compData['nombre'],
                    'icono'       => $compData['icono'],
                    'descripcion' => null,
                    'orden'       => $compData['orden'],
                    'activo'      => true,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);

                foreach ($compData['preguntas'] as $orden => $pregunta) {
                    DB::table('integridad_preguntas')->insert([
                        'componente_id' => $compId,
                        'nombre'        => $pregunta,
                        'orden'         => $orden + 1,
                        'activo'        => true,
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ]);
                }
            }
        }

        $sciTotal        = DB::table('sci_preguntas')->count();
        $integridadTotal = DB::table('integridad_preguntas')->count();
        $this->command->info("✓ Estructura SCI: {$sciTotal} preguntas. Integridad: {$integridadTotal} preguntas.");
    }
}
