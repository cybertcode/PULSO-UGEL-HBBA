<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Componente;
use App\Models\Alerta;

class DashboardController extends Controller
{
    public function index()
    {
        $total       = Actividad::count();
        $completadas = Actividad::where('estado', 'completada')->count();
        $en_proceso  = Actividad::where('estado', 'en_proceso')->count();
        $vencidas    = Actividad::where('estado', '!=', 'completada')
                         ->where('estado', '!=', 'cancelada')
                         ->whereDate('fecha_limite', '<', now())->count();

        $stats = [
            'total'         => $total,
            'completadas'   => $completadas,
            'en_proceso'    => $en_proceso,
            'vencidas'      => $vencidas,
            'alertas'       => Alerta::where('leida', false)->count(),
            'avance_global' => $total > 0 ? round(($completadas / $total) * 100) : 0,
        ];

        $componentes = Componente::withCount([
            'actividades',
            'actividades as completadas_count' => fn($q) => $q->where('estado', 'completada'),
        ])->get()->map(function ($c) {
            $c->porcentaje = $c->actividades_count > 0
                ? round(($c->completadas_count / $c->actividades_count) * 100) : 0;
            $c->semaforo = $c->porcentaje >= 75 ? 'success' : ($c->porcentaje >= 50 ? 'warning' : 'danger');
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
            ->limit(8)->get();

        return view('content.dashboard.index', compact(
            'stats', 'componentes', 'alertas_recientes', 'actividades_proximas'
        ));
    }
}
