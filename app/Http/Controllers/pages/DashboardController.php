<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Componente;
use App\Models\Alerta;
use App\Models\Reconocimiento;
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
        $vencidas    = Actividad::where('estado', '!=', 'completada')
                         ->where('estado', '!=', 'cancelada')
                         ->whereDate('fecha_limite', '<', now())->count();

        $config = ConfiguracionInstitucional::first();
        $umbral_verde    = $config->umbral_verde    ?? 75;
        $umbral_amarillo = $config->umbral_amarillo ?? 50;

        $totalUnidades = UnidadOrganica::count();
        $reconocimientosTotal = Reconocimiento::count();

        $stats = [
            'total'                      => $total,
            'completadas'                => $completadas,
            'en_proceso'                 => $en_proceso,
            'pendientes'                 => $pendientes,
            'vencidas'                   => $vencidas,
            'alertas'                    => Alerta::where('leida', false)->count(),
            'avance_global'              => $total > 0 ? round(($completadas / $total) * 100) : 0,
            'reconocimientos'            => $reconocimientosTotal,
            'reconocimientos_implementadas' => 0,
            'unidades'                   => $totalUnidades,
            'total_unidades'             => $totalUnidades,
        ];

        // Datos mensuales para el gráfico de línea (12 meses del año actual)
        $meses_labels = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        $por_mes_sci  = [];
        $por_mes_comp = [];

        for ($m = 1; $m <= 12; $m++) {
            $tot = Actividad::whereYear('created_at', $anio)->whereMonth('created_at', $m)->count();
            $com = Actividad::whereYear('created_at', $anio)->whereMonth('created_at', $m)
                    ->where('estado', 'completada')->count();
            $por_mes_sci[]  = $tot  > 0 ? round(($com / $tot) * 100) : 0;
            // Para componentes de integridad: mismas actividades pero filtradas por componente
            $por_mes_comp[] = $tot > 0 ? round(($com / $tot) * 100) : 0;
        }

        $componentes = Componente::withCount([
            'actividades',
            'actividades as completadas_count' => fn($q) => $q->where('estado', 'completada'),
            'actividades as en_proceso_count'  => fn($q) => $q->where('estado', 'en_proceso'),
            'actividades as vencidas_count'    => fn($q) => $q->where('estado', '!=', 'completada')
                ->where('estado', '!=', 'cancelada')->whereDate('fecha_limite', '<', now()),
        ])->get()->map(function ($c) use ($umbral_verde, $umbral_amarillo) {
            $c->porcentaje = $c->actividades_count > 0
                ? round(($c->completadas_count / $c->actividades_count) * 100) : 0;
            $c->color    = $c->porcentaje >= $umbral_verde ? 'success' : ($c->porcentaje >= $umbral_amarillo ? 'warning' : 'danger');
            $c->semaforo = $c->porcentaje >= $umbral_verde ? 'success' : ($c->porcentaje >= $umbral_amarillo ? 'warning' : 'danger');
            return $c;
        });

        $alertas_recientes = Alerta::with('actividad', 'unidadOrganica')
            ->where('leida', false)
            ->orderByRaw("FIELD(prioridad,'alta','media','baja')")
            ->limit(5)->get();

        $actividades_proximas = Actividad::with('componente', 'responsable')
            ->whereNotIn('estado', ['completada', 'cancelada'])
            ->whereDate('fecha_limite', '>=', now())
            ->orderBy('fecha_limite')
            ->limit(6)->get();

        return view('content.dashboard.index', compact(
            'stats', 'componentes', 'alertas_recientes', 'actividades_proximas',
            'meses_labels', 'por_mes_sci', 'por_mes_comp'
        ));
    }
}
