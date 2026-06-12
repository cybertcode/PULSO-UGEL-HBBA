<?php

namespace App\Http\Controllers\pages;

use App\Exports\ActividadesExport;
use App\Http\Controllers\Controller;
use App\Models\Actividad;
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
        $anio     = (int) $request->input('anio', now()->year);
        $modulo   = $request->input('modulo');
        $estado   = $request->input('estado');
        $unidad   = $request->input('unidad_organica_id') ? (int) $request->input('unidad_organica_id') : null;
        $buscar   = $request->input('buscar');
        $prioridad = $request->input('prioridad');
        $fecha_desde = $request->input('fecha_desde');
        $fecha_hasta = $request->input('fecha_hasta');
        $avance_min  = $request->input('avance_min', 0);
        $avance_max  = $request->input('avance_max', 100);
        $tab         = $request->input('tab', 'todos'); // todos | sci | integridad

        $query = Actividad::with(['sciPregunta.componente', 'integridadPregunta.componente', 'unidadOrganica', 'responsables'])
            ->whereYear('fecha_limite', $anio);

        if ($modulo)      $query->where('modulo', $modulo);
        if ($estado)      $query->where('estado', $estado);
        if ($unidad)      $query->where('unidad_organica_id', $unidad);
        if ($prioridad)   $query->where('prioridad', $prioridad);
        if ($buscar)      $query->where(fn($q) => $q->where('nombre', 'like', "%{$buscar}%")->orWhere('codigo', 'like', "%{$buscar}%"));
        if ($fecha_desde) $query->where('fecha_limite', '>=', $fecha_desde);
        if ($fecha_hasta) $query->where('fecha_limite', '<=', $fecha_hasta);
        if ((int)$avance_min > 0)   $query->where('avance', '>=', (int)$avance_min);
        if ((int)$avance_max < 100) $query->where('avance', '<=', (int)$avance_max);

        $actividades = $query->orderBy('fecha_limite')->paginate(25)->withQueryString();

        // Stats globales con los mismos filtros (sin paginar)
        $statsQuery = Actividad::whereYear('fecha_limite', $anio);
        if ($modulo)      $statsQuery->where('modulo', $modulo);
        if ($unidad)      $statsQuery->where('unidad_organica_id', $unidad);
        if ($prioridad)   $statsQuery->where('prioridad', $prioridad);
        if ($buscar)      $statsQuery->where(fn($q) => $q->where('nombre', 'like', "%{$buscar}%")->orWhere('codigo', 'like', "%{$buscar}%"));
        if ($fecha_desde) $statsQuery->where('fecha_limite', '>=', $fecha_desde);
        if ($fecha_hasta) $statsQuery->where('fecha_limite', '<=', $fecha_hasta);

        $allIds      = (clone $statsQuery)->pluck('id');
        $total       = $allIds->count();
        $completadas = (clone $statsQuery)->where('estado', 'completada')->count();
        $en_proceso  = (clone $statsQuery)->where('estado', 'en_proceso')->count();
        $pendientes  = (clone $statsQuery)->where('estado', 'pendiente')->count();
        $vencidas    = (clone $statsQuery)->where('estado', 'vencida')->count();
        $porcentaje  = $total > 0 ? round($completadas / $total * 100) : 0;
        $sin_ev      = (clone $statsQuery)->where('estado', '!=', 'completada')
                        ->whereDoesntHave('evidencias')->count();

        $stats = compact('total', 'completadas', 'en_proceso', 'pendientes', 'vencidas', 'porcentaje', 'sin_ev');

        // Resumen por módulo
        $config  = ConfiguracionInstitucional::cached();
        $resumen = collect(['sci', 'integridad'])->map(function ($mod) use ($anio, $config, $unidad) {
            $q = Actividad::where('modulo', $mod)->whereYear('fecha_limite', $anio);
            if ($unidad) $q->where('unidad_organica_id', $unidad);
            $tot  = $q->count();
            $comp = (clone $q)->where('estado', 'completada')->count();
            $obj  = (object) [
                'nombre'     => $mod === 'sci' ? 'Control Interno (SCI)' : 'Modelo de Integridad',
                'total'      => $tot,
                'completadas' => $comp,
            ];
            return SemaforoHelper::decorar($obj, 'total', 'completadas', $config);
        });

        // Datos por mes para gráfica
        $por_mes = Actividad::selectRaw('MONTH(fecha_limite) as mes, COUNT(*) as total, SUM(estado="completada") as completadas')
            ->whereYear('fecha_limite', $anio)
            ->when($modulo,  fn($q) => $q->where('modulo', $modulo))
            ->when($unidad,  fn($q) => $q->where('unidad_organica_id', $unidad))
            ->groupBy('mes')->orderBy('mes')->get();

        // Avance por unidad orgánica
        $por_unidad = Actividad::selectRaw('unidad_organica_id, COUNT(*) as total, SUM(estado="completada") as completadas')
            ->whereYear('fecha_limite', $anio)
            ->when($modulo, fn($q) => $q->where('modulo', $modulo))
            ->whereNotNull('unidad_organica_id')
            ->groupBy('unidad_organica_id')
            ->with('unidadOrganica')
            ->get()
            ->map(fn($r) => [
                'nombre'     => $r->unidadOrganica?->sigla ?? '—',
                'total'      => (int) $r->total,
                'completadas'=> (int) $r->completadas,
                'porcentaje' => $r->total > 0 ? round($r->completadas / $r->total * 100) : 0,
            ])
            ->sortByDesc('porcentaje')->values();

        // Avance por componente SCI
        $por_componente_sci = Actividad::selectRaw('sci_pregunta_id, COUNT(*) as total, SUM(estado="completada") as completadas')
            ->whereYear('fecha_limite', $anio)
            ->where('modulo', 'sci')
            ->whereNotNull('sci_pregunta_id')
            ->when($unidad, fn($q) => $q->where('unidad_organica_id', $unidad))
            ->groupBy('sci_pregunta_id')
            ->with('sciPregunta.componente')
            ->get()
            ->groupBy(fn($r) => $r->sciPregunta?->componente?->nombre ?? 'Sin componente')
            ->map(fn($rows, $nombre) => [
                'nombre'     => $nombre,
                'total'      => $rows->sum('total'),
                'completadas'=> $rows->sum('completadas'),
                'porcentaje' => $rows->sum('total') > 0 ? round($rows->sum('completadas') / $rows->sum('total') * 100) : 0,
            ])
            ->sortByDesc('porcentaje')->values();

        // Avance por componente Integridad
        $por_componente_int = Actividad::selectRaw('integridad_pregunta_id, COUNT(*) as total, SUM(estado="completada") as completadas')
            ->whereYear('fecha_limite', $anio)
            ->where('modulo', 'integridad')
            ->whereNotNull('integridad_pregunta_id')
            ->when($unidad, fn($q) => $q->where('unidad_organica_id', $unidad))
            ->groupBy('integridad_pregunta_id')
            ->with('integridadPregunta.componente')
            ->get()
            ->groupBy(fn($r) => $r->integridadPregunta?->componente?->nombre ?? 'Sin componente')
            ->map(fn($rows, $nombre) => [
                'nombre'     => $nombre,
                'total'      => $rows->sum('total'),
                'completadas'=> $rows->sum('completadas'),
                'porcentaje' => $rows->sum('total') > 0 ? round($rows->sum('completadas') / $rows->sum('total') * 100) : 0,
            ])
            ->sortByDesc('porcentaje')->values();

        $unidades = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
        $anios    = range(now()->year + 1, now()->year - 3);

        if ($request->ajax()) {
            return response()->json([
                'html'               => view('content.reportes._tabla', compact('actividades'))->render(),
                'pages'              => $actividades->hasPages() ? (string) $actividades->links() : '',
                'stats'              => $stats,
                'resumen'            => $resumen->values(),
                'por_mes'            => $por_mes,
                'por_unidad'         => $por_unidad,
                'por_componente_sci' => $por_componente_sci,
                'por_componente_int' => $por_componente_int,
                'total'              => $actividades->total(),
                'from'               => $actividades->firstItem() ?? 0,
                'to'                 => $actividades->lastItem() ?? 0,
            ]);
        }

        return view('content.reportes.index', compact(
            'actividades', 'stats', 'resumen', 'por_mes', 'por_unidad',
            'por_componente_sci', 'por_componente_int',
            'unidades', 'anios',
            'anio', 'modulo', 'estado', 'unidad', 'buscar',
            'prioridad', 'fecha_desde', 'fecha_hasta', 'avance_min', 'avance_max'
        ));
    }

    public function exportar(Request $request)
    {
        $anio     = (int) $request->input('anio', now()->year);
        $modulo   = $request->input('modulo');
        $estado   = $request->input('estado');
        $unidadId = $request->input('unidad_organica_id') ? (int) $request->input('unidad_organica_id') : null;
        $formato  = $request->input('formato', 'excel');

        if ($formato === 'pdf') {
            $actividades = Actividad::with(['sciPregunta', 'integridadPregunta', 'unidadOrganica', 'responsables'])
                ->whereYear('fecha_limite', $anio)
                ->when($modulo,   fn($q) => $q->where('modulo', $modulo))
                ->when($estado,   fn($q) => $q->where('estado', $estado))
                ->when($unidadId, fn($q) => $q->where('unidad_organica_id', $unidadId))
                ->orderBy('fecha_limite')->get();

            $stats = [
                'total'       => $actividades->count(),
                'completadas' => $actividades->where('estado', 'completada')->count(),
                'pendientes'  => $actividades->whereIn('estado', ['pendiente', 'en_proceso'])->count(),
                'observadas'  => $actividades->where('estado', 'observado')->count(),
            ];

            $filtro_modulo = $modulo ? ($modulo === 'sci' ? 'Control Interno (SCI)' : 'Modelo de Integridad') : null;
            $filtro_unidad = $unidadId ? UnidadOrganica::find($unidadId)?->nombre : null;
            $filtro_estado = $estado;

            $pdf = Pdf::loadView('exports.reporte-pdf', compact(
                'actividades', 'stats', 'anio',
                'filtro_modulo', 'filtro_estado', 'filtro_unidad'
            ))->setPaper('a4', 'landscape');

            return $pdf->download("PULSO-UGEL-Reporte-{$anio}.pdf");
        }

        return Excel::download(
            new ActividadesExport($anio, $modulo, $estado, $unidadId),
            "PULSO-UGEL-Reporte-{$anio}.xlsx"
        );
    }
}
