<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionInstitucional;
use App\Models\HistorialRanking;
use App\Models\UnidadOrganica;
use App\Models\User;
use App\Support\SemaforoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RankingUnidadesController extends Controller
{
    public function index()
    {
        return view('content.ranking-unidades.index');
    }

    // ── Ranking por unidades orgánicas ─────────────────────────────────────
    public function data(Request $request)
    {
        $modulo = $request->query('modulo', 'ambos');
        $config = ConfiguracionInstitucional::cached();
        [$umbral_verde, $umbral_amarillo] = SemaforoHelper::umbrales($config);
        $anio = now()->year;
        $mes  = now()->month;

        $unidades = UnidadOrganica::withCount([
            'actividades as actividades_count' => fn($q) => $this->filtrarModulo($q, $modulo),
            'actividades as completadas_count' => fn($q) => $this->filtrarModulo($q->where('estado', 'completada'), $modulo),
            'actividades as vencidas_count'    => fn($q) => $this->filtrarModulo(
                $q->whereNotIn('estado', ['completada', 'observado'])->whereDate('fecha_limite', '<', now()),
                $modulo
            ),
        ])->where('activo', true)->get()
          ->map(function ($u) use ($config) {
              SemaforoHelper::decorar($u, 'actividades_count', 'completadas_count', $config, 'Cumplido', 'En proceso', 'En riesgo');
              return $u;
          })->sortByDesc('porcentaje')->values();

        $historialMesAnterior = HistorialRanking::where('anio', $anio)
            ->where('mes', $mes > 1 ? $mes - 1 : 12)
            ->where('modulo', $modulo)
            ->pluck('posicion', 'unidad_organica_id');

        $unidades = $unidades->map(function ($u, $i) use ($historialMesAnterior) {
            $posActual   = $i + 1;
            $posAnterior = $historialMesAnterior->get($u->id, $posActual);
            $u->posicion_actual   = $posActual;
            $u->posicion_anterior = $posAnterior;
            $u->variacion         = $posAnterior - $posActual;
            return $u;
        });

        $colorHex = ['success' => '#28c76f', 'warning' => '#ff9f43', 'danger' => '#ea5455'];

        return response()->json([
            'timestamp'       => now()->toISOString(),
            'modulo'          => $modulo,
            'umbral_verde'    => $umbral_verde,
            'umbral_amarillo' => $umbral_amarillo,
            'resumen' => [
                'cumplieron' => $unidades->where('color', 'success')->count(),
                'en_riesgo'  => $unidades->where('color', 'warning')->count(),
                'criticas'   => $unidades->where('color', 'danger')->count(),
                'promedio'   => (int) round($unidades->avg('porcentaje') ?? 0),
                'maximo'     => $unidades->max('porcentaje') ?? 0,
                'minimo'     => $unidades->min('porcentaje') ?? 0,
            ],
            'chart' => [
                'labels' => $unidades->pluck('sigla')->values(),
                'data'   => $unidades->pluck('porcentaje')->values(),
                'colors' => $unidades->map(fn($u) => SemaforoHelper::colorHex($u->porcentaje, $umbral_verde, $umbral_amarillo))->values(),
            ],
            'unidades' => $unidades->map(fn($u) => [
                'id'               => $u->id,
                'sigla'            => $u->sigla,
                'nombre'           => $u->nombre,
                'porcentaje'       => $u->porcentaje,
                'color'            => $u->color,
                'color_hex'        => $colorHex[$u->color] ?? '#ea5455',
                'semaforo'         => $u->semaforo,
                'actividades_count'=> $u->actividades_count,
                'completadas_count'=> $u->completadas_count,
                'vencidas_count'   => $u->vencidas_count,
                'posicion_actual'  => $u->posicion_actual,
                'posicion_anterior'=> $u->posicion_anterior,
                'variacion'        => $u->variacion,
            ])->values(),
        ]);
    }

    // ── Ranking por usuarios ───────────────────────────────────────────────
    public function dataUsuarios(Request $request)
    {
        $modulo = $request->query('modulo', 'ambos');
        $config = ConfiguracionInstitucional::cached();
        [$umbral_verde, $umbral_amarillo] = SemaforoHelper::umbrales($config);

        $usuarios = User::with(['cargo', 'unidadOrganica'])
            ->where('estado', 'activo')
            ->whereHas('actividadesResponsable', function ($q) use ($modulo) {
                $this->filtrarModulo($q, $modulo);
            })
            ->withCount([
                'actividadesResponsable as actividades_count' => fn($q) => $this->filtrarModulo($q, $modulo),
                'actividadesResponsable as completadas_count' => fn($q) => $this->filtrarModulo($q->where('actividades.estado', 'completada'), $modulo),
                'actividadesResponsable as vencidas_count'    => fn($q) => $this->filtrarModulo(
                    $q->whereNotIn('actividades.estado', ['completada', 'observado'])
                      ->whereDate('actividades.fecha_limite', '<', now()),
                    $modulo
                ),
                'actividadesResponsable as en_proceso_count'  => fn($q) => $this->filtrarModulo(
                    $q->whereIn('actividades.estado', ['en_proceso', 'pendiente']),
                    $modulo
                ),
            ])
            ->get()
            ->map(function ($u) {
                $total       = (int) $u->actividades_count;
                $completadas = (int) $u->completadas_count;
                $u->porcentaje = $total > 0 ? (int) round(($completadas / $total) * 100) : 0;
                return $u;
            })
            ->sortByDesc('porcentaje')
            ->values();

        $colorHex = ['success' => '#28c76f', 'warning' => '#ff9f43', 'danger' => '#ea5455'];

        $usuarios = $usuarios->map(function ($u, $i) use ($umbral_verde, $umbral_amarillo, $colorHex) {
            $color = SemaforoHelper::color($u->porcentaje, $umbral_verde, $umbral_amarillo);
            $u->posicion = $i + 1;
            $u->color    = $color;
            $u->color_hex = $colorHex[$color] ?? '#ea5455';
            $u->semaforo  = SemaforoHelper::label($u->porcentaje, $umbral_verde, $umbral_amarillo, 'Cumplido', 'En proceso', 'En riesgo');
            return $u;
        });

        return response()->json([
            'timestamp'       => now()->toISOString(),
            'modulo'          => $modulo,
            'umbral_verde'    => $umbral_verde,
            'umbral_amarillo' => $umbral_amarillo,
            'resumen' => [
                'cumplieron' => $usuarios->where('color', 'success')->count(),
                'en_riesgo'  => $usuarios->where('color', 'warning')->count(),
                'criticas'   => $usuarios->where('color', 'danger')->count(),
                'promedio'   => (int) round($usuarios->avg('porcentaje') ?? 0),
                'maximo'     => $usuarios->max('porcentaje') ?? 0,
                'minimo'     => $usuarios->min('porcentaje') ?? 0,
            ],
            'chart' => [
                'labels' => $usuarios->map(fn($u) => explode(' ', $u->name)[0])->values(),
                'data'   => $usuarios->pluck('porcentaje')->values(),
                'colors' => $usuarios->pluck('color_hex')->values(),
            ],
            'usuarios' => $usuarios->map(fn($u) => [
                'id'               => $u->id,
                'name'             => $u->name,
                'inicial'          => strtoupper(substr($u->name, 0, 1)),
                'cargo'            => $u->cargo?->nombre,
                'unidad'           => $u->unidadOrganica?->sigla,
                'porcentaje'       => $u->porcentaje,
                'color'            => $u->color,
                'color_hex'        => $u->color_hex,
                'semaforo'         => $u->semaforo,
                'actividades_count'=> $u->actividades_count,
                'completadas_count'=> $u->completadas_count,
                'vencidas_count'   => $u->vencidas_count,
                'en_proceso_count' => $u->en_proceso_count,
                'posicion'         => $u->posicion,
            ])->values(),
        ]);
    }

    private function filtrarModulo($query, string $modulo)
    {
        if ($modulo !== 'ambos') {
            $query->where('modulo', $modulo);
        }
        return $query;
    }
}
