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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class EnviarAlertaEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public Alerta $alerta) {}

    public function handle(): void
    {
        $config = ConfiguracionInstitucional::first();

        // Notificar al responsable de la actividad si existe
        if ($this->alerta->usuario_id) {
            $usuario = User::find($this->alerta->usuario_id);
            if ($usuario && $usuario->email) {
                $usuario->notify(new AlertaInstitucion($this->alerta));
                $this->alerta->update([
                    'email_enviado'    => true,
                    'email_enviado_at' => now(),
                    'destinatario_email' => $usuario->email,
                ]);
                return;
            }
        }

        // Si no hay responsable, enviar al correo institucional configurado
        $correoInstitucional = $config?->correo_institucional;
        if ($correoInstitucional) {
            // Crear un notifiable anónimo con el correo institucional
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

    public function failed(\Throwable $exception): void
    {
        \Log::error("Error al enviar alerta email ID {$this->alerta->id}: " . $exception->getMessage());
    }
}
