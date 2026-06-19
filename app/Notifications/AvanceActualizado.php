<?php

namespace App\Notifications;

use App\Models\Actividad;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AvanceActualizado extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Actividad $actividad,
        public readonly int $avanceNuevo,
        public readonly int $avanceAnterior,
        public readonly string $nombreResponsable,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $codigo  = $this->actividad->codigo;
        $url     = '/mis-actividades?buscar=' . urlencode($codigo);
        $modulo  = $this->actividad->modulo === 'sci' ? 'SCI' : 'Integridad';
        $delta   = $this->avanceNuevo - $this->avanceAnterior;
        $signo   = $delta >= 0 ? '+' : '';

        return [
            'tipo'         => 'avance_actualizado',
            'titulo'       => '📊 Avance actualizado',
            'mensaje'      => "{$this->nombreResponsable} actualizó el avance de [{$modulo}] {$codigo} de {$this->avanceAnterior}% a {$this->avanceNuevo}% ({$signo}{$delta}%).",
            'icono'        => 'tabler-chart-bar',
            'color'        => 'info',
            'url'          => $url,
            'actividad_id' => $this->actividad->id,
            'modulo'       => $this->actividad->modulo,
        ];
    }
}
