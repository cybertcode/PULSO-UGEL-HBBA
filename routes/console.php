<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Generar alertas automáticas todos los días a las 7am
Schedule::command('pulso:alertas')->dailyAt('07:00');

// Marcar actividades vencidas correctamente (cada día a las 00:05)
Schedule::command('pulso:sincronizar-estados')->dailyAt('00:05');

// Snapshot mensual del ranking (último día del mes a las 23:00)
Schedule::command('pulso:snapshot-ranking')->monthlyOn(28, '23:00');
