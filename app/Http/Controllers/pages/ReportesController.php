<?php

namespace App\Http\Controllers\pages;

use App\Exports\ActividadesExport;
use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Componente;
use App\Models\ConfiguracionInstitucional;
use App\Models\UnidadOrganica;
use App\Support\SemaforoHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportesController extends Controller
{
    public function index(Request $request)
    {
        $anio       = $request->input('anio', now()->year);
        $componente = $request->input('componente_id');
        $estado     = $request->input('estado');
        $unidad     = $request->input('unidad_organica_id');

        $query = Actividad::with(['componente', 'unidadOrganica'])
            ->whereYear('created_at', $anio);

        if ($componente) $query->where('componente_id', $componente);
        if ($estado)     $query->where('estado', $estado);
        if ($unidad)     $query->where('unidad_organica_id', $unidad);

        $actividades = $query->orderBy('fecha_limite')->paginate(20)->withQueryString();

        $config = ConfiguracionInstitucional::cached();

        $resumen = Componente::withCount([
            'actividades as total'       => fn($q) => $q->whereYear('created_at', $anio),
            'actividades as completadas' => fn($q) => $q->whereYear('created_at', $anio)->where('estado', 'completada'),
        ])->get()->map(fn($c) => SemaforoHelper::decorar($c, 'total', 'completadas', $config));

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
        $anio         = (int) $request->input('anio', now()->year);
        $componenteId = $request->input('componente_id') ? (int) $request->input('componente_id') : null;
        $estado       = $request->input('estado');
        $unidadId     = $request->input('unidad_organica_id') ? (int) $request->input('unidad_organica_id') : null;
        $formato      = $request->input('formato', 'excel');

        if ($formato === 'pdf') {
            $actividades = Actividad::with(['componente', 'unidadOrganica', 'responsables'])
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
