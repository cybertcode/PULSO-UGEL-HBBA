<?php

namespace App\Http\Controllers\pages;

use App\Exports\CumplimientoExport;
use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Componente;
use App\Models\UnidadOrganica;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class CumplimientoController extends Controller
{
    public function responsables(Request $request)
    {
        $unidadId    = $request->input('unidad_organica_id');
        $componenteId = $request->input('componente_id');
        $anio        = $request->input('anio', now()->year);

        // Por cada responsable: cuántas actividades tiene, cuántas cumplió, cuántas vencidas, sin evidencia
        $responsables = User::where('estado', 'activo')
            ->whereHas('actividadesResponsable')
            ->with('unidadOrganica')
            ->when($unidadId, fn($q) => $q->where('unidad_organica_id', $unidadId))
            ->get()
            ->map(function (User $user) use ($anio, $componenteId) {
                $base = Actividad::whereHas('responsables', fn($q) => $q->where('users.id', $user->id))
                    ->whereYear('created_at', $anio)
                    ->when($componenteId, fn($q) => $q->where('componente_id', $componenteId));

                $total       = (clone $base)->count();
                $completadas = (clone $base)->where('estado', 'completada')->count();
                $vencidas    = (clone $base)->where('estado', 'vencida')->count();
                $en_proceso  = (clone $base)->whereIn('estado', ['pendiente', 'en_proceso'])->count();
                $observadas  = (clone $base)->where('estado', 'observado')->count();

                // Actividades que deberían tener evidencias (no pendiente) y no tienen ninguna
                $sin_evidencia = (clone $base)
                    ->whereNotIn('estado', ['pendiente'])
                    ->whereDoesntHave('evidencias')
                    ->count();

                // Actividades con evidencias pendientes de validación
                $evidencia_pendiente = (clone $base)
                    ->whereHas('evidencias', fn($q) => $q->where('estado', 'pendiente'))
                    ->count();

                // Promedio de días de retraso en las vencidas
                $diasRetraso = (clone $base)
                    ->where('estado', 'vencida')
                    ->whereNotNull('fecha_limite')
                    ->selectRaw('AVG(DATEDIFF(NOW(), fecha_limite)) as promedio')
                    ->value('promedio');

                $porcentaje = $total > 0 ? round(($completadas / $total) * 100) : 0;

                $semaforo = match(true) {
                    $porcentaje >= 75 => 'success',
                    $porcentaje >= 50 => 'warning',
                    default           => 'danger',
                };

                $user->stat_total             = $total;
                $user->stat_completadas       = $completadas;
                $user->stat_vencidas          = $vencidas;
                $user->stat_en_proceso        = $en_proceso;
                $user->stat_observadas        = $observadas;
                $user->stat_sin_evidencia     = $sin_evidencia;
                $user->stat_ev_pendiente      = $evidencia_pendiente;
                $user->stat_porcentaje        = $porcentaje;
                $user->stat_semaforo          = $semaforo;
                $user->stat_dias_retraso      = $diasRetraso ? round($diasRetraso) : 0;

                return $user;
            })
            ->sortBy('stat_porcentaje')
            ->values();

        $unidades    = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
        $componentes = Componente::where('activo', true)->orderBy('numero')->get();
        $anios       = range(now()->year, now()->year - 3);

        // Totales globales para resumen
        $totales = [
            'responsables'    => $responsables->count(),
            'en_riesgo'       => $responsables->where('stat_semaforo', 'danger')->count(),
            'sin_evidencia'   => $responsables->sum('stat_sin_evidencia'),
            'vencidas_total'  => $responsables->sum('stat_vencidas'),
        ];

        return view('content.cumplimiento.responsables', compact(
            'responsables', 'unidades', 'componentes', 'anios',
            'unidadId', 'componenteId', 'anio', 'totales'
        ));
    }

    public function sinEvidencia(Request $request)
    {
        $unidadId     = $request->input('unidad_organica_id');
        $componenteId = $request->input('componente_id');
        $responsableId = $request->input('responsable_id');
        $prioridad    = $request->input('prioridad');
        $anio         = $request->input('anio', now()->year);

        $query = Actividad::with(['componente', 'unidadOrganica', 'responsables'])
            ->whereNotIn('estado', ['pendiente'])   // ya deberían tener evidencias
            ->whereDoesntHave('evidencias')
            ->whereYear('created_at', $anio)
            ->when($unidadId,     fn($q) => $q->where('unidad_organica_id', $unidadId))
            ->when($componenteId, fn($q) => $q->where('componente_id', $componenteId))
            ->when($prioridad,    fn($q) => $q->where('prioridad', $prioridad))
            ->when($responsableId, fn($q) => $q->whereHas('responsables', fn($r) => $r->where('users.id', $responsableId)))
            ->orderByRaw("FIELD(estado,'vencida','observado','en_proceso','completada')")
            ->orderBy('fecha_limite');

        $actividades = $query->paginate(20)->withQueryString();

        // Estadísticas de resumen
        $baseQuery = Actividad::whereNotIn('estado', ['pendiente'])
            ->whereDoesntHave('evidencias')
            ->whereYear('created_at', $anio);

        $stats = [
            'total'     => (clone $baseQuery)->count(),
            'vencidas'  => (clone $baseQuery)->where('estado', 'vencida')->count(),
            'en_proceso'=> (clone $baseQuery)->whereIn('estado', ['en_proceso','observado'])->count(),
            'alta_prio' => (clone $baseQuery)->where('prioridad', 'alta')->count(),
        ];

        $unidades     = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
        $componentes  = Componente::where('activo', true)->orderBy('numero')->get();
        $responsables = User::where('estado', 'activo')->orderBy('name')->get();
        $anios        = range(now()->year, now()->year - 3);

        return view('content.cumplimiento.sin-evidencia', compact(
            'actividades', 'stats', 'unidades', 'componentes', 'responsables', 'anios',
            'unidadId', 'componenteId', 'responsableId', 'prioridad', 'anio'
        ));
    }

    public function panelSci()
    {
        $hoy  = now();
        $anio = $hoy->year;

        // KPIs principales
        $kpis = [
            'total'      => Actividad::whereYear('created_at', $anio)->count(),
            'completadas'=> Actividad::whereYear('created_at', $anio)->where('estado', 'completada')->count(),
            'vencidas'   => Actividad::whereYear('created_at', $anio)->where('estado', 'vencida')->count(),
            'sin_ev'     => Actividad::whereYear('created_at', $anio)
                                ->whereNotIn('estado', ['pendiente'])
                                ->whereDoesntHave('evidencias')->count(),
        ];
        $kpis['porcentaje_global'] = $kpis['total'] > 0
            ? round(($kpis['completadas'] / $kpis['total']) * 100)
            : 0;

        // Actividades con plazo próximo (próximos 15 días, no completadas/vencidas)
        $proximas = Actividad::with(['unidadOrganica', 'responsables'])
            ->whereNotIn('estado', ['completada', 'vencida', 'observado'])
            ->whereDate('fecha_limite', '>=', $hoy->toDateString())
            ->whereDate('fecha_limite', '<=', $hoy->copy()->addDays(15)->toDateString())
            ->orderBy('fecha_limite')
            ->limit(10)
            ->get();

        // Actividades vencidas recientes (últimos 30 días)
        $vencidas = Actividad::with(['unidadOrganica', 'responsables'])
            ->where('estado', 'vencida')
            ->whereDate('fecha_limite', '>=', $hoy->copy()->subDays(30)->toDateString())
            ->orderByDesc('fecha_limite')
            ->limit(10)
            ->get();

        // Top 5 responsables con más incumplimientos (vencidas + sin evidencia)
        $incumplidores = User::where('estado', 'activo')
            ->whereHas('actividadesResponsable')
            ->get()
            ->map(function (User $u) use ($anio) {
                $base = Actividad::whereHas('responsables', fn($q) => $q->where('users.id', $u->id))
                    ->whereYear('created_at', $anio);

                $u->inc_vencidas    = (clone $base)->where('estado', 'vencida')->count();
                $u->inc_sin_ev      = (clone $base)->whereNotIn('estado',['pendiente'])->whereDoesntHave('evidencias')->count();
                $u->inc_total       = $u->inc_vencidas + $u->inc_sin_ev;
                $u->inc_unidad      = $u->unidadOrganica?->sigla ?? '—';
                return $u;
            })
            ->filter(fn($u) => $u->inc_total > 0)
            ->sortByDesc('inc_total')
            ->take(8)
            ->values();

        // Avance por unidad
        $avance_unidades = UnidadOrganica::where('activo', true)
            ->withCount([
                'actividades as total_act'       => fn($q) => $q->whereYear('created_at', $anio),
                'actividades as completadas_act' => fn($q) => $q->whereYear('created_at', $anio)->where('estado','completada'),
                'actividades as vencidas_act'    => fn($q) => $q->whereYear('created_at', $anio)->where('estado','vencida'),
            ])
            ->get()
            ->map(function ($u) {
                $u->porcentaje = $u->total_act > 0
                    ? round(($u->completadas_act / $u->total_act) * 100)
                    : 0;
                $u->semaforo = match(true) {
                    $u->porcentaje >= 75 => 'success',
                    $u->porcentaje >= 50 => 'warning',
                    default              => 'danger',
                };
                return $u;
            })
            ->sortBy('porcentaje')
            ->values();

        // Actividades sin evidencia por unidad (para gráfico de barras)
        $sin_ev_por_unidad = UnidadOrganica::where('activo', true)
            ->withCount([
                'actividades as sin_evidencia' => fn($q) => $q
                    ->whereYear('created_at', $anio)
                    ->whereNotIn('estado', ['pendiente'])
                    ->whereDoesntHave('evidencias'),
            ])
            ->get()
            ->sortByDesc('sin_evidencia')
            ->values();

        return view('content.cumplimiento.panel-sci', compact(
            'kpis', 'proximas', 'vencidas', 'incumplidores',
            'avance_unidades', 'sin_ev_por_unidad', 'hoy'
        ));
    }

    public function exportar(Request $request)
    {
        $anio         = (int) $request->input('anio', now()->year);
        $unidadId     = $request->input('unidad_organica_id') ? (int) $request->input('unidad_organica_id') : null;
        $componenteId = $request->input('componente_id')      ? (int) $request->input('componente_id')      : null;
        $formato      = $request->input('formato', 'excel');

        if ($formato === 'pdf') {
            // Reutiliza la misma lógica de responsables() pero sin paginar
            $responsables = User::where('estado', 'activo')
                ->whereHas('actividadesResponsable')
                ->with('unidadOrganica')
                ->when($unidadId, fn($q) => $q->where('unidad_organica_id', $unidadId))
                ->get()
                ->map(function (User $user) use ($anio, $componenteId) {
                    $base = Actividad::whereHas('responsables', fn($q) => $q->where('users.id', $user->id))
                        ->whereYear('created_at', $anio)
                        ->when($componenteId, fn($q) => $q->where('componente_id', $componenteId));

                    $user->stat_total         = (clone $base)->count();
                    $user->stat_completadas   = (clone $base)->where('estado', 'completada')->count();
                    $user->stat_vencidas      = (clone $base)->where('estado', 'vencida')->count();
                    $user->stat_sin_evidencia = (clone $base)->whereNotIn('estado', ['pendiente'])->whereDoesntHave('evidencias')->count();
                    $user->stat_porcentaje    = $user->stat_total > 0
                        ? round(($user->stat_completadas / $user->stat_total) * 100) : 0;
                    $user->stat_semaforo      = $user->stat_porcentaje >= 75 ? 'success'
                        : ($user->stat_porcentaje >= 50 ? 'warning' : 'danger');
                    return $user;
                })
                ->sortBy('stat_porcentaje')
                ->values();

            $sinEvidencia = Actividad::with(['unidadOrganica', 'responsables'])
                ->whereNotIn('estado', ['pendiente'])
                ->whereDoesntHave('evidencias')
                ->whereYear('created_at', $anio)
                ->when($unidadId,     fn($q) => $q->where('unidad_organica_id', $unidadId))
                ->when($componenteId, fn($q) => $q->where('componente_id', $componenteId))
                ->orderByRaw("FIELD(estado,'vencida','observado','en_proceso','completada')")
                ->orderBy('fecha_limite')
                ->get();

            $totales = [
                'responsables'   => $responsables->count(),
                'en_riesgo'      => $responsables->where('stat_semaforo', 'danger')->count(),
                'sin_evidencia'  => $responsables->sum('stat_sin_evidencia'),
                'vencidas_total' => $responsables->sum('stat_vencidas'),
            ];

            $filtro_unidad     = $unidadId     ? UnidadOrganica::find($unidadId)?->nombre     : null;
            $filtro_componente = $componenteId ? Componente::find($componenteId)?->nombre : null;

            $pdf = Pdf::loadView('exports.cumplimiento-pdf', compact(
                'responsables', 'sinEvidencia', 'totales', 'anio',
                'filtro_unidad', 'filtro_componente'
            ))->setPaper('a4', 'landscape');

            return $pdf->download("PULSO-Cumplimiento-{$anio}.pdf");
        }

        return Excel::download(
            new CumplimientoExport($anio, $unidadId, $componenteId),
            "PULSO-Cumplimiento-{$anio}.xlsx"
        );
    }
}
