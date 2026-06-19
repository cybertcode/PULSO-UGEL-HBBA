<?php

namespace App\Notifications;

use App\Models\Actividad;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ActividadAsignada extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Actividad $actividad,
        public readonly string $tipo, // 'nueva' | 'actualizada' | 'fecha_limite'
        public readonly string $rolAsignado = 'principal',
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $codigo  = $this->actividad->codigo;
        $nombre  = $this->actividad->nombre;
        $modulo  = $this->actividad->modulo;
        $url     = '/mis-actividades?buscar=' . urlencode($codigo);
        $rol     = match($this->rolAsignado) {
            'principal'   => 'responsable principal',
            'colaborador' => 'colaborador',
            'supervisor'  => 'supervisor',
            default       => $this->rolAsignado,
        };

        return match ($this->tipo) {
            'nueva' => [
                'tipo'         => 'actividad_asignada',
                'titulo'       => '📋 Nueva actividad asignada',
                'mensaje'      => "Se te asignó la actividad $codigo como $rol: \"$nombre\". Revísala en Mis Actividades.",
                'icono'        => 'tabler-clipboard-plus',
                'color'        => 'primary',
                'url'          => $url,
                'actividad_id' => $this->actividad->id,
                'modulo'       => $modulo,
            ],
            'fecha_limite' => [
                'tipo'         => 'actividad_fecha_limite',
                'titulo'       => '📅 Fecha límite actualizada',
                'mensaje'      => "La fecha límite de la actividad $codigo fue modificada a " . ($this->actividad->fecha_limite?->format('d/m/Y') ?? '—') . '.',
                'icono'        => 'tabler-calendar-event',
                'color'        => 'warning',
                'url'          => $url,
                'actividad_id' => $this->actividad->id,
                'modulo'       => $modulo,
            ],
            default => [
                'tipo'         => 'actividad_actualizada',
                'titulo'       => '🔄 Actividad actualizada',
                'mensaje'      => "La actividad $codigo fue actualizada. Revisa los cambios en Mis Actividades.",
                'icono'        => 'tabler-clipboard-text',
                'color'        => 'info',
                'url'          => $url,
                'actividad_id' => $this->actividad->id,
                'modulo'       => $modulo,
            ],
        };
    }
}
