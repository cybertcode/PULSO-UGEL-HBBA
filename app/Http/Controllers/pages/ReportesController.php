<?php

namespace App\Http\Controllers\pages;

use App\Exports\ActividadesExport;
use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Componente;
use App\Models\UnidadOrganica;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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

        $resumen = Componente::withCount([
            'actividades as total'       => fn($q) => $q->whereYear('created_at', $anio),
            'actividades as completadas' => fn($q) => $q->whereYear('created_at', $anio)->where('estado', 'completada'),
        ])->get()->map(function ($c) {
            $c->porcentaje = $c->total > 0 ? round(($c->completadas / $c->total) * 100) : 0;
            $c->color = $c->porcentaje >= 75 ? 'success' : ($c->porcentaje >= 50 ? 'warning' : 'danger');
            return $c;
        });

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

    public function exportar(Request $request)
    {
        $anio       = (int) $request->get('anio', now()->year);
        $componenteId = $request->get('componente_id') ? (int) $request->get('componente_id') : null;
        $estado     = $request->get('estado');
        $unidadId   = $request->get('unidad_organica_id') ? (int) $request->get('unidad_organica_id') : null;
        $formato    = $request->get('formato', 'excel');

        if ($formato === 'pdf') {
            $actividades = Actividad::with(['componente', 'unidadOrganica', 'responsable'])
                ->whereYear('created_at', $anio)
                ->when($componenteId, fn($q) => $q->where('componente_id', $componenteId))
                ->when($estado,       fn($q) => $q->where('estado', $estado))
                ->when($unidadId,     fn($q) => $q->where('unidad_organica_id', $unidadId))
                ->orderBy('fecha_limite')
                ->get();

            $stats = [
                'total'       => $actividades->count(),
                'completadas' => $actividades->where('estado', 'completada')->count(),
                'pendientes'  => $actividades->whereIn('estado', ['pendiente', 'en_proceso'])->count(),
                'observadas'  => $actividades->where('estado', 'observado')->count(),
            ];

            $filtro_componente = $componenteId ? Componente::find($componenteId)?->nombre : null;
            $filtro_unidad     = $unidadId     ? UnidadOrganica::find($unidadId)?->nombre : null;
            $filtro_estado     = $estado;

            $pdf = Pdf::loadView('exports.reporte-pdf', compact(
                'actividades', 'stats', 'anio',
                'filtro_componente', 'filtro_estado', 'filtro_unidad'
            ))->setPaper('a4', 'landscape');

            return $pdf->download("PULSO-UGEL-Reporte-{$anio}.pdf");
        }

        return Excel::download(
            new ActividadesExport($anio, $componenteId, $estado, $unidadId),
            "PULSO-UGEL-Reporte-{$anio}.xlsx"
        );
    }
}
