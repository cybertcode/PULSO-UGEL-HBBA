<?php

namespace App\Jobs;

use App\Models\HistorialRanking;
use App\Models\UnidadOrganica;
use App\Support\SemaforoHelper;
use App\Models\ConfiguracionInstitucional;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GuardarSnapshotRanking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $anio,
        private int $mes,
    ) {}

    public function handle(): void
    {
        $yaGuardado = HistorialRanking::where('anio', $this->anio)->where('mes', $this->mes)->exists();
        if ($yaGuardado) return;

        $config = ConfiguracionInstitucional::cached();

        $unidades = UnidadOrganica::withCount([
            'actividades',
            'actividades as completadas_count' => fn($q) => $q->where('estado', 'completada'),
        ])->where('activo', true)->get()
          ->map(function ($u) use ($config) {
              SemaforoHelper::decorar($u, 'actividades_count', 'completadas_count', $config);
              return $u;
          })->sortByDesc('porcentaje')->values();

        $historialAnterior = HistorialRanking::where('anio', $this->anio)
            ->where('mes', $this->mes > 1 ? $this->mes - 1 : 12)
            ->pluck('posicion', 'unidad_organica_id');

        foreach ($unidades as $i => $u) {
            $posActual = $i + 1;
            HistorialRanking::create([
                'unidad_organica_id' => $u->id,
                'posicion'           => $posActual,
                'posicion_anterior'  => $historialAnterior->get($u->id, $posActual),
                'porcentaje'         => $u->porcentaje,
                'anio'               => $this->anio,
                'mes'                => $this->mes,
            ]);
        }
    }
}
