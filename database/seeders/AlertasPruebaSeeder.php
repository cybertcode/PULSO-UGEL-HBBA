<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\Alerta;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AlertasPruebaSeeder extends Seeder
{
    public function run(): void
    {
        // Limpia alertas existentes de prueba (tipo sistema manual)
        Alerta::whereNull('actividad_id')->delete();

        $users    = User::pluck('id')->toArray();
        $sciActs  = Actividad::where('modulo', 'sci')->pluck('id', 'unidad_organica_id')->toArray();
        $intActs  = Actividad::where('modulo', 'integridad')->pluck('id', 'unidad_organica_id')->toArray();

        // IDs de actividades con responsables para asignar alertas reales
        $sciIds = Actividad::where('modulo', 'sci')->pluck('id')->toArray();
        $intIds = Actividad::where('modulo', 'integridad')->pluck('id')->toArray();

        $now = Carbon::now();

        $alertas = [

            // ── VENCIDAS (alta prioridad) ─────────────────────────────
            [
                'actividad_id'  => $sciIds[0] ?? null,
                'usuario_id'    => $users[0] ?? null,
                'modulo'        => 'sci',
                'titulo'        => 'Actividad vencida sin completar — Eje 1',
                'mensaje'       => 'La actividad venció hace 15 días y no ha sido completada. Se requiere acción inmediata.',
                'tipo'          => 'vencimiento',
                'prioridad'     => 'alta',
                'leida'         => false,
                'created_at'    => $now->copy()->subDays(3),
            ],
            [
                'actividad_id'  => $intIds[0] ?? null,
                'usuario_id'    => $users[1] ?? null,
                'modulo'        => 'integridad',
                'titulo'        => 'Compromiso de integridad vencido',
                'mensaje'       => 'El plazo para completar este componente venció. Se debe regularizar a la brevedad.',
                'tipo'          => 'vencimiento',
                'prioridad'     => 'alta',
                'leida'         => false,
                'created_at'    => $now->copy()->subDays(1),
            ],
            [
                'actividad_id'  => $sciIds[1] ?? null,
                'usuario_id'    => $users[2] ?? null,
                'modulo'        => 'sci',
                'titulo'        => 'Fecha límite superada — Componente 2',
                'mensaje'       => 'La actividad debió completarse en la fecha establecida.',
                'tipo'          => 'vencimiento',
                'prioridad'     => 'alta',
                'leida'         => false,
                'created_at'    => $now->copy()->subHours(6),
            ],

            // ── POR VENCER (prioridad mixta) ──────────────────────────
            [
                'actividad_id'  => $sciIds[2] ?? null,
                'usuario_id'    => $users[3] ?? null,
                'modulo'        => 'sci',
                'titulo'        => 'Vence en 1 día — Actividad SCI urgente',
                'mensaje'       => 'Esta actividad vence mañana. Por favor completar antes del plazo.',
                'tipo'          => 'vencimiento_proximo',
                'prioridad'     => 'alta',
                'dias_anticipacion' => 1,
                'leida'         => false,
                'created_at'    => $now->copy()->subHours(2),
            ],
            [
                'actividad_id'  => $intIds[1] ?? null,
                'usuario_id'    => $users[4] ?? null,
                'modulo'        => 'integridad',
                'titulo'        => 'Vence en 5 días — Código de ética',
                'mensaje'       => 'Quedan 5 días para completar el registro de difusión del código de ética.',
                'tipo'          => 'vencimiento_proximo',
                'prioridad'     => 'media',
                'dias_anticipacion' => 5,
                'leida'         => false,
                'created_at'    => $now->copy()->subHours(4),
            ],
            [
                'actividad_id'  => $sciIds[3] ?? null,
                'usuario_id'    => $users[5] ?? null,
                'modulo'        => 'sci',
                'titulo'        => 'Vence en 10 días — Planificación estratégica',
                'mensaje'       => 'Tienes 10 días para registrar el avance de esta actividad de planificación.',
                'tipo'          => 'vencimiento_proximo',
                'prioridad'     => 'baja',
                'dias_anticipacion' => 10,
                'leida'         => false,
                'created_at'    => $now->copy()->subHours(8),
            ],

            // ── AVANCE BAJO ───────────────────────────────────────────
            [
                'actividad_id'  => $sciIds[4] ?? null,
                'usuario_id'    => $users[6] ?? null,
                'modulo'        => 'sci',
                'titulo'        => 'Avance bajo — Supervisión y monitoreo',
                'mensaje'       => 'Esta actividad lleva más de 30 días iniciada y el avance registrado es inferior al 20%.',
                'tipo'          => 'avance_bajo',
                'prioridad'     => 'media',
                'leida'         => false,
                'created_at'    => $now->copy()->subDays(2),
            ],
            [
                'actividad_id'  => $intIds[2] ?? null,
                'usuario_id'    => $users[7] ?? null,
                'modulo'        => 'integridad',
                'titulo'        => 'Avance insuficiente — Gestión de riesgos',
                'mensaje'       => 'El avance actual no es suficiente para cumplir el objetivo en el plazo establecido.',
                'tipo'          => 'avance_bajo',
                'prioridad'     => 'media',
                'leida'         => false,
                'created_at'    => $now->copy()->subDays(4),
            ],
            [
                'actividad_id'  => $sciIds[5] ?? null,
                'usuario_id'    => $users[8] ?? null,
                'modulo'        => 'sci',
                'titulo'        => 'Sin avance registrado — Control de calidad',
                'mensaje'       => 'La actividad no ha registrado ningún avance desde su inicio hace 45 días.',
                'tipo'          => 'avance_bajo',
                'prioridad'     => 'alta',
                'leida'         => false,
                'created_at'    => $now->copy()->subDays(5),
            ],

            // ── SIN EVIDENCIA ─────────────────────────────────────────
            [
                'actividad_id'  => $intIds[3] ?? null,
                'usuario_id'    => $users[9] ?? null,
                'modulo'        => 'integridad',
                'titulo'        => 'Sin evidencias — Difusión de valores institucionales',
                'mensaje'       => 'Esta actividad está en proceso pero no tiene ninguna evidencia adjunta.',
                'tipo'          => 'evidencia_falta',
                'prioridad'     => 'media',
                'leida'         => false,
                'created_at'    => $now->copy()->subDays(1),
            ],
            [
                'actividad_id'  => $sciIds[6] ?? null,
                'usuario_id'    => $users[10] ?? null,
                'modulo'        => 'sci',
                'titulo'        => 'Falta evidencia — Reunión de coordinación SCI',
                'mensaje'       => 'No se han registrado evidencias para esta actividad. Adjunta actas, fotos o documentos.',
                'tipo'          => 'evidencia_falta',
                'prioridad'     => 'baja',
                'leida'         => false,
                'created_at'    => $now->copy()->subHours(12),
            ],

            // ── SISTEMA (alertas manuales) ────────────────────────────
            [
                'usuario_id'    => $users[0] ?? null,
                'modulo'        => 'sci',
                'titulo'        => 'Recordatorio: Cierre de trimestre SCI',
                'mensaje'       => 'El cierre del primer trimestre de SCI es el 30 de junio. Asegúrate de tener todas las actividades al día.',
                'tipo'          => 'sistema',
                'prioridad'     => 'alta',
                'leida'         => false,
                'created_at'    => $now->copy()->subHours(1),
            ],
            [
                'usuario_id'    => $users[1] ?? null,
                'modulo'        => 'integridad',
                'titulo'        => 'Reunión de seguimiento de Integridad — 20 Jun',
                'mensaje'       => 'Se programó una reunión de seguimiento del Modelo de Integridad. Actualiza tus avances antes de la fecha.',
                'tipo'          => 'sistema',
                'prioridad'     => 'media',
                'leida'         => false,
                'created_at'    => $now->copy()->subHours(3),
            ],

            // ── YA RESUELTAS (leída = true) ───────────────────────────
            [
                'actividad_id'  => $sciIds[7] ?? null,
                'usuario_id'    => $users[2] ?? null,
                'modulo'        => 'sci',
                'titulo'        => 'RESUELTA: Actividad completada con éxito',
                'mensaje'       => 'La actividad fue completada antes del plazo. Alerta marcada como resuelta.',
                'tipo'          => 'vencimiento_proximo',
                'prioridad'     => 'media',
                'leida'         => true,
                'leida_at'      => $now->copy()->subDays(2),
                'created_at'    => $now->copy()->subDays(5),
            ],
            [
                'usuario_id'    => $users[3] ?? null,
                'modulo'        => 'integridad',
                'titulo'        => 'RESUELTA: Evidencias cargadas correctamente',
                'mensaje'       => 'Se adjuntaron las evidencias pendientes. Actividad normalizada.',
                'tipo'          => 'evidencia_falta',
                'prioridad'     => 'baja',
                'leida'         => true,
                'leida_at'      => $now->copy()->subDay(),
                'created_at'    => $now->copy()->subDays(3),
            ],
            [
                'usuario_id'    => $users[4] ?? null,
                'modulo'        => 'sci',
                'titulo'        => 'RESUELTA: Avance actualizado al 100%',
                'mensaje'       => 'El responsable actualizó el avance a 100%. Alerta cerrada.',
                'tipo'          => 'avance_bajo',
                'prioridad'     => 'media',
                'leida'         => true,
                'leida_at'      => $now->copy()->subHours(5),
                'created_at'    => $now->copy()->subDays(4),
            ],
        ];

        foreach ($alertas as $data) {
            // Asigna unidad_organica_id desde la actividad si existe
            if (!empty($data['actividad_id'])) {
                $act = Actividad::find($data['actividad_id']);
                if ($act) {
                    $data['unidad_organica_id'] = $act->unidad_organica_id;
                    // Si no tiene usuario asignado, usa el responsable de la actividad
                    if (empty($data['usuario_id'])) {
                        $resp = $act->responsables()->first();
                        if ($resp) $data['usuario_id'] = $resp->id;
                    }
                }
            }
            Alerta::create($data);
        }

        $this->command->info('✅ AlertasPruebaSeeder: ' . count($alertas) . ' alertas de prueba creadas.');
    }
}
