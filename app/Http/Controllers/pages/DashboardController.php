<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Componente;
use App\Models\Alerta;
use App\Models\TrabajadorDestacado;
use App\Models\UnidadOrganica;
use App\Models\ConfiguracionInstitucional;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $anio        = now()->year;
        $total       = Actividad::count();
        $completadas = Actividad::where('estado', 'completada')->count();
        $en_proceso  = Actividad::where('estado', 'en_proceso')->count();
        $pendientes  = Actividad::where('estado', 'pendiente')->count();
        $observados  = Actividad::where('estado', 'observado')->count();
        $vencidas    = Actividad::whereNotIn('estado', ['completada', 'observado'])
                         ->whereDate('fecha_limite', '<', now())->count();

        $config = ConfiguracionInstitucional::first();
        $umbral_verde    = $config->umbral_verde    ?? 75;
        $umbral_amarillo = $config->umbral_amarillo ?? 50;

        $totalUnidades = UnidadOrganica::where('activo', true)->count();
        $responsables  = DB::table('actividad_responsables')
                            ->join('actividades', 'actividades.id', '=', 'actividad_responsables.actividad_id')
                            ->whereNotIn('actividades.estado', ['completada'])
                            ->distinct('actividad_responsables.user_id')
                            ->count('actividad_responsables.user_id');

        $reconocimientosTotal = TrabajadorDestacado::where('activo', true)->count();
        $reconocimientosImpl  = TrabajadorDestacado::where('activo', true)
                                    ->where('anio', $anio)->count();

        $stats = [
            'total'                         => $total,
            'completadas'                   => $completadas,
            'en_proceso'                    => $en_proceso,
            'pendientes'                    => $pendientes,
            'observados'                    => $observados,
            'vencidas'                      => $vencidas,
            'alertas'                       => Alerta::where('leida', false)->count(),
            'avance_global'                 => $total > 0 ? round(($completadas / $total) * 100) : 0,
            'reconocimientos'               => $reconocimientosTotal,
            'reconocimientos_implementadas' => $reconocimientosImpl,
            'unidades'                      => $totalUnidades,
            'total_unidades'                => $totalUnidades,
            'responsables_asignados'        => $responsables,
        ];

        // Datos mensuales para el gráfico de línea (12 meses)
        $meses_labels = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        $por_mes_sci  = [];
        $por_mes_integ = [];

        for ($m = 1; $m <= 12; $m++) {
            // SCI: todas las actividades
            $tot = Actividad::whereYear('created_at', $anio)->whereMonth('created_at', $m)->count();
            $com = Actividad::whereYear('created_at', $anio)->whereMonth('created_at', $m)
                    ->where('estado', 'completada')->count();
            $por_mes_sci[] = $tot > 0 ? round(($com / $tot) * 100) : 0;

            // Modelo de Integridad: solo componentes tipo integridad o ambos
            $totI = Actividad::whereYear('created_at', $anio)->whereMonth('created_at', $m)
                ->whereHas('componente', fn($q) => $q->whereIn('tipo', ['integridad', 'ambos']))->count();
            $comI = Actividad::whereYear('created_at', $anio)->whereMonth('created_at', $m)
                ->where('estado', 'completada')
                ->whereHas('componente', fn($q) => $q->whereIn('tipo', ['integridad', 'ambos']))->count();
            $por_mes_integ[] = $totI > 0 ? round(($comI / $totI) * 100) : 0;
        }

        // Datos para gráfico comparativo por áreas (unidades orgánicas)
        $areas_ranking = UnidadOrganica::withCount([
            'actividades',
            'actividades as completadas_count' => fn($q) => $q->where('estado', 'completada'),
        ])->where('activo', true)->get()
          ->map(function ($u) use ($umbral_verde, $umbral_amarillo) {
              $u->porcentaje = $u->actividades_count > 0
                  ? round(($u->completadas_count / $u->actividades_count) * 100) : 0;
              $u->color = $u->porcentaje >= $umbral_verde ? '#28c76f'
                        : ($u->porcentaje >= $umbral_amarillo ? '#ff9f43' : '#ea5455');
              return $u;
          })->sortByDesc('porcentaje')->values()->take(8);

        $componentes = Componente::withCount([
            'actividades',
            'actividades as completadas_count' => fn($q) => $q->where('estado', 'completada'),
            'actividades as en_proceso_count'  => fn($q) => $q->where('estado', 'en_proceso'),
            'actividades as vencidas_count'    => fn($q) => $q->whereNotIn('estado', ['completada','observado'])
                ->whereDate('fecha_limite', '<', now()),
        ])->get()->map(function ($c) use ($umbral_verde, $umbral_amarillo) {
            $c->porcentaje = $c->actividades_count > 0
                ? round(($c->completadas_count / $c->actividades_count) * 100) : 0;
            $c->color    = $c->porcentaje >= $umbral_verde ? 'success' : ($c->porcentaje >= $umbral_amarillo ? 'warning' : 'danger');
            $c->semaforo = $c->color;
            return $c;
        });

        $alertas_recientes = Alerta::with(['actividad.responsables', 'unidadOrganica'])
            ->where('leida', false)
            ->orderByRaw("FIELD(prioridad,'alta','media','baja')")
            ->limit(5)->get();

        $actividades_proximas = Actividad::with('componente', 'responsables')
            ->whereNotIn('estado', ['completada', 'observado'])
            ->whereDate('fecha_limite', '>=', now())
            ->orderBy('fecha_limite')
            ->limit(6)->get();

        return view('content.dashboard.index', compact(
            'stats', 'componentes', 'alertas_recientes', 'actividades_proximas',
            'meses_labels', 'por_mes_sci', 'por_mes_integ',
            'areas_ranking'
        ));
    }
}
