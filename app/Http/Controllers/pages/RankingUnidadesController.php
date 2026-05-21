<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\UnidadOrganica;
use App\Models\ConfiguracionInstitucional;
use Illuminate\Http\Request;

class RankingUnidadesController extends Controller
{
    public function index(Request $request)
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
              $u->semaforo = $u->porcentaje >= $umbral_verde    ? 'Verde'
                           : ($u->porcentaje >= $umbral_amarillo ? 'Amarillo' : 'Rojo');
              return $u;
          })->sortByDesc('porcentaje')->values();

        // Datos para la gráfica de barras
        $chart_labels  = $unidades->pluck('sigla')->toJson();
        $chart_data    = $unidades->pluck('porcentaje')->toJson();
        $chart_colors  = $unidades->map(fn($u) => match($u->color) {
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
