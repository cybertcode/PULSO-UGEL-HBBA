<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IntegridadEvidenciasSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Limpiar evidencias previas de integridad
        $ids = DB::table('actividades')->where('modulo', 'integridad')->pluck('id');
        DB::table('evidencias')->whereIn('actividad_id', $ids)->delete();

        // Obtener actividades por nombre clave para mapear ids
        $acts = DB::table('actividades')
            ->where('modulo', 'integridad')
            ->get(['id', 'nombre', 'estado', 'unidad_organica_id'])
            ->keyBy('nombre');

        $find = fn(string $substr) => collect($acts)->first(
            fn($a) => str_contains($a->nombre, $substr)
        );

        // [actividad_substr, subido_por, titulo, sgd, estado_ev, dias_atras, url]
        $evidencias = [
            // Diagnóstico Institucional — completada
            ['diagnóstico de integridad institucional', 2,
                'Informe de Diagnóstico de Integridad Institucional 2026',
                'SGD-2026-0041-EV1', 'validado', 55,
                'https://storage.ugel.gob.pe/integridad/diagnostico-2026.pdf'],
            ['diagnóstico de integridad institucional', 2,
                'Acta de aprobación del diagnóstico por Dirección',
                'SGD-2026-0041-EV2', 'validado', 50,
                'https://storage.ugel.gob.pe/integridad/acta-diagnostico.pdf'],

            // Taller alta dirección — completada
            ['involucramiento de la alta dirección', 2,
                'Lista de asistencia al taller de alta dirección',
                'SGD-2026-0042-EV1', 'validado', 48,
                'https://storage.ugel.gob.pe/integridad/asistencia-taller-dir.pdf'],
            ['involucramiento de la alta dirección', 2,
                'Fotografías y memorias del taller',
                'SGD-2026-0042-EV2', 'validado', 47,
                'https://storage.ugel.gob.pe/integridad/fotos-taller.pdf'],

            // Documentación resultados — en proceso
            ['sistematización de resultados', 3,
                'Borrador del informe de sistematización (versión 1)',
                'SGD-2026-0043-EV1', 'pendiente', 10,
                'https://storage.ugel.gob.pe/integridad/borrador-sistematizacion.pdf'],

            // Grupos de interés — completada
            ['Identificación y registro de grupos', 4,
                'Registro de grupos de interés institucionales validado',
                'SGD-2026-0051-EV1', 'validado', 40,
                'https://storage.ugel.gob.pe/integridad/registro-grupos-interes.pdf'],
            ['Identificación y registro de grupos', 4,
                'Matriz de identificación de partes interesadas',
                'SGD-2026-0051-EV2', 'validado', 38,
                'https://storage.ugel.gob.pe/integridad/matriz-partes.pdf'],

            // Mapa de actores — en proceso
            ['mapa de actores y partes interesadas', 4,
                'Avance del mapa de actores (borrador preliminar)',
                'SGD-2026-0052-EV1', 'pendiente', 8,
                'https://storage.ugel.gob.pe/integridad/mapa-actores-v1.pdf'],

            // Riesgos de corrupción — en proceso
            ['riesgos de corrupción por proceso', 6,
                'Matriz de identificación de riesgos por proceso',
                'SGD-2026-0061-EV1', 'pendiente', 12,
                'https://storage.ugel.gob.pe/integridad/matriz-riesgos.pdf'],

            // Política de integridad — observado
            ['política de integridad institucional 2026', 2,
                'Propuesta de política de integridad (versión observada)',
                'SGD-2026-0071-EV1', 'rechazado', 20,
                'https://storage.ugel.gob.pe/integridad/politica-v1-obs.pdf'],

            // Capacitación ejecutada — completada
            ['plan de capacitación en ética', 9,
                'Plan de capacitación en ética e integridad aprobado',
                'SGD-2026-0081-EV1', 'validado', 45,
                'https://storage.ugel.gob.pe/integridad/plan-capacitacion.pdf'],
            ['plan de capacitación en ética', 9,
                'Listas de asistencia a talleres de capacitación',
                'SGD-2026-0081-EV2', 'validado', 42,
                'https://storage.ugel.gob.pe/integridad/asistencia-capacitacion.pdf'],
            ['plan de capacitación en ética', 9,
                'Certificados de participación del personal',
                'SGD-2026-0081-EV3', 'validado', 38,
                'https://storage.ugel.gob.pe/integridad/certificados.pdf'],

            // Capacitación 80% — en proceso
            ['80% del personal en valores', 9,
                'Registro de personal capacitado (avance 75%)',
                'SGD-2026-0082-EV1', 'pendiente', 5,
                'https://storage.ugel.gob.pe/integridad/registro-personal-cap.pdf'],

            // Evaluación efectividad — en proceso
            ['efectividad de las capacitaciones', 10,
                'Cuestionario de evaluación post-capacitación',
                'SGD-2026-0083-EV1', 'pendiente', 7,
                'https://storage.ugel.gob.pe/integridad/cuestionario-eval.pdf'],

            // Canal de denuncias — completada
            ['canal de denuncias institucional', 2,
                'Resolución de implementación del canal de denuncias',
                'SGD-2026-0091-EV1', 'validado', 58,
                'https://storage.ugel.gob.pe/integridad/resolucion-canal.pdf'],
            ['canal de denuncias institucional', 2,
                'Manual de procedimientos del canal de denuncias',
                'SGD-2026-0091-EV2', 'validado', 55,
                'https://storage.ugel.gob.pe/integridad/manual-canal.pdf'],

            // Confidencialidad — en proceso
            ['confidencialidad y protección al denunciante', 6,
                'Borrador del protocolo de confidencialidad',
                'SGD-2026-0092-EV1', 'pendiente', 6,
                'https://storage.ugel.gob.pe/integridad/protocolo-conf.pdf'],

            // Seguimiento denuncias — vencida
            ['seguimiento y respuesta a denuncias', 4,
                'Registro de denuncias recibidas (incompleto)',
                'SGD-2026-0093-EV1', 'rechazado', 15,
                'https://storage.ugel.gob.pe/integridad/registro-denuncias.pdf'],
        ];

        $inserted = 0;
        foreach ($evidencias as $ev) {
            [$substr, $subido_por, $titulo, $sgd, $estado, $dias_atras, $url] = $ev;
            $act = $find($substr);
            if (!$act) continue;

            $validado_por = $estado === 'validado' ? 2 : null;
            $validado_at  = $estado === 'validado' ? $now->copy()->subDays($dias_atras - 2) : null;

            DB::table('evidencias')->insert([
                'modulo'        => 'integridad',
                'actividad_id'  => $act->id,
                'subido_por'    => $subido_por,
                'numero_sgd'    => $sgd,
                'titulo'        => $titulo,
                'descripcion'   => null,
                'url_documento' => $url,
                'estado'        => $estado,
                'validado_por'  => $validado_por,
                'validado_at'   => $validado_at,
                'motivo_rechazo'=> $estado === 'rechazado' ? 'Requiere correcciones antes de ser aprobado.' : null,
                'created_at'    => $now->copy()->subDays($dias_atras),
                'updated_at'    => $now->copy()->subDays($dias_atras),
            ]);
            $inserted++;
        }

        $this->command->info("✅ {$inserted} evidencias de integridad insertadas.");
    }
}
