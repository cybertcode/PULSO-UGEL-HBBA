<?php

namespace App\Jobs;

use App\Models\Alerta;
use App\Models\User;
use App\Notifications\AlertaInstitucion;
use App\Models\ConfiguracionInstitucional;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class EnviarAlertaEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public Alerta $alerta) {}

    public function handle(): void
    {
        $config = ConfiguracionInstitucional::first();

        if (!$config?->notif_email) {
            return;
        }

        // Aplicar configuración SMTP dinámica si está definida en BD
        $this->aplicarSmtpDinamico($config);

        // Notificar al responsable de la actividad si existe
        if ($this->alerta->usuario_id) {
            $usuario = User::find($this->alerta->usuario_id);
            if ($usuario?->email) {
                $usuario->notify(new AlertaInstitucion($this->alerta));
                $this->alerta->update([
                    'email_enviado'      => true,
                    'email_enviado_at'   => now(),
                    'destinatario_email' => $usuario->email,
                ]);
                return;
            }
        }

        // Fallback: correo institucional configurado
        $correoInstitucional = $config?->correo_institucional;
        if ($correoInstitucional) {
            $recipient = (new \Illuminate\Notifications\AnonymousNotifiable)
                ->route('mail', $correoInstitucional);
            Notification::send($recipient, new AlertaInstitucion($this->alerta));

            $this->alerta->update([
                'email_enviado'      => true,
                'email_enviado_at'   => now(),
                'destinatario_email' => $correoInstitucional,
            ]);
        }
    }

    /** Sobreescribe config de correo en runtime con los valores de BD */
    private function aplicarSmtpDinamico(ConfiguracionInstitucional $config): void
    {
        if (empty($config->mail_host)) {
            return; // Sin config en BD, usar .env
        }

        $fromName = $config->mail_from_name ?: ($config->nombre_institucion ?? config('mail.from.name'));

        Config::set('mail.mailers.smtp.host',       $config->mail_host);
        Config::set('mail.mailers.smtp.port',       $config->mail_port ?? 587);
        Config::set('mail.mailers.smtp.username',   $config->mail_username);
        Config::set('mail.mailers.smtp.password',   $config->mail_password);
        Config::set('mail.mailers.smtp.encryption', $config->mail_encryption ?? 'tls');
        Config::set('mail.from.name',               $fromName);

        if ($config->mail_username) {
            Config::set('mail.from.address', $config->mail_username);
        }

        // Reinicializar el mailer para que tome los nuevos valores
        app()->forgetInstance('swift.mailer');
        app()->forgetInstance('swift.transport');
        app()->forgetInstance('mailer');
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Error al enviar alerta email ID {$this->alerta->id}: " . $exception->getMessage());
    }
}
