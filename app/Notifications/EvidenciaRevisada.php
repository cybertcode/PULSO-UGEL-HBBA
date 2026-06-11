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
        public readonly string $accion, // 'validado' | 'rechazado' | 'observado'
        public readonly ?string $motivo = null,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $titulo = $this->evidencia->titulo;
        $act    = $this->evidencia->actividad?->nombre ?? 'actividad';

        return match ($this->accion) {
            'validado' => [
                'tipo'    => 'evidencia_validada',
                'titulo'  => 'Evidencia aprobada',
                'mensaje' => "Tu evidencia \"$titulo\" para la actividad \"$act\" fue validada correctamente.",
                'icono'   => 'tabler-file-check',
                'color'   => 'success',
                'url'     => '/evidencias?modulo=' . ($this->evidencia->actividad?->modulo ?? 'sci'),
                'evidencia_id' => $this->evidencia->id,
            ],
            'rechazado' => [
                'tipo'    => 'evidencia_rechazada',
                'titulo'  => 'Evidencia rechazada — requiere corrección',
                'mensaje' => "Tu evidencia \"$titulo\" fue rechazada." . ($this->motivo ? " Motivo: {$this->motivo}" : ''),
                'icono'   => 'tabler-file-x',
                'color'   => 'danger',
                'url'     => '/evidencias?modulo=' . ($this->evidencia->actividad?->modulo ?? 'sci'),
                'evidencia_id' => $this->evidencia->id,
                'motivo'  => $this->motivo,
            ],
            default => [
                'tipo'    => 'evidencia_observada',
                'titulo'  => 'Evidencia observada',
                'mensaje' => "Tu evidencia \"$titulo\" tiene observaciones." . ($this->motivo ? " {$this->motivo}" : ''),
                'icono'   => 'tabler-file-alert',
                'color'   => 'warning',
                'url'     => '/evidencias?modulo=' . ($this->evidencia->actividad?->modulo ?? 'sci'),
                'evidencia_id' => $this->evidencia->id,
            ],
        };
    }
}
