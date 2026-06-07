<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\UnidadOrganica;
use App\Models\HistorialRanking;
use App\Models\ConfiguracionInstitucional;

class RankingUnidadesController extends Controller
{
    public function index()
    {
        $config          = ConfiguracionInstitucional::first();
        $umbral_verde    = $config->umbral_verde    ?? 75;
        $umbral_amarillo = $config->umbral_amarillo ?? 50;
        $anio = now()->year;
        $mes  = now()->month;

        $unidades = UnidadOrganica::withCount([
            'actividades',
            'actividades as completadas_count' => fn($q) => $q->where('estado', 'completada'),
            'actividades as vencidas_count'    => fn($q) => $q->whereNotIn('estado', ['completada', 'observado'])
                                                               ->whereDate('fecha_limite', '<', now()),
        ])->where('activo', true)->get()
          ->map(function ($u) use ($umbral_verde, $umbral_amarillo) {
              $u->porcentaje = $u->actividades_count > 0
                  ? round(($u->completadas_count / $u->actividades_count) * 100) : 0;
              $u->color    = $u->porcentaje >= $umbral_verde    ? 'success'
                           : ($u->porcentaje >= $umbral_amarillo ? 'warning' : 'danger');
              $u->semaforo = $u->porcentaje >= $umbral_verde    ? 'Cumplido'
                           : ($u->porcentaje >= $umbral_amarillo ? 'En proceso' : 'En riesgo');
              return $u;
          })->sortByDesc('porcentaje')->values();

        // Obtener posiciones anteriores del historial de BD
        $historialMesAnterior = HistorialRanking::where('anio', $anio)
            ->where('mes', $mes > 1 ? $mes - 1 : 12)
            ->pluck('posicion', 'unidad_organica_id');

        $unidades = $unidades->map(function ($u, $i) use (&$historialMesAnterior) {
            $posActual   = $i + 1;
            $posAnterior = $historialMesAnterior->get($u->id, $posActual);
            $u->posicion_actual   = $posActual;
            $u->posicion_anterior = $posAnterior;
            $u->variacion         = $posAnterior - $posActual; // positivo = subió
            return $u;
        });

        // Guardar snapshot mensual si aún no existe
        $yaGuardado = HistorialRanking::where('anio', $anio)->where('mes', $mes)->exists();
        if (!$yaGuardado) {
            foreach ($unidades as $u) {
                HistorialRanking::create([
                    'unidad_organica_id' => $u->id,
                    'posicion'           => $u->posicion_actual,
                    'posicion_anterior'  => $u->posicion_anterior,
                    'porcentaje'         => $u->porcentaje,
                    'anio'               => $anio,
                    'mes'                => $mes,
                ]);
            }
        }

        // Resumen para el sidebar
        $resumen = [
            'cumplieron' => $unidades->where('color', 'success')->count(),
            'en_riesgo'  => $unidades->where('color', 'danger')->count(),
            'criticas'   => $unidades->where('porcentaje', 0)->count(),
        ];

        // Datos para la gráfica de barras
        $chart_labels = $unidades->pluck('sigla')->toJson();
        $chart_data   = $unidades->pluck('porcentaje')->toJson();
        $chart_colors = $unidades->map(fn($u) => match($u->color) {
            'success' => '#28c76f',
            'warning' => '#ff9f43',
            default   => '#ea5455',
        })->toJson();

        return view('content.ranking-unidades.index', compact(
            'unidades', 'chart_labels', 'chart_data', 'chart_colors',
            'umbral_verde', 'umbral_amarillo', 'resumen'
        ));
    }
}
