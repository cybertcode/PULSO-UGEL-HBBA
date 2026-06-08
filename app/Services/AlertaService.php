<?php

namespace App\Services;

use App\Jobs\EnviarAlertaEmail;
use App\Models\Actividad;
use App\Models\Alerta;

class AlertaService
{
    /**
     * Genera alertas automáticas basadas en el estado actual de las actividades.
     * Retorna el número de alertas nuevas creadas.
     */
    public function generarAutomaticas(): int
    {
        $generadas = 0;

        $generadas += $this->alertasVencimiento();
        $generadas += $this->alertasAvanceBajo();
        $generadas += $this->alertasEvidenciaFaltante();

        return $generadas;
    }

    /** Actividades vencidas sin alerta activa de vencimiento */
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
                    'titulo'             => "Sin evidencias: {$actividad->nombre}",
                    'mensaje'            => "La actividad «{$actividad->nombre}» está en proceso pero no tiene evidencias adjuntas.",
                    'tipo'               => 'evidencia_falta',
                    'prioridad'          => 'media',
                ]);
                $generadas++;
            });

        return $generadas;
    }
}
