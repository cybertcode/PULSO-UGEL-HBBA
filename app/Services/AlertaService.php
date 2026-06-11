<?php

namespace App\Services;

use App\Jobs\EnviarAlertaEmail;
use App\Models\Actividad;
use App\Models\Alerta;

class AlertaService
{
    /** Genera todas las alertas automáticas. Retorna cantidad creada. */
    public function generarAutomaticas(): int
    {
        $generadas = 0;
        $generadas += $this->alertasVencimiento();
        $generadas += $this->alertasProximidad(10);
        $generadas += $this->alertasProximidad(5);
        $generadas += $this->alertasProximidad(1);
        $generadas += $this->alertasAvanceBajo();
        $generadas += $this->alertasEvidenciaFaltante();
        return $generadas;
    }

    /** Actividades ya vencidas sin alerta activa */
    private function alertasVencimiento(): int
    {
        $generadas = 0;

        Actividad::whereNotIn('estado', ['completada', 'observado'])
            ->whereDate('fecha_limite', '<', now())
            ->whereDoesntHave('alertas', fn($q) => $q->where('tipo', 'vencimiento')->where('leida', false))
            ->each(function (Actividad $actividad) use (&$generadas) {
                $alerta = Alerta::create([
                    'actividad_id'       => $actividad->id,
                    'usuario_id'         => $actividad->responsablePrincipal()->first()?->id
                                         ?? $actividad->responsables()->first()?->id,
                    'unidad_organica_id' => $actividad->unidad_organica_id,
                    'modulo'             => $actividad->modulo,
                    'titulo'             => "Actividad vencida: {$actividad->nombre}",
                    'mensaje'            => "La actividad «{$actividad->nombre}» (código: {$actividad->codigo}) venció el {$actividad->fecha_limite->format('d/m/Y')} y no ha sido completada.",
                    'tipo'               => 'vencimiento',
                    'prioridad'          => 'alta',
                ]);
                dispatch(new EnviarAlertaEmail($alerta));
                $generadas++;
            });

        return $generadas;
    }

    /** Alertas de proximidad: N días antes del vencimiento (10, 5, 1) */
    private function alertasProximidad(int $dias): int
    {
        $generadas = 0;
        $fecha = now()->addDays($dias)->toDateString();

        Actividad::whereNotIn('estado', ['completada', 'observado'])
            ->whereDate('fecha_limite', $fecha)
            ->whereDoesntHave('alertas', fn($q) => $q
                ->where('tipo', 'vencimiento_proximo')
                ->where('dias_anticipacion', $dias)
                ->where('leida', false)
            )
            ->each(function (Actividad $actividad) use ($dias, &$generadas) {
                $prioridad = $dias === 1 ? 'alta' : ($dias === 5 ? 'media' : 'baja');
                Alerta::create([
                    'actividad_id'       => $actividad->id,
                    'usuario_id'         => $actividad->responsablePrincipal()->first()?->id
                                         ?? $actividad->responsables()->first()?->id,
                    'unidad_organica_id' => $actividad->unidad_organica_id,
                    'modulo'             => $actividad->modulo,
                    'dias_anticipacion'  => $dias,
                    'titulo'             => "Vence en {$dias} día(s): {$actividad->nombre}",
                    'mensaje'            => "La actividad «{$actividad->nombre}» (código: {$actividad->codigo}) vence el {$actividad->fecha_limite->format('d/m/Y')} — quedan {$dias} día(s).",
                    'tipo'               => 'vencimiento_proximo',
                    'prioridad'          => $prioridad,
                ]);
                $generadas++;
            });

        return $generadas;
    }

    /** Actividades sin avance después de 7 días desde el inicio */
    private function alertasAvanceBajo(): int
    {
        $generadas = 0;

        Actividad::where('estado', 'pendiente')
            ->where('avance', 0)
            ->whereNotNull('fecha_inicio')
            ->whereDate('fecha_inicio', '<', now()->subDays(7))
            ->whereDoesntHave('alertas', fn($q) => $q->where('tipo', 'avance_bajo')->where('leida', false))
            ->each(function (Actividad $actividad) use (&$generadas) {
                Alerta::create([
                    'actividad_id'       => $actividad->id,
                    'usuario_id'         => $actividad->responsablePrincipal()->first()?->id
                                         ?? $actividad->responsables()->first()?->id,
                    'unidad_organica_id' => $actividad->unidad_organica_id,
                    'modulo'             => $actividad->modulo,
                    'titulo'             => "Sin avance: {$actividad->nombre}",
                    'mensaje'            => "La actividad «{$actividad->nombre}» lleva más de 7 días sin registrar avance.",
                    'tipo'               => 'avance_bajo',
                    'prioridad'          => 'media',
                ]);
                $generadas++;
            });

        return $generadas;
    }

    /** Actividades en proceso sin evidencias adjuntas */
    private function alertasEvidenciaFaltante(): int
    {
        $generadas = 0;

        Actividad::where('estado', 'en_proceso')
            ->whereDoesntHave('evidencias')
            ->whereDoesntHave('alertas', fn($q) => $q->where('tipo', 'evidencia_falta')->where('leida', false))
            ->each(function (Actividad $actividad) use (&$generadas) {
                Alerta::create([
                    'actividad_id'       => $actividad->id,
                    'usuario_id'         => $actividad->responsablePrincipal()->first()?->id
                                         ?? $actividad->responsables()->first()?->id,
                    'unidad_organica_id' => $actividad->unidad_organica_id,
                    'modulo'             => $actividad->modulo,
                    'titulo'             => "Sin evidencias: {$actividad->nombre}",
                    'mensaje'            => "La actividad «{$actividad->nombre}» está en proceso pero no tiene evidencias adjuntas.",
                    'tipo'               => 'evidencia_falta',
                    'prioridad'          => 'media',
                ]);
                $generadas++;
            });

        return $generadas;
    }

    /** Envía email manual a los responsables de una alerta */
    public function enviarEmailManual(Alerta $alerta): void
    {
        dispatch(new EnviarAlertaEmail($alerta));
        $alerta->update([
            'email_enviado'    => true,
            'email_enviado_at' => now(),
        ]);
    }
}
