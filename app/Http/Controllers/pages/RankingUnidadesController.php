<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\UnidadOrganica;
use App\Models\ConfiguracionInstitucional;
use Illuminate\Support\Facades\Cache;

class RankingUnidadesController extends Controller
{
    public function index()
    {
        $config = ConfiguracionInstitucional::first();
        $umbral_verde    = $config->umbral_verde    ?? 75;
        $umbral_amarillo = $config->umbral_amarillo ?? 50;

        $unidades = UnidadOrganica::withCount([
            'actividades',
            'actividades as completadas_count' => fn($q) => $q->where('estado', 'completada'),
            'actividades as vencidas_count'    => fn($q) => $q->where('estado', '!=', 'completada')
                                                               ->where('estado', '!=', 'cancelada')
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

        // Posición anterior (guardada en caché por 24h para mostrar variación)
        $cacheKey = 'ranking_posiciones_anteriores';
        $posicionesAnteriores = Cache::get($cacheKey, []);

        // Asignar posición anterior a cada unidad
        $unidades = $unidades->map(function ($u, $i) use (&$posicionesAnteriores) {
            $posActual   = $i + 1;
            $posAnterior = $posicionesAnteriores[$u->id] ?? $posActual;
            $u->posicion_actual   = $posActual;
            $u->posicion_anterior = $posAnterior;
            $u->variacion         = $posAnterior - $posActual; // positivo = subió
            return $u;
        });

        // Guardar posiciones actuales para la próxima visita
        $nuevasPosiciones = $unidades->pluck('posicion_actual', 'id')->toArray();
        Cache::put($cacheKey, $nuevasPosiciones, now()->addDay());

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
            'umbral_verde', 'umbral_amarillo'
        ));
    }
}
