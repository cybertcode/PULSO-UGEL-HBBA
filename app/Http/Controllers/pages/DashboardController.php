<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Alerta;
use App\Models\BuenaPractica;
use App\Models\ConfiguracionInstitucional;
use App\Models\SciEje;
use App\Models\IntegridadEtapa;
use App\Models\TrabajadorDestacado;
use App\Models\UnidadOrganica;
use App\Support\SemaforoHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $anio   = now()->year;
        $config = ConfiguracionInstitucional::cached();
        [$umbral_verde, $umbral_amarillo] = SemaforoHelper::umbrales($config);

        // ── Stats SCI ─────────────────────────────────────────────────────────
        $totalSci       = Actividad::where('modulo', 'sci')->count();
        $completadasSci = Actividad::where('modulo', 'sci')->where('estado', 'completada')->count();
        $vencidasSci    = Actividad::where('modulo', 'sci')
                            ->whereNotIn('estado', ['completada', 'observado'])
                            ->whereDate('fecha_limite', '<', now())->count();
        $avanceSci      = $totalSci > 0 ? round(($completadasSci / $totalSci) * 100) : 0;

        // ── Stats Integridad ──────────────────────────────────────────────────
        $totalInt       = Actividad::where('modulo', 'integridad')->count();
        $completadasInt = Actividad::where('modulo', 'integridad')->where('estado', 'completada')->count();
        $vencidasInt    = Actividad::where('modulo', 'integridad')
                            ->whereNotIn('estado', ['completada', 'observado'])
                            ->whereDate('fecha_limite', '<', now())->count();
        $avanceInt      = $totalInt > 0 ? round(($completadasInt / $totalInt) * 100) : 0;

        // ── Stats generales ───────────────────────────────────────────────────
        $total       = $totalSci + $totalInt;
        $completadas = $completadasSci + $completadasInt;
        $en_proceso  = Actividad::where('estado', 'en_proceso')->count();
        $pendientes  = Actividad::where('estado', 'pendiente')->count();
        $observados  = Actividad::where('estado', 'observado')->count();
        $vencidas    = $vencidasSci + $vencidasInt;

        $totalUnidades = UnidadOrganica::where('activo', true)->count();

        $stats = [
            'total'          => $total,
            'completadas'    => $completadas,
            'en_proceso'     => $en_proceso,
            'pendientes'     => $pendientes,
            'observados'     => $observados,
            'vencidas'       => $vencidas,
            'avance_global'  => $total > 0 ? round(($completadas / $total) * 100) : 0,
            'total_sci'      => $totalSci,
            'completadas_sci'=> $completadasSci,
            'avance_sci'     => $avanceSci,
            'vencidas_sci'   => $vencidasSci,
            'total_int'      => $totalInt,
            'completadas_int'=> $completadasInt,
            'avance_int'     => $avanceInt,
            'vencidas_int'   => $vencidasInt,
            'unidades'       => $totalUnidades,
            'total_unidades' => $totalUnidades,
            'alertas'        => Alerta::where('leida', false)->count(),
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

        // ── Ranking de unidades (ambos módulos) ───────────────────────────────
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

        // ── Ejes SCI con avance ───────────────────────────────────────────────
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
                $eje->actividades_count = $total;
                $eje->completadas_count = $completadas;
                SemaforoHelper::decorar($eje, 'actividades_count', 'completadas_count', $config);
                return $eje;
            });

        // ── Etapas Integridad con avance ──────────────────────────────────────
        $integridadEtapas = IntegridadEtapa::where('activo', true)
            ->where('anio', $anio)
            ->with(['componentes' => fn($q) => $q->where('activo', true)->with('preguntas')])
            ->orderBy('orden')
            ->get()
            ->map(function ($etapa) use ($config) {
                $pregIds     = $etapa->componentes->flatMap(fn($c) => $c->preguntas->pluck('id'));
                $total       = Actividad::where('modulo', 'integridad')->whereIn('integridad_pregunta_id', $pregIds)->count();
                $completadas = Actividad::where('modulo', 'integridad')->whereIn('integridad_pregunta_id', $pregIds)
                                ->where('estado', 'completada')->count();
                $etapa->actividades_count = $total;
                $etapa->completadas_count = $completadas;
                SemaforoHelper::decorar($etapa, 'actividades_count', 'completadas_count', $config);
                return $etapa;
            });

        // ── Alertas recientes (no leídas) ─────────────────────────────────────
        $alertas_recientes = Alerta::with(['actividad', 'unidadOrganica'])
            ->where('leida', false)
            ->orderByRaw("FIELD(prioridad,'alta','media','baja')")
            ->limit(6)->get();

        // Stats alertas por tipo
        $alertas_stats = [
            'vencimiento'     => Alerta::where('leida', false)->where('tipo', 'vencimiento')->count(),
            'avance_bajo'     => Alerta::where('leida', false)->where('tipo', 'avance_bajo')->count(),
            'evidencia_falta' => Alerta::where('leida', false)->where('tipo', 'evidencia_falta')->count(),
            'total'           => Alerta::where('leida', false)->count(),
        ];

        // ── Actividades próximas a vencer (ambos módulos) ─────────────────────
        $actividades_proximas = Actividad::with([
                'sciPregunta.componente',
                'integridadPregunta.componente',
                'responsables',
                'unidadOrganica',
            ])
            ->whereNotIn('estado', ['completada', 'observado'])
            ->whereDate('fecha_limite', '>=', now())
            ->orderBy('fecha_limite')
            ->limit(8)->get();

        // ── Actividades vencidas recientes ────────────────────────────────────
        $actividades_vencidas = Actividad::with(['responsables', 'unidadOrganica'])
            ->whereNotIn('estado', ['completada', 'observado'])
            ->whereDate('fecha_limite', '<', now())
            ->orderByDesc('fecha_limite')
            ->limit(5)->get();

        // ── Buenas Prácticas reales ───────────────────────────────────────────
        $buenas_practicas = BuenaPractica::with(['unidadOrganica', 'responsable'])
            ->whereNotIn('estado', ['suspendida', 'no_elegible'])
            ->orderByDesc('updated_at')
            ->limit(5)->get();

        $bp_stats = [
            'total'          => BuenaPractica::count(),
            'en_concurso'    => BuenaPractica::whereIn('estado', ['elegible','ganador_ugel','participante_externo'])->count(),
            'ganadores'      => BuenaPractica::whereIn('estado', ['ganador_ugel','ganador_externo'])->count(),
            'implementadas'  => BuenaPractica::where('estado', 'en_implementacion')->count(),
        ];

        // ── Usuario actual ────────────────────────────────────────────────────
        $user = Auth::user();
        $mis_actividades_count = Actividad::whereHas('responsables', fn($q) => $q->where('users.id', $user->id))
            ->whereNotIn('estado', ['completada'])->count();

        return view('content.dashboard.index', compact(
            'stats', 'sciEjes', 'integridadEtapas',
            'alertas_recientes', 'alertas_stats',
            'actividades_proximas', 'actividades_vencidas',
            'buenas_practicas', 'bp_stats',
            'meses_labels', 'por_mes_sci', 'por_mes_integ',
            'areas_ranking', 'anio', 'user',
            'mis_actividades_count'
        ));
    }
}
