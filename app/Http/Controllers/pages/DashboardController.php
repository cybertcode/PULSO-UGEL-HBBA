<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Alerta;
use App\Models\ConfiguracionInstitucional;
use App\Models\SciEje;
use App\Models\SciComponente;
use App\Models\IntegridadEtapa;
use App\Models\TrabajadorDestacado;
use App\Models\UnidadOrganica;
use App\Support\SemaforoHelper;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $anio   = now()->year;
        $config = ConfiguracionInstitucional::cached();
        [$umbral_verde, $umbral_amarillo] = SemaforoHelper::umbrales($config);

        // ── Stats generales ───────────────────────────────────────────────────
        $total       = Actividad::count();
        $completadas = Actividad::where('estado', 'completada')->count();
        $en_proceso  = Actividad::where('estado', 'en_proceso')->count();
        $pendientes  = Actividad::where('estado', 'pendiente')->count();
        $observados  = Actividad::where('estado', 'observado')->count();
        $vencidas    = Actividad::whereNotIn('estado', ['completada', 'observado'])
                         ->whereDate('fecha_limite', '<', now())->count();

        // ── Stats SCI ─────────────────────────────────────────────────────────
        $totalSci       = Actividad::where('modulo', 'sci')->count();
        $completadasSci = Actividad::where('modulo', 'sci')->where('estado', 'completada')->count();
        $avanceSci      = $totalSci > 0 ? round(($completadasSci / $totalSci) * 100) : 0;

        // ── Stats Integridad ──────────────────────────────────────────────────
        $totalInt       = Actividad::where('modulo', 'integridad')->count();
        $completadasInt = Actividad::where('modulo', 'integridad')->where('estado', 'completada')->count();
        $avanceInt      = $totalInt > 0 ? round(($completadasInt / $totalInt) * 100) : 0;

        $totalUnidades = UnidadOrganica::where('activo', true)->count();
        $responsables  = DB::table('actividad_responsables')
                            ->join('actividades', 'actividades.id', '=', 'actividad_responsables.actividad_id')
                            ->whereNotIn('actividades.estado', ['completada'])
                            ->distinct('actividad_responsables.user_id')
                            ->count('actividad_responsables.user_id');

        $reconocimientosTotal = TrabajadorDestacado::where('activo', true)->count();
        $reconocimientosImpl  = TrabajadorDestacado::where('activo', true)->where('anio', $anio)->count();

        $stats = [
            'total'                         => $total,
            'completadas'                   => $completadas,
            'en_proceso'                    => $en_proceso,
            'pendientes'                    => $pendientes,
            'observados'                    => $observados,
            'vencidas'                      => $vencidas,
            'alertas'                       => Alerta::where('leida', false)->count(),
            'avance_global'                 => $total > 0 ? round(($completadas / $total) * 100) : 0,
            'total_sci'                     => $totalSci,
            'completadas_sci'               => $completadasSci,
            'avance_sci'                    => $avanceSci,
            'total_int'                     => $totalInt,
            'completadas_int'               => $completadasInt,
            'avance_int'                    => $avanceInt,
            'reconocimientos'               => $reconocimientosTotal,
            'reconocimientos_implementadas' => $reconocimientosImpl,
            'unidades'                      => $totalUnidades,
            'total_unidades'                => $totalUnidades,
            'responsables_asignados'        => $responsables,
        ];

        // ── Gráfico mensual SCI vs Integridad ─────────────────────────────────
        $meses_labels  = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        $por_mes_sci   = [];
        $por_mes_integ = [];

        for ($m = 1; $m <= 12; $m++) {
            $base = fn($modulo) => Actividad::where('modulo', $modulo)
                ->whereYear('created_at', $anio)->whereMonth('created_at', $m);

            $tot = $base('sci')->count();
            $com = $base('sci')->where('estado', 'completada')->count();
            $por_mes_sci[] = $tot > 0 ? round(($com / $tot) * 100) : 0;

            $totI = $base('integridad')->count();
            $comI = $base('integridad')->where('estado', 'completada')->count();
            $por_mes_integ[] = $totI > 0 ? round(($comI / $totI) * 100) : 0;
        }

        // ── Ranking de unidades ───────────────────────────────────────────────
        $areas_ranking = UnidadOrganica::withCount([
            'actividades',
            'actividades as completadas_count' => fn($q) => $q->where('estado', 'completada'),
        ])->where('activo', true)->get()
          ->map(function ($u) use ($umbral_verde, $umbral_amarillo) {
              $u->porcentaje = $u->actividades_count > 0
                  ? round(($u->completadas_count / $u->actividades_count) * 100) : 0;
              $u->color = SemaforoHelper::colorHex($u->porcentaje, $umbral_verde, $umbral_amarillo);
              return $u;
          })->sortByDesc('porcentaje')->values()->take(8);

        // ── Ejes SCI con avance (reemplaza "componentes PCM") ─────────────────
        $sciEjes = SciEje::where('activo', true)
            ->where('anio', $anio)
            ->with(['componentes' => fn($q) => $q->where('activo', true)->with('preguntas')])
            ->orderBy('orden')
            ->get()
            ->map(function ($eje) use ($config) {
                $pregIds     = $eje->componentes->flatMap(fn($c) => $c->preguntas->pluck('id'));
                $total       = Actividad::where('modulo', 'sci')->whereIn('sci_pregunta_id', $pregIds)->count();
                $completadas = Actividad::where('modulo', 'sci')->whereIn('sci_pregunta_id', $pregIds)
                                ->where('estado', 'completada')->count();
                $eje->actividades_count  = $total;
                $eje->completadas_count  = $completadas;
                SemaforoHelper::decorar($eje, 'actividades_count', 'completadas_count', $config);
                return $eje;
            });

        // ── Alertas recientes ─────────────────────────────────────────────────
        $alertas_recientes = Alerta::with(['actividad.responsables', 'unidadOrganica'])
            ->where('leida', false)
            ->orderByRaw("FIELD(prioridad,'alta','media','baja')")
            ->limit(5)->get();

        // ── Actividades próximas a vencer (ambos módulos) ─────────────────────
        $actividades_proximas = Actividad::with([
                'sciPregunta.componente',
                'integridadPregunta.componente',
                'responsables',
            ])
            ->whereNotIn('estado', ['completada', 'observado'])
            ->whereDate('fecha_limite', '>=', now())
            ->orderBy('fecha_limite')
            ->limit(6)->get();

        return view('content.dashboard.index', compact(
            'stats', 'sciEjes', 'alertas_recientes', 'actividades_proximas',
            'meses_labels', 'por_mes_sci', 'por_mes_integ',
            'areas_ranking'
        ));
    }
}
