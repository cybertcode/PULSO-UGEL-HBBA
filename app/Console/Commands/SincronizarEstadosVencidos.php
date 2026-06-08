<?php

namespace App\Console\Commands;

use App\Models\Actividad;
use Illuminate\Console\Command;

class SincronizarEstadosVencidos extends Command
{
    protected $signature   = 'pulso:sincronizar-estados';
    protected $description = 'Marca como vencidas las actividades cuya fecha_limite ya pasó y no están completadas/observadas';

    public function handle(): int
    {
        $actualizadas = Actividad::whereNotIn('estado', ['completada', 'observado', 'vencida'])
            ->whereDate('fecha_limite', '<', now())
            ->update(['estado' => 'vencida']);

        $this->info("✓ {$actualizadas} actividad(es) marcada(s) como vencida.");
        return Command::SUCCESS;
    }
}
