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
        $prioridadLabel = match($this->alerta->prioridad) {
            'alta'  => '🔴 ALTA PRIORIDAD',
            'media' => '🟡 MEDIA PRIORIDAD',
            default => '🔵 BAJA PRIORIDAD',
        };

        $tipoLabel = match($this->alerta->tipo) {
            'vencimiento'     => 'Actividad Vencida',
            'avance_bajo'     => 'Avance Bajo',
            'evidencia_falta' => 'Evidencia Faltante',
            default           => 'Notificación del Sistema',
        };

        return (new MailMessage)
            ->subject("[PULSO UGEL] {$prioridadLabel} — {$tipoLabel}")
            ->greeting("Estimado/a servidor/a,")
            ->line("**{$this->alerta->titulo}**")
            ->line($this->alerta->mensaje)
            ->line("---")
            ->line("**Prioridad:** {$prioridadLabel}")
            ->line("**Fecha:** " . $this->alerta->created_at->format('d/m/Y H:i'))
            ->action('Ver en el Sistema PULSO', url('/alertas'))
            ->line('Por favor, tome las acciones necesarias a la brevedad.')
            ->salutation('Sistema PULSO — Control Interno y Modelo de Integridad');
    }
}
