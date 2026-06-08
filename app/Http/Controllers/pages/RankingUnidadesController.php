<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionInstitucional;
use App\Models\HistorialRanking;
use App\Models\UnidadOrganica;
use App\Support\SemaforoHelper;

class RankingUnidadesController extends Controller
{
    public function index()
    {
        $config = ConfiguracionInstitucional::cached();
        [$umbral_verde, $umbral_amarillo] = SemaforoHelper::umbrales($config);
        $anio = now()->year;
        $mes  = now()->month;

        $unidades = UnidadOrganica::withCount([
            'actividades',
            'actividades as completadas_count' => fn($q) => $q->where('estado', 'completada'),
            'actividades as vencidas_count'    => fn($q) => $q->whereNotIn('estado', ['completada', 'observado'])
                                                               ->whereDate('fecha_limite', '<', now()),
        ])->where('activo', true)->get()
          ->map(function ($u) use ($config) {
              SemaforoHelper::decorar($u, 'actividades_count', 'completadas_count', $config, 'Cumplido', 'En proceso', 'En riesgo');
              return $u;
          })->sortByDesc('porcentaje')->values();

        $historialMesAnterior = HistorialRanking::where('anio', $anio)
            ->where('mes', $mes > 1 ? $mes - 1 : 12)
            ->pluck('posicion', 'unidad_organica_id');

        $unidades = $unidades->map(function ($u, $i) use ($historialMesAnterior) {
            $posActual   = $i + 1;
            $posAnterior = $historialMesAnterior->get($u->id, $posActual);
            $u->posicion_actual   = $posActual;
            $u->posicion_anterior = $posAnterior;
            $u->variacion         = $posAnterior - $posActual;
            return $u;
        });

        $resumen = [
            'cumplieron' => $unidades->where('color', 'success')->count(),
            'en_riesgo'  => $unidades->where('color', 'danger')->count(),
            'criticas'   => $unidades->where('porcentaje', 0)->count(),
        ];

        $chart_labels = $unidades->pluck('sigla')->toJson();
        $chart_data   = $unidades->pluck('porcentaje')->toJson();
        $chart_colors = $unidades->map(fn($u) => SemaforoHelper::colorHex($u->porcentaje, $umbral_verde, $umbral_amarillo))->toJson();

        return view('content.ranking-unidades.index', compact(
            'unidades', 'chart_labels', 'chart_data', 'chart_colors',
            'umbral_verde', 'umbral_amarillo', 'resumen'
        ));
    }
}
