<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\ActividadHistorial;
use App\Models\Alerta;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatosComplementariosSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('email', 'admin@admin.com')->value('id');
        $sciId   = User::where('email', 'sci@ugel.gob.pe')->value('id');
        $dirId   = User::where('email', 'director@ugel.gob.pe')->value('id');

        // 1. Cambiar algunos en_proceso a observado para tener datos reales
        Actividad::where('estado', 'en_proceso')
            ->take(6)
            ->get()
            ->each(function (Actividad $act) use ($sciId) {
                // Desactivar observer para no duplicar historial
                Actividad::withoutEvents(function () use ($act) {
                    $act->update(['estado' => 'observado']);
                });

                // Crear historial manualmente con datos realistas
                ActividadHistorial::create([
                    'actividad_id'   => $act->id,
                    'usuario_id'     => $sciId,
                    'campo'          => 'estado',
                    'valor_anterior' => 'en_proceso',
                    'valor_nuevo'    => 'observado',
                    'descripcion'    => 'Actividad observada por falta de evidencias documentales en la visita de seguimiento.',
                    'created_at'     => Carbon::now()->subDays(rand(1, 15)),
                ]);
            });

        // 2. Historial de cambios de avance para actividades completadas
        Actividad::where('estado', 'completada')->take(8)->get()->each(function (Actividad $act) use ($adminId, $sciId) {
            $avances = [25, 50, 75, 100];
            $anterior = 0;
            $base = Carbon::now()->subDays(rand(30, 90));
            foreach ($avances as $i => $avance) {
                ActividadHistorial::create([
                    'actividad_id'   => $act->id,
                    'usuario_id'     => $i % 2 === 0 ? $adminId : $sciId,
                    'campo'          => 'avance',
                    'valor_anterior' => (string) $anterior,
                    'valor_nuevo'    => (string) $avance,
                    'descripcion'    => "Avance actualizado al {$avance}%.",
                    'created_at'     => $base->copy()->addDays($i * 7),
                ]);
                $anterior = $avance;
            }
            // Último registro: estado a completada
            ActividadHistorial::create([
                'actividad_id'   => $act->id,
                'usuario_id'     => $sciId,
                'campo'          => 'estado',
                'valor_anterior' => 'en_proceso',
                'valor_nuevo'    => 'completada',
                'descripcion'    => 'Actividad completada. Todas las evidencias validadas.',
                'created_at'     => $base->copy()->addDays(28),
            ]);
        });

        // 3. Historial de cambio de prioridad para actividades vencidas
        Actividad::where('estado', 'vencida')->take(4)->get()->each(function (Actividad $act) use ($dirId) {
            ActividadHistorial::create([
                'actividad_id'   => $act->id,
                'usuario_id'     => $dirId,
                'campo'          => 'prioridad',
                'valor_anterior' => 'baja',
                'valor_nuevo'    => 'alta',
                'descripcion'    => 'Prioridad escalada por vencimiento inminente sin completarse.',
                'created_at'     => Carbon::now()->subDays(rand(5, 20)),
            ]);
        });

        // 4. Alertas manuales adicionales (tipo sistema y vencimiento con email)
        $actividadVencida = Actividad::where('estado', 'vencida')->first();
        $actividadObserv  = Actividad::where('estado', 'observado')->first();

        if ($actividadVencida) {
            Alerta::firstOrCreate(
                ['actividad_id' => $actividadVencida->id, 'tipo' => 'sistema'],
                [
                    'titulo'              => 'Alerta de Vencimiento Crítico',
                    'mensaje'             => "La actividad \"{$actividadVencida->nombre}\" ha superado su fecha límite sin ser completada. Se requiere acción inmediata.",
                    'prioridad'           => 'alta',
                    'leida'               => false,
                    'email_enviado'       => true,
                    'email_enviado_at'    => Carbon::now()->subHours(2),
                    'destinatario_email'  => 'director@ugel.gob.pe',
                ]
            );
        }

        if ($actividadObserv) {
            Alerta::firstOrCreate(
                ['actividad_id' => $actividadObserv->id, 'tipo' => 'evidencia_falta'],
                [
                    'titulo'      => 'Evidencia Pendiente de Subsanación',
                    'mensaje'     => "La actividad \"{$actividadObserv->nombre}\" está en estado OBSERVADO. El responsable debe subsanar las observaciones y cargar las evidencias faltantes.",
                    'prioridad'   => 'media',
                    'leida'       => false,
                    'email_enviado' => false,
                ]
            );
        }
    }
}
