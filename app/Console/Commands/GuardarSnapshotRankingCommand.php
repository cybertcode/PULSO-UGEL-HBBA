<?php

namespace App\Console\Commands;

use App\Jobs\GuardarSnapshotRanking;
use Illuminate\Console\Command;

class GuardarSnapshotRankingCommand extends Command
{
    protected $signature   = 'pulso:snapshot-ranking {--anio= : Año (default: actual)} {--mes= : Mes 1-12 (default: actual)}';
    protected $description = 'Guarda el snapshot mensual del ranking de unidades orgánicas';

    public function handle(): int
    {
        $anio = (int) ($this->option('anio') ?: now()->year);
        $mes  = (int) ($this->option('mes')  ?: now()->month);

        GuardarSnapshotRanking::dispatchSync($anio, $mes);
        $this->info("✓ Snapshot de ranking guardado para {$anio}-{$mes}.");
        return Command::SUCCESS;
    }
}
