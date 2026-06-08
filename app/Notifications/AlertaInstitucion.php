<?php

namespace App\Notifications;

use App\Models\Alerta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlertaInstitucion extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Alerta $alerta) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tipo = match($this->alerta->tipo) {
            'vencimiento'     => 'Actividad Vencida',
            'avance_bajo'     => 'Avance Insuficiente',
            'evidencia_falta' => 'Evidencia Faltante',
            default           => 'Notificación del Sistema',
        };

        $colorBorde = match($this->alerta->prioridad) {
            'alta'  => '#ea5455',
            'media' => '#ff9f43',
            default => '#28c76f',
        };

        $iconoTexto = match($this->alerta->tipo) {
            'vencimiento'     => '⏰',
            'avance_bajo'     => '📉',
            'evidencia_falta' => '📎',
            default           => '🔔',
        };

        $prioridadLabel = match($this->alerta->prioridad) {
            'alta'  => 'ALTA PRIORIDAD',
            'media' => 'MEDIA PRIORIDAD',
            default => 'BAJA PRIORIDAD',
        };

        $actividad = $this->alerta->actividad;
        $unidad    = $this->alerta->unidadOrganica?->nombre ?? '—';

        return (new MailMessage)
            ->subject("{$iconoTexto} [PULSO UGEL] {$tipo} — {$prioridadLabel}")
            ->view('emails.alerta-institucion', [
                'alerta'         => $this->alerta,
                'tipo'           => $tipo,
                'colorBorde'     => $colorBorde,
                'iconoTexto'     => $iconoTexto,
                'prioridadLabel' => $prioridadLabel,
                'actividad'      => $actividad,
                'unidad'         => $unidad,
                'urlSistema'     => url('/mis-actividades'),
                'urlAlertas'     => url('/alertas'),
            ]);
    }
}
