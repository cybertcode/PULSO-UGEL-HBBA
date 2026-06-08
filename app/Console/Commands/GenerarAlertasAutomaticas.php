<?php

namespace App\Console\Commands;

use App\Services\AlertaService;
use Illuminate\Console\Command;

class GenerarAlertasAutomaticas extends Command
{
    protected $signature   = 'pulso:alertas';
    protected $description = 'Genera alertas automáticas por vencimientos, avance bajo y falta de evidencias';

    public function handle(AlertaService $service): int
    {
        $this->info('Generando alertas automáticas...');
        $generadas = $service->generarAutomaticas();
        $this->info("✓ {$generadas} alerta(s) generada(s).");
        return Command::SUCCESS;
    }
}
