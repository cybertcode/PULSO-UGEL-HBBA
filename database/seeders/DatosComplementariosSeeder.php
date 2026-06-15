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
        $sciId   = User::where('email', 'sci@ugelhuacaybamba.edu.pe')->value('id') ?? $adminId;
        $dirId   = User::where('email', 'director@ugelhuacaybamba.edu.pe')->value('id') ?? $adminId;

        // ── 1. Convertir algunas en_proceso → observado con historial realista ─
        Actividad::where('estado', 'en_proceso')
            ->take(5)
            ->get()
            ->each(function (Actividad $act) use ($sciId) {
                Actividad::withoutEvents(fn() => $act->update(['estado' => 'observado']));

                ActividadHistorial::create([
                    'actividad_id'   => $act->id,
                    'usuario_id'     => $sciId,
                    'campo'          => 'estado',
                    'valor_anterior' => 'en_proceso',
                    'valor_nuevo'    => 'observado',
                    'descripcion'    => 'Actividad observada por el Coordinador SCI. Falta documentación sustentatoria en la visita de seguimiento.',
                    'created_at'     => Carbon::now()->subDays(rand(3, 14)),
                    'updated_at'     => Carbon::now()->subDays(rand(3, 14)),
                ]);
            });

        // ── 2. Historial de avance progresivo para actividades completadas ─────
        Actividad::where('estado', 'completada')
            ->take(10)
            ->get()
            ->each(function (Actividad $act) use ($adminId, $sciId) {
                $avances  = [20, 40, 60, 80, 100];
                $anterior = 0;
                $base     = Carbon::now()->subDays(rand(40, 100));

                foreach ($avances as $i => $avance) {
                    ActividadHistorial::create([
                        'actividad_id'   => $act->id,
                        'usuario_id'     => $i % 2 === 0 ? $adminId : $sciId,
                        'campo'          => 'avance',
                        'valor_anterior' => (string) $anterior,
                        'valor_nuevo'    => (string) $avance,
                        'descripcion'    => "Avance actualizado al {$avance}%.",
                        'created_at'     => $base->copy()->addDays($i * 8),
                        'updated_at'     => $base->copy()->addDays($i * 8),
                    ]);
                    $anterior = $avance;
                }

                ActividadHistorial::create([
                    'actividad_id'   => $act->id,
                    'usuario_id'     => $sciId,
                    'campo'          => 'estado',
                    'valor_anterior' => 'en_proceso',
                    'valor_nuevo'    => 'completada',
                    'descripcion'    => 'Actividad completada. Evidencias validadas y aprobadas por el Coordinador SCI.',
                    'created_at'     => $base->copy()->addDays(40),
                    'updated_at'     => $base->copy()->addDays(40),
                ]);
            });

        // ── 3. Historial de escalado de prioridad en actividades vencidas ──────
        Actividad::where('estado', 'vencida')
            ->take(5)
            ->get()
            ->each(function (Actividad $act) use ($dirId) {
                ActividadHistorial::create([
                    'actividad_id'   => $act->id,
                    'usuario_id'     => $dirId,
                    'campo'          => 'prioridad',
                    'valor_anterior' => 'baja',
                    'valor_nuevo'    => 'alta',
                    'descripcion'    => 'Prioridad escalada a ALTA por vencimiento sin completarse. Se notificó al responsable.',
                    'created_at'     => Carbon::now()->subDays(rand(5, 18)),
                    'updated_at'     => Carbon::now()->subDays(rand(5, 18)),
                ]);
            });

        // ── 4. Historial de cambio de responsable en actividades en proceso ────
        Actividad::where('estado', 'en_proceso')
            ->take(4)
            ->get()
            ->each(function (Actividad $act) use ($sciId, $dirId) {
                ActividadHistorial::create([
                    'actividad_id'   => $act->id,
                    'usuario_id'     => $dirId,
                    'campo'          => 'responsable',
                    'valor_anterior' => 'Responsable anterior',
                    'valor_nuevo'    => 'Responsable reasignado',
                    'descripcion'    => 'Responsable reasignado por reorganización interna. Se actualizó el equipo de trabajo.',
                    'created_at'     => Carbon::now()->subDays(rand(10, 30)),
                    'updated_at'     => Carbon::now()->subDays(rand(10, 30)),
                ]);
            });

        // ── 5. Alertas manuales del sistema con email ─────────────────────────
        $actVencida = Actividad::where('estado', 'vencida')->first();
        $actObs     = Actividad::where('estado', 'observado')->first();
        $actPend    = Actividad::where('estado', 'pendiente')->first();

        if ($actVencida) {
            Alerta::firstOrCreate(
                ['actividad_id' => $actVencida->id, 'tipo' => 'sistema'],
                [
                    'titulo'             => 'Vencimiento crítico sin atención',
                    'mensaje'            => "La actividad \"{$actVencida->nombre}\" superó su fecha límite sin completarse. Se requiere acción inmediata o justificación formal.",
                    'prioridad'          => 'alta',
                    'leida'              => false,
                    'email_enviado'      => true,
                    'email_enviado_at'   => Carbon::now()->subHours(3),
                    'destinatario_email' => 'director@ugelhuacaybamba.edu.pe',
                    'unidad_organica_id' => $actVencida->unidad_organica_id,
                ]
            );
        }

        if ($actObs) {
            Alerta::firstOrCreate(
                ['actividad_id' => $actObs->id, 'tipo' => 'evidencia_falta'],
                [
                    'titulo'             => 'Actividad observada — subsanación pendiente',
                    'mensaje'            => "La actividad \"{$actObs->nombre}\" está en estado OBSERVADO. El responsable debe subsanar las observaciones y cargar los documentos faltantes.",
                    'prioridad'          => 'media',
                    'leida'              => false,
                    'email_enviado'      => false,
                    'unidad_organica_id' => $actObs->unidad_organica_id,
                ]
            );
        }

        if ($actPend) {
            Alerta::firstOrCreate(
                ['actividad_id' => $actPend->id, 'tipo' => 'avance_bajo'],
                [
                    'titulo'             => 'Actividad sin iniciar — cierre de trimestre próximo',
                    'mensaje'            => "La actividad \"{$actPend->nombre}\" no ha registrado ningún avance. El cierre del trimestre se aproxima.",
                    'prioridad'          => 'media',
                    'leida'              => false,
                    'email_enviado'      => false,
                    'unidad_organica_id' => $actPend->unidad_organica_id,
                ]
            );
        }

        $this->command->info('✓ DatosComplementariosSeeder: historiales de avance, estados y alertas complementarias generados.');
    }
}
