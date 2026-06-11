<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IntegridadDatosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $anio = 2026;
        $now  = Carbon::now();

        // Limpiar actividades de integridad existentes de prueba (mantiene las reales)
        $existentes = DB::table('actividades')
            ->where('modulo', 'integridad')
            ->where('anio', $anio)
            ->pluck('id');

        DB::table('actividad_responsables')->whereIn('actividad_id', $existentes)->delete();
        DB::table('actividades')->whereIn('id', $existentes)->delete();

        // Datos: [pregunta_id, unidad_id, responsable_id, estado, avance, fecha_inicio, dias_limite, prioridad, nombre, sgd]
        $actividades = [
            // ── Comp 1: Diagnóstico Institucional (preguntas 1,2,3) ──────────────
            [1, 1, 2, 'completada', 100, -60, -10, 'alta',
                'Elaboración del diagnóstico de integridad institucional 2026',
                'SGD-2026-0041'],
            [2, 1, 2, 'completada', 100, -55,  -5, 'alta',
                'Taller de involucramiento de la alta dirección en el diagnóstico',
                'SGD-2026-0042'],
            [3, 4, 3, 'en_proceso',  65, -30, +20, 'media',
                'Documentación y sistematización de resultados del diagnóstico',
                'SGD-2026-0043'],

            // ── Comp 2: Mapeo de Actores (preguntas 4,5,6) ──────────────────────
            [4, 4, 4, 'completada', 100, -45,  -8, 'alta',
                'Identificación y registro de grupos de interés institucionales',
                'SGD-2026-0051'],
            [5, 4, 4, 'en_proceso',  50, -20, +15, 'media',
                'Elaboración del mapa de actores y partes interesadas',
                'SGD-2026-0052'],
            [6, 3, 5, 'pendiente',    0,  -5, +30, 'baja',
                'Socialización del mapa de actores con el personal',
                'SGD-2026-0053'],

            // ── Comp 3: Análisis de Riesgos (preguntas 7,8,9) ───────────────────
            [7, 5, 6, 'en_proceso',  40, -25, +10, 'alta',
                'Identificación de riesgos de corrupción por proceso institucional',
                'SGD-2026-0061'],
            [8, 5, 6, 'pendiente',    0,  -3, +25, 'media',
                'Priorización y valoración de riesgos de integridad identificados',
                'SGD-2026-0062'],
            [9, 5, 7, 'pendiente',    0, null, +40, 'baja',
                'Elaboración del registro actualizado de riesgos de integridad',
                null],

            // ── Comp 4: Política de Integridad (preguntas 10,11,12) ─────────────
            [10, 1, 2, 'observado',  30, -40, -15, 'alta',
                'Elaboración de la política de integridad institucional 2026',
                'SGD-2026-0071'],
            [11, 1, 2, 'pendiente',   0, -10, +20, 'alta',
                'Aprobación de la política de integridad por la alta dirección',
                'SGD-2026-0072'],
            [12, 3, 8, 'pendiente',   0, null, +35, 'media',
                'Publicación de la política de integridad en el portal de transparencia',
                null],

            // ── Comp 5: Capacitación en Integridad (preguntas 13,14,15) ─────────
            [13, 8, 9, 'completada', 100, -50, -12, 'alta',
                'Ejecución del plan de capacitación en ética e integridad pública',
                'SGD-2026-0081'],
            [14, 8, 9, 'en_proceso',  75, -35,  +5, 'alta',
                'Capacitación al 80% del personal en valores institucionales',
                'SGD-2026-0082'],
            [15, 8, 10, 'en_proceso', 60, -20, +15, 'media',
                'Evaluación de la efectividad de las capacitaciones realizadas',
                'SGD-2026-0083'],

            // ── Comp 6: Canal de Denuncias (preguntas 16,17,18) ─────────────────
            [16, 1, 2, 'completada', 100, -60, -20, 'alta',
                'Implementación y operativización del canal de denuncias institucional',
                'SGD-2026-0091'],
            [17, 5, 6, 'en_proceso',  45, -15, +20, 'alta',
                'Protocolo de confidencialidad y protección al denunciante',
                'SGD-2026-0092'],
            [18, 4, 4, 'vencida',     20, -40,  -5, 'alta',
                'Sistema de seguimiento y respuesta a denuncias recibidas',
                'SGD-2026-0093'],
        ];

        $responsables_extra = [
            // [actividad_index, user_id, tipo]
            [0,  3, 'colaborador'],
            [2,  5, 'colaborador'],
            [6,  7, 'colaborador'],
            [9,  3, 'supervisor'],
            [13, 11,'colaborador'],
            [16, 8, 'colaborador'],
        ];

        $insertedIds = [];

        foreach ($actividades as $i => $a) {
            [$pregunta_id, $unidad_id, $resp_id, $estado, $avance,
             $inicio_offset, $dias_limite, $prioridad, $nombre, $sgd] = $a;

            $fecha_inicio = $inicio_offset ? $now->copy()->addDays((int)$inicio_offset)->toDateString() : null;
            $fecha_limite = $now->copy()->addDays($dias_limite)->toDateString();

            $codigo = 'INTEGRIDAD-' . $anio . '-' . str_pad(count($insertedIds) + 1, 3, '0', STR_PAD_LEFT);

            $id = DB::table('actividades')->insertGetId([
                'modulo'                  => 'integridad',
                'integridad_pregunta_id'  => $pregunta_id,
                'unidad_organica_id'      => $unidad_id,
                'codigo'                  => $codigo,
                'nombre'                  => $nombre,
                'numero_sgd'              => $sgd,
                'anio'                    => $anio,
                'fecha_inicio'            => $fecha_inicio,
                'fecha_limite'            => $fecha_limite,
                'estado'                  => $estado,
                'avance'                  => $avance,
                'prioridad'               => $prioridad,
                'descripcion'             => null,
                'observaciones'           => $estado === 'observado' ? 'Requiere revisión y ajuste antes de la aprobación final.' : null,
                'created_at'              => $now,
                'updated_at'              => $now,
            ]);

            $insertedIds[$i] = $id;

            DB::table('actividad_responsables')->insert([
                'actividad_id' => $id,
                'user_id'      => $resp_id,
                'tipo'         => 'principal',
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }

        // Responsables adicionales
        foreach ($responsables_extra as [$idx, $user_id, $tipo]) {
            if (!isset($insertedIds[$idx])) continue;
            DB::table('actividad_responsables')->insert([
                'actividad_id' => $insertedIds[$idx],
                'user_id'      => $user_id,
                'tipo'         => $tipo,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }

        $this->command->info('✅ ' . count($actividades) . ' actividades de integridad insertadas.');
    }
}
