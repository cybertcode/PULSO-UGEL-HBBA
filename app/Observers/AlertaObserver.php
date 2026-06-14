<?php

namespace App\Observers;

use App\Models\Alerta;
use App\Services\AlertaService;
use Illuminate\Support\Facades\Log;

class AlertaObserver
{
    public function __construct(private AlertaService $service) {}

    /**
     * Al crear una alerta, envía el email síncrono si notif_email está activo.
     * El envío manual se hace desde AlertaService::enviarEmailManual().
     */
    public function created(Alerta $alerta): void
    {
        $config = \App\Models\ConfiguracionInstitucional::cached();

        if (!$config->notif_email) {
            return;
        }

        try {
            $this->service->enviarEmailManual($alerta);
        } catch (\Throwable $e) {
            Log::error("AlertaObserver: fallo al enviar email alerta ID {$alerta->id} — " . $e->getMessage());
        }
    }
}
