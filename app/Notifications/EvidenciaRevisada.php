<?php

namespace App\Notifications;

use App\Models\Evidencia;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class EvidenciaRevisada extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Evidencia $evidencia,
        public readonly string $accion, // 'validado' | 'rechazado' | 'eliminado' | 'observado'
        public readonly ?string $motivo = null,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $titulo  = $this->evidencia->titulo;
        $act     = $this->evidencia->actividad?->nombre ?? 'actividad';
        $codigo  = $this->evidencia->actividad?->codigo ?? '';
        $modulo  = $this->evidencia->actividad?->modulo ?? 'sci';
        $urlBase = "/evidencias?modulo={$modulo}&actividad_id=" . ($this->evidencia->actividad_id ?? '');

        return match ($this->accion) {
            'validado' => [
                'tipo'         => 'evidencia_validada',
                'titulo'       => '✅ Evidencia validada',
                'mensaje'      => "Tu evidencia \"$titulo\" de la actividad $codigo fue aprobada. La actividad quedó marcada como completada.",
                'icono'        => 'tabler-file-check',
                'color'        => 'success',
                'url'          => $urlBase,
                'evidencia_id' => $this->evidencia->id,
                'actividad_id' => $this->evidencia->actividad_id,
            ],
            'rechazado' => [
                'tipo'         => 'evidencia_rechazada',
                'titulo'       => '❌ Evidencia rechazada — requiere corrección',
                'mensaje'      => "Tu evidencia \"$titulo\" de la actividad $codigo fue rechazada."
                                . ($this->motivo ? " Motivo: {$this->motivo}" : '')
                                . " Ingresa a Mis Actividades para corregirla y reenviarla.",
                'icono'        => 'tabler-file-x',
                'color'        => 'danger',
                'url'          => '/mis-actividades?estado=observado',
                'url_evidencia'=> $urlBase . '&estado=rechazado',
                'evidencia_id' => $this->evidencia->id,
                'actividad_id' => $this->evidencia->actividad_id,
                'motivo'       => $this->motivo,
            ],
            'eliminado' => [
                'tipo'         => 'evidencia_eliminada',
                'titulo'       => '🗑️ Evidencia eliminada por el validador',
                'mensaje'      => "Tu evidencia \"$titulo\" de la actividad $codigo fue eliminada por el coordinador. Debes volver a subir una nueva evidencia para continuar.",
                'icono'        => 'tabler-file-off',
                'color'        => 'warning',
                'url'          => "/mis-actividades",
                'evidencia_id' => $this->evidencia->id,
                'actividad_id' => $this->evidencia->actividad_id,
            ],
            default => [
                'tipo'         => 'evidencia_observada',
                'titulo'       => '⚠️ Evidencia con observaciones',
                'mensaje'      => "Tu evidencia \"$titulo\" de la actividad $codigo tiene observaciones." . ($this->motivo ? " {$this->motivo}" : ''),
                'icono'        => 'tabler-file-alert',
                'color'        => 'warning',
                'url'          => $urlBase,
                'evidencia_id' => $this->evidencia->id,
                'actividad_id' => $this->evidencia->actividad_id,
            ],
        };
    }
}
