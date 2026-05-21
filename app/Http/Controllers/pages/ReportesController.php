<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Componente;
use App\Models\UnidadOrganica;
use Illuminate\Http\Request;

class ReportesController extends Controller
{
    public function index(Request $request)
    {
        $anio       = $request->get('anio', now()->year);
        $componente = $request->get('componente_id');
        $estado     = $request->get('estado');
        $unidad     = $request->get('unidad_organica_id');

        $query = Actividad::with(['componente', 'unidadOrganica'])
            ->whereYear('created_at', $anio);

        if ($componente) $query->where('componente_id', $componente);
        if ($estado)     $query->where('estado', $estado);
        if ($unidad)     $query->where('unidad_organica_id', $unidad);

        $actividades = $query->orderBy('fecha_limite')->paginate(20)->withQueryString();

        // Resumen por componente
        $resumen = Componente::withCount([
            'actividades as total'       => fn($q) => $q->whereYear('created_at', $anio),
            'actividades as completadas' => fn($q) => $q->whereYear('created_at', $anio)->where('estado', 'completada'),
        ])->get()->map(function ($c) {
            $c->porcentaje = $c->total > 0 ? round(($c->completadas / $c->total) * 100) : 0;
            $c->color = $c->porcentaje >= 75 ? 'success' : ($c->porcentaje >= 50 ? 'warning' : 'danger');
            return $c;
        });

        // Datos para gráfica mensual
        $por_mes = Actividad::selectRaw('MONTH(created_at) as mes, COUNT(*) as total, SUM(estado="completada") as completadas')
            ->whereYear('created_at', $anio)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $componentes = Componente::where('activo', true)->orderBy('numero')->get();
        $unidades    = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
        $anios       = range(now()->year, now()->year - 3);

        return view('content.reportes.index', compact(
            'actividades', 'resumen', 'por_mes',
            'componentes', 'unidades', 'anios',
            'anio', 'componente', 'estado', 'unidad'
        ));
    }
}
