<?php

namespace App\Services;

use App\Jobs\EnviarAlertaEmail;
use App\Models\Actividad;
use App\Models\Alerta;
use App\Models\ConfiguracionInstitucional;

class AlertaService
{
    private ConfiguracionInstitucional $config;

    public function __construct()
    {
        $this->config = ConfiguracionInstitucional::cached();
    }

    /** Genera todas las alertas automáticas. Retorna cantidad creada. */
    public function generarAutomaticas(): int
    {
        $generadas = 0;
        $generadas += $this->alertasVencimiento();

        // Solo los niveles habilitados en configuración
        if ($this->config->notif_10dias) $generadas += $this->alertasProximidad(10);
        if ($this->config->notif_5dias)  $generadas += $this->alertasProximidad(5);
        if ($this->config->notif_1dia)   $generadas += $this->alertasProximidad(1);

        $generadas += $this->alertasAvanceBajo();
        $generadas += $this->alertasEvidenciaFaltante();
        return $generadas;
    }

    /** Filtra actividades según módulos habilitados en config */
    private function queryActividades()
    {
        $modulosActivos = [];
        if ($this->config->notif_modulo_sci)        $modulosActivos[] = 'sci';
        if ($this->config->notif_modulo_integridad) $modulosActivos[] = 'integridad';

        $query = Actividad::query();
        if (!empty($modulosActivos)) {
            $query->whereIn('modulo', $modulosActivos);
        } else {
            // Sin módulos activos, no generar alertas
            $query->whereRaw('1 = 0');
        }
        return $query;
    }

    /** Actividades ya vencidas sin alerta activa */
    private function alertasVencimiento(): int
    {
        if (!$this->config->notif_vencimiento) return 0;

        $generadas = 0;
        $this->queryActividades()
            ->whereNotIn('estado', ['completada', 'observado'])
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
                if ($this->config->notif_email) {
                    dispatch(new EnviarAlertaEmail($alerta));
                }
                $generadas++;
            });

        return $generadas;
    }

    /** Alertas de proximidad: N días antes del vencimiento (10, 5, 1) */
    private function alertasProximidad(int $dias): int
    {
        if (!$this->config->notif_vencimiento) return 0;

        $generadas = 0;
        $fecha = now()->addDays($dias)->toDateString();

        $this->queryActividades()
            ->whereNotIn('estado', ['completada', 'observado'])
            ->whereDate('fecha_limite', $fecha)
            ->whereDoesntHave('alertas', fn($q) => $q
                ->where('tipo', 'vencimiento_proximo')
                ->where('dias_anticipacion', $dias)
                ->where('leida', false)
            )
            ->each(function (Actividad $actividad) use ($dias, &$generadas) {
                $prioridad = $dias === 1 ? 'alta' : ($dias === 5 ? 'media' : 'baja');
                $alerta = Alerta::create([
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
                // Email solo en nivel urgente (1 día) o si está habilitado
                if ($this->config->notif_email && $dias === 1) {
                    dispatch(new EnviarAlertaEmail($alerta));
                }
                $generadas++;
            });

        return $generadas;
    }

    /** Actividades sin avance después de 7 días desde el inicio */
    private function alertasAvanceBajo(): int
    {
        if (!$this->config->notif_avance_bajo) return 0;

        $generadas = 0;
        $umbral = $this->config->notif_umbral_avance ?? 30;

        $this->queryActividades()
            ->where('estado', 'pendiente')
            ->where('avance', '<', $umbral)
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

        $this->queryActividades()
            ->where('estado', 'en_proceso')
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
