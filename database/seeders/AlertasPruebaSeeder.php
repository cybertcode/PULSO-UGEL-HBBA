<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\Alerta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AlertasPruebaSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar alertas de sistema sin actividad (manuales)
        Alerta::whereNull('actividad_id')->delete();

        $users  = User::pluck('id')->toArray();
        $sciIds = Actividad::where('modulo', 'sci')->pluck('id')->values()->toArray();
        $intIds = Actividad::where('modulo', 'integridad')->pluck('id')->values()->toArray();
        $now    = Carbon::now();

        $alertas = [

            // ── VENCIMIENTO — actividades que ya pasaron su fecha límite ─────
            [
                'actividad_id' => $sciIds[0] ?? null,
                'usuario_id'   => $users[0] ?? null,
                'modulo'       => 'sci',
                'titulo'       => 'Actividad SCI vencida sin completar — Eje 1',
                'mensaje'      => 'Han pasado 15 días desde el vencimiento. Se requiere acción o justificación formal ante el Coordinador SCI.',
                'tipo'         => 'vencimiento',
                'prioridad'    => 'alta',
                'leida'        => false,
                'created_at'   => $now->copy()->subDays(3),
            ],
            [
                'actividad_id' => $intIds[0] ?? null,
                'usuario_id'   => $users[1] ?? null,
                'modulo'       => 'integridad',
                'titulo'       => 'Compromiso de Integridad vencido — Diagnóstico',
                'mensaje'      => 'El plazo para completar este componente de diagnóstico venció. Debe regularizarse antes del próximo corte de evaluación.',
                'tipo'         => 'vencimiento',
                'prioridad'    => 'alta',
                'leida'        => false,
                'created_at'   => $now->copy()->subDays(1),
            ],
            [
                'actividad_id' => $sciIds[1] ?? null,
                'usuario_id'   => $users[2] ?? null,
                'modulo'       => 'sci',
                'titulo'       => 'Fecha límite superada — Actividades de Control',
                'mensaje'      => 'La actividad debió completarse en la fecha establecida. El responsable debe reportar avance o solicitar prórroga.',
                'tipo'         => 'vencimiento',
                'prioridad'    => 'alta',
                'leida'        => false,
                'created_at'   => $now->copy()->subHours(6),
            ],

            // ── VENCIMIENTO PRÓXIMO — alertas anticipadas ─────────────────────
            [
                'actividad_id'      => $sciIds[2] ?? null,
                'usuario_id'        => $users[3] ?? null,
                'modulo'            => 'sci',
                'titulo'            => 'Vence mañana — Actividad SCI urgente',
                'mensaje'           => 'Esta actividad vence en 1 día. Asegúrate de completar el avance y adjuntar evidencias antes de la medianoche.',
                'tipo'              => 'vencimiento_proximo',
                'dias_anticipacion' => 1,
                'prioridad'         => 'alta',
                'leida'             => false,
                'created_at'        => $now->copy()->subHours(2),
            ],
            [
                'actividad_id'      => $intIds[1] ?? null,
                'usuario_id'        => $users[4] ?? null,
                'modulo'            => 'integridad',
                'titulo'            => 'Vence en 5 días — Difusión del Código de Ética',
                'mensaje'           => 'Quedan 5 días para registrar la difusión del código de ética institucional. Adjunta las listas de asistencia.',
                'tipo'              => 'vencimiento_proximo',
                'dias_anticipacion' => 5,
                'prioridad'         => 'media',
                'leida'             => false,
                'created_at'        => $now->copy()->subHours(4),
            ],
            [
                'actividad_id'      => $sciIds[3] ?? null,
                'usuario_id'        => $users[5] ?? null,
                'modulo'            => 'sci',
                'titulo'            => 'Vence en 10 días — Plan Estratégico Institucional',
                'mensaje'           => 'Tienes 10 días para registrar el seguimiento al PEI. No olvides adjuntar el informe de avance del trimestre.',
                'tipo'              => 'vencimiento_proximo',
                'dias_anticipacion' => 10,
                'prioridad'         => 'baja',
                'leida'             => false,
                'created_at'        => $now->copy()->subHours(8),
            ],

            // ── AVANCE BAJO — actividades con poco progreso ───────────────────
            [
                'actividad_id' => $sciIds[4] ?? null,
                'usuario_id'   => $users[6] ?? null,
                'modulo'       => 'sci',
                'titulo'       => 'Avance bajo — Supervisión y monitoreo SCI',
                'mensaje'      => 'Esta actividad lleva más de 30 días iniciada y el avance registrado es inferior al 20%. El Coordinador SCI ha sido notificado.',
                'tipo'         => 'avance_bajo',
                'prioridad'    => 'media',
                'leida'        => false,
                'created_at'   => $now->copy()->subDays(2),
            ],
            [
                'actividad_id' => $intIds[2] ?? null,
                'usuario_id'   => $users[7] ?? null,
                'modulo'       => 'integridad',
                'titulo'       => 'Avance insuficiente — Gestión de Riesgos de Corrupción',
                'mensaje'      => 'El avance actual (15%) no es suficiente para cumplir el objetivo en el plazo establecido. Se recomienda reprogramar las actividades del componente.',
                'tipo'         => 'avance_bajo',
                'prioridad'    => 'media',
                'leida'        => false,
                'created_at'   => $now->copy()->subDays(4),
            ],
            [
                'actividad_id' => $sciIds[5] ?? null,
                'usuario_id'   => $users[8] ?? null,
                'modulo'       => 'sci',
                'titulo'       => 'Sin avance registrado — Identificación de Riesgos',
                'mensaje'      => 'La actividad no registra ningún avance desde su inicio hace 45 días. El responsable debe actualizar el estado o informar impedimentos.',
                'tipo'         => 'avance_bajo',
                'prioridad'    => 'alta',
                'leida'        => false,
                'created_at'   => $now->copy()->subDays(5),
            ],

            // ── EVIDENCIA FALTANTE ─────────────────────────────────────────────
            [
                'actividad_id' => $intIds[3] ?? null,
                'usuario_id'   => $users[9] ?? null,
                'modulo'       => 'integridad',
                'titulo'       => 'Sin evidencias — Difusión de valores institucionales',
                'mensaje'      => 'Esta actividad está marcada como en proceso pero no tiene ninguna evidencia adjunta. Sube las actas, fotos o documentos de respaldo.',
                'tipo'         => 'evidencia_falta',
                'prioridad'    => 'media',
                'leida'        => false,
                'created_at'   => $now->copy()->subDays(1),
            ],
            [
                'actividad_id' => $sciIds[6] ?? null,
                'usuario_id'   => $users[10] ?? null,
                'modulo'       => 'sci',
                'titulo'       => 'Falta evidencia — Reunión de coordinación SCI',
                'mensaje'      => 'No se han registrado evidencias para esta actividad. Se requiere adjuntar el acta de reunión firmada por todos los participantes.',
                'tipo'         => 'evidencia_falta',
                'prioridad'    => 'baja',
                'leida'        => false,
                'created_at'   => $now->copy()->subHours(12),
            ],
            [
                'actividad_id' => $intIds[4] ?? null,
                'usuario_id'   => $users[11] ?? null,
                'modulo'       => 'integridad',
                'titulo'       => 'Evidencia incompleta — Canal de denuncias',
                'mensaje'      => 'El protocolo de confidencialidad debe adjuntarse como evidencia. Sin este documento la actividad no puede marcarse como completada.',
                'tipo'         => 'evidencia_falta',
                'prioridad'    => 'alta',
                'leida'        => false,
                'created_at'   => $now->copy()->subHours(18),
            ],

            // ── SISTEMA — comunicados manuales ────────────────────────────────
            [
                'usuario_id'  => $users[0] ?? null,
                'modulo'      => 'sci',
                'titulo'      => 'Recordatorio: Cierre del primer semestre SCI — 30 Jun',
                'mensaje'     => 'El cierre del primer semestre del Sistema de Control Interno es el 30 de junio de 2026. Asegúrate de completar y evidenciar todas las actividades asignadas.',
                'tipo'        => 'sistema',
                'prioridad'   => 'alta',
                'leida'       => false,
                'created_at'  => $now->copy()->subHours(1),
            ],
            [
                'usuario_id'  => $users[1] ?? null,
                'modulo'      => 'integridad',
                'titulo'      => 'Reunión de seguimiento del Modelo de Integridad — 20 Jun',
                'mensaje'     => 'Se ha programado una reunión de seguimiento del Modelo de Integridad. Por favor actualiza tus avances antes del 18 de junio para facilitar la revisión.',
                'tipo'        => 'sistema',
                'prioridad'   => 'media',
                'leida'       => false,
                'created_at'  => $now->copy()->subHours(3),
            ],
            [
                'usuario_id'  => $users[2] ?? null,
                'modulo'      => 'sci',
                'titulo'      => 'Nueva normativa CGR sobre SCI publicada',
                'mensaje'     => 'La Contraloría General de la República publicó la actualización de la Guía para la Implementación del SCI. Revisa la sección de Normativas para el documento actualizado.',
                'tipo'        => 'sistema',
                'prioridad'   => 'baja',
                'leida'       => false,
                'created_at'  => $now->copy()->subDays(1),
            ],

            // ── RESUELTAS (leída = true) — para probar filtros ────────────────
            [
                'actividad_id' => $sciIds[7] ?? null,
                'usuario_id'   => $users[3] ?? null,
                'modulo'       => 'sci',
                'titulo'       => '[Resuelta] Actividad completada antes del plazo',
                'mensaje'      => 'La actividad fue completada con 3 días de anticipación. Todas las evidencias fueron validadas por el Coordinador SCI.',
                'tipo'         => 'vencimiento_proximo',
                'prioridad'    => 'media',
                'leida'        => true,
                'leida_at'     => $now->copy()->subDays(2),
                'created_at'   => $now->copy()->subDays(5),
            ],
            [
                'usuario_id'  => $users[4] ?? null,
                'modulo'      => 'integridad',
                'titulo'      => '[Resuelta] Evidencias cargadas correctamente',
                'mensaje'     => 'El responsable adjuntó las evidencias faltantes. La actividad fue normalizada y marcada como en proceso con avance del 80%.',
                'tipo'        => 'evidencia_falta',
                'prioridad'   => 'baja',
                'leida'       => true,
                'leida_at'    => $now->copy()->subDay(),
                'created_at'  => $now->copy()->subDays(3),
            ],
            [
                'actividad_id' => $intIds[5] ?? null,
                'usuario_id'   => $users[5] ?? null,
                'modulo'       => 'integridad',
                'titulo'       => '[Resuelta] Avance actualizado al 100%',
                'mensaje'      => 'El responsable actualizó el avance a 100% y adjuntó el informe final. Alerta cerrada automáticamente.',
                'tipo'         => 'avance_bajo',
                'prioridad'    => 'media',
                'leida'        => true,
                'leida_at'     => $now->copy()->subHours(5),
                'created_at'   => $now->copy()->subDays(4),
            ],
        ];

        $creadas = 0;
        foreach ($alertas as $data) {
            if (!empty($data['actividad_id'])) {
                $act = Actividad::find($data['actividad_id']);
                if ($act) {
                    $data['unidad_organica_id'] = $act->unidad_organica_id;
                    if (empty($data['usuario_id'])) {
                        $resp = $act->responsables()->first();
                        if ($resp) $data['usuario_id'] = $resp->id;
                    }
                }
            }

            Alerta::create($data);
            $creadas++;
        }

        $this->command->info("✅ AlertasPruebaSeeder: {$creadas} alertas de prueba creadas ({$this->contarPorTipo($alertas)}).");
    }

    private function contarPorTipo(array $alertas): string
    {
        $conteo = [];
        foreach ($alertas as $a) {
            $tipo = $a['tipo'];
            $conteo[$tipo] = ($conteo[$tipo] ?? 0) + 1;
        }
        return implode(', ', array_map(fn($t, $c) => "{$c} {$t}", array_keys($conteo), $conteo));
    }
}
