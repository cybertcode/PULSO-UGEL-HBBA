<?php

namespace App\Services;

use App\Models\Actividad;
use App\Models\Alerta;
use App\Models\ConfiguracionInstitucional;
use App\Models\UnidadOrganica;
use App\Models\User;

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

    /**
     * Envío manual de email para una alerta existente.
     * Síncrono — no usa Queue. Lanza excepción si falla para que el controller la capture.
     */
    public function enviarEmailManual(Alerta $alerta): void
    {
        $config = ConfiguracionInstitucional::cached();

        $destinatario = $this->resolverDestinatario($alerta, $config);

        if (!$destinatario) {
            throw new \RuntimeException('No hay destinatario configurado. Asigna un responsable o un correo institucional.');
        }

        \Illuminate\Support\Facades\Notification::sendNow(
            $destinatario,
            new \App\Notifications\AlertaInstitucion($alerta)
        );

        $email = $destinatario instanceof User
            ? $destinatario->email
            : $config->correo_institucional;

        $alerta->updateQuietly([
            'email_enviado'      => true,
            'email_enviado_at'   => now(),
            'destinatario_email' => $email,
        ]);
    }

    /**
     * Envío masivo de emails según el tipo_destino de la alerta.
     * Retorna array con conteos: ['enviados' => N, 'fallidos' => N, 'emails' => [...]]
     */
    public function enviarEmailGrupo(Alerta $alerta): array
    {
        $config = ConfiguracionInstitucional::cached();
        $destinatarios = $this->resolverDestinatariosGrupo($alerta);

        if (empty($destinatarios)) {
            throw new \RuntimeException('No se encontraron destinatarios para este grupo.');
        }

        $enviados = 0;
        $fallidos = 0;
        $emailsEnviados = [];

        foreach ($destinatarios as $usuario) {
            try {
                \Illuminate\Support\Facades\Notification::sendNow(
                    $usuario,
                    new \App\Notifications\AlertaInstitucion($alerta)
                );
                $enviados++;
                $emailsEnviados[] = $usuario->email;
            } catch (\Throwable) {
                $fallidos++;
            }
        }

        if ($enviados > 0) {
            $alerta->updateQuietly([
                'email_enviado'      => true,
                'email_enviado_at'   => now(),
                'destinatario_email' => implode(', ', array_slice($emailsEnviados, 0, 3))
                                        . (count($emailsEnviados) > 3 ? ' y ' . (count($emailsEnviados) - 3) . ' más' : ''),
            ]);
        }

        return compact('enviados', 'fallidos', 'emailsEnviados');
    }

    /**
     * Resuelve la lista de usuarios destinatarios según tipo_destino de la alerta.
     */
    public function resolverDestinatariosGrupo(Alerta $alerta): \Illuminate\Support\Collection
    {
        return match($alerta->tipo_destino) {
            'todos' => User::permission('alertas.ver')
                          ->whereNotNull('email')
                          ->get(),
            'unidad' => $alerta->unidad_organica_id
                          ? User::permission('alertas.ver')
                                ->where('unidad_organica_id', $alerta->unidad_organica_id)
                                ->whereNotNull('email')
                                ->get()
                          : collect(),
            default => $alerta->usuario_id
                          ? User::where('id', $alerta->usuario_id)->whereNotNull('email')->get()
                          : collect(),
        };
    }

    /** Resuelve el destinatario: responsable directo o correo institucional como fallback */
    private function resolverDestinatario(Alerta $alerta, ConfiguracionInstitucional $config): mixed
    {
        if ($alerta->usuario_id) {
            $usuario = User::find($alerta->usuario_id);
            if ($usuario?->email) {
                return $usuario;
            }
        }

        if ($config->correo_institucional) {
            return (new \Illuminate\Notifications\AnonymousNotifiable)
                ->route('mail', $config->correo_institucional);
        }

        return null;
    }
}
