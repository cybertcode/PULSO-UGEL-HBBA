<?php

namespace App\Notifications;

use App\Models\Evidencia;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EvidenciaEnviada extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Evidencia $evidencia,
        public readonly string $accion, // 'nueva' | 'corregida'
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $titulo  = $this->evidencia->titulo;
        $codigo  = $this->evidencia->actividad?->codigo ?? '';
        $nombre  = $this->evidencia->actividad?->nombre ?? 'actividad';
        $modulo  = $this->evidencia->actividad?->modulo ?? 'sci';
        $url     = "/evidencias?modulo={$modulo}&estado=pendiente&actividad_id=" . ($this->evidencia->actividad_id ?? '');

        return match ($this->accion) {
            'nueva' => [
                'tipo'         => 'evidencia_pendiente',
                'titulo'       => '📄 Nueva evidencia para revisar',
                'mensaje'      => "El responsable envió una nueva evidencia \"$titulo\" para la actividad $codigo. Pendiente de tu validación.",
                'icono'        => 'tabler-file-upload',
                'color'        => 'info',
                'url'          => $url,
                'evidencia_id' => $this->evidencia->id,
                'actividad_id' => $this->evidencia->actividad_id,
                'modulo'       => $modulo,
            ],
            'corregida' => [
                'tipo'         => 'evidencia_corregida',
                'titulo'       => '🔄 Evidencia corregida — lista para revisar',
                'mensaje'      => "La evidencia \"$titulo\" de la actividad $codigo fue corregida por el responsable y está lista para validación.",
                'icono'        => 'tabler-file-check',
                'color'        => 'warning',
                'url'          => $url,
                'evidencia_id' => $this->evidencia->id,
                'actividad_id' => $this->evidencia->actividad_id,
                'modulo'       => $modulo,
            ],
            default => [
                'tipo'         => 'evidencia_pendiente',
                'titulo'       => '📄 Evidencia pendiente de revisión',
                'mensaje'      => "Hay una evidencia de la actividad $codigo esperando tu validación.",
                'icono'        => 'tabler-file-time',
                'color'        => 'info',
                'url'          => $url,
                'evidencia_id' => $this->evidencia->id,
                'actividad_id' => $this->evidencia->actividad_id,
                'modulo'       => $modulo,
            ],
        };
    }
}
