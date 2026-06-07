<?php

namespace App\Console\Commands;

use App\Http\Controllers\pages\AlertasController;
use Illuminate\Console\Command;

class GenerarAlertasAutomaticas extends Command
{
    protected $signature   = 'pulso:alertas';
    protected $description = 'Genera alertas automáticas por vencimientos, avance bajo y falta de evidencias';

    public function handle(): int
    {
        $this->info('Generando alertas automáticas...');
        $generadas = AlertasController::generarAlertasAutomaticas();
        $this->info("✓ {$generadas} alerta(s) generada(s).");
        return Command::SUCCESS;
    }
}
