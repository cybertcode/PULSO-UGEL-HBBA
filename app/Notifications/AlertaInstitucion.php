<?php

namespace App\Notifications;

use App\Models\Alerta;
use App\Models\ConfiguracionInstitucional;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlertaInstitucion extends Notification
{

    public function __construct(public Alerta $alerta) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $dias = $this->alerta->dias_anticipacion;
        $tipo = match($this->alerta->tipo) {
            'vencimiento'          => 'Actividad Vencida',
            'vencimiento_proximo'  => $dias ? "Vence en {$dias} día(s)" : 'Próximo Vencimiento',
            'avance_bajo'          => 'Avance Insuficiente',
            'evidencia_falta'      => 'Evidencia Faltante',
            default                => 'Notificación del Sistema',
        };

        $colorBorde = match($this->alerta->prioridad) {
            'alta'  => '#ea5455',
            'media' => '#ff9f43',
            default => '#28c76f',
        };

        $iconoTexto = match($this->alerta->tipo) {
            'vencimiento'         => '⏰',
            'vencimiento_proximo' => '⚠️',
            'avance_bajo'         => '📉',
            'evidencia_falta'     => '📎',
            default               => '🔔',
        };

        $prioridadLabel = match($this->alerta->prioridad) {
            'alta'  => 'ALTA PRIORIDAD',
            'media' => 'MEDIA PRIORIDAD',
            default => 'BAJA PRIORIDAD',
        };

        $actividad = $this->alerta->actividad;
        $unidad    = $this->alerta->unidadOrganica?->nombre ?? '—';
        $ci        = ConfiguracionInstitucional::cached();
        $instSigla = $ci?->sigla ?? $ci?->nombre_institucion ?? 'PULSO UGEL';
        $instNombre = $ci?->nombre_institucion ?? 'PULSO UGEL';
        $instLugar = implode(' &bull; ', array_filter([
            $ci?->provincia,
            $ci?->departamento,
            'Perú',
        ]));

        return (new MailMessage)
            ->subject("{$iconoTexto} [{$instSigla}] {$tipo} — {$prioridadLabel}")
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
                'instSigla'      => $instSigla,
                'instNombre'     => $instNombre,
                'instLugar'      => $instLugar,
                'ci'             => $ci,
            ]);
    }
}
