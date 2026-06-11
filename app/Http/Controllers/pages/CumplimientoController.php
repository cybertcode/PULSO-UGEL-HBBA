<?php

namespace App\Http\Controllers\pages;

use App\Exports\CumplimientoExport;
use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\SciEje;
use App\Models\UnidadOrganica;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class CumplimientoController extends Controller
{
    // ── Panel SCI ─────────────────────────────────────────────────────────────

    public function panelSci()
    {
        $hoy  = now();
        $anio = $hoy->year;

        $kpis = [
            'total'       => Actividad::whereYear('created_at', $anio)->count(),
            'completadas' => Actividad::whereYear('created_at', $anio)->where('estado', 'completada')->count(),
            'vencidas'    => Actividad::whereYear('created_at', $anio)->where('estado', 'vencida')->count(),
            'sin_ev'      => Actividad::whereYear('created_at', $anio)
                                ->whereNotIn('estado', ['pendiente'])
                                ->whereDoesntHave('evidencias')->count(),
        ];
        $kpis['porcentaje_global'] = $kpis['total'] > 0
            ? round(($kpis['completadas'] / $kpis['total']) * 100) : 0;

        $proximas = Actividad::with(['unidadOrganica', 'responsables'])
            ->whereNotIn('estado', ['completada', 'vencida', 'observado'])
            ->whereDate('fecha_limite', '>=', $hoy->toDateString())
            ->whereDate('fecha_limite', '<=', $hoy->copy()->addDays(15)->toDateString())
            ->orderBy('fecha_limite')
            ->limit(10)->get();

        $vencidas = Actividad::with(['unidadOrganica', 'responsables'])
            ->where('estado', 'vencida')
            ->whereDate('fecha_limite', '>=', $hoy->copy()->subDays(30)->toDateString())
            ->orderByDesc('fecha_limite')
            ->limit(10)->get();

        $incumplidores = User::where('estado', 'activo')
            ->whereHas('actividadesResponsable')
            ->with(['unidadOrganica', 'cargo'])
            ->get()
            ->map(function (User $u) use ($anio) {
                $base = Actividad::whereHas('responsables', fn($q) => $q->where('users.id', $u->id))
                    ->whereYear('created_at', $anio);
                $u->inc_vencidas = (clone $base)->where('estado', 'vencida')->count();
                $u->inc_sin_ev   = (clone $base)->whereNotIn('estado', ['pendiente'])->whereDoesntHave('evidencias')->count();
                $u->inc_total    = $u->inc_vencidas + $u->inc_sin_ev;
                $u->inc_unidad   = $u->unidadOrganica?->sigla ?? '—';
                return $u;
            })
            ->filter(fn($u) => $u->inc_total > 0)
            ->sortByDesc('inc_total')->take(8)->values();

        $avance_unidades = UnidadOrganica::where('activo', true)
            ->withCount([
                'actividades as total_act'       => fn($q) => $q->whereYear('created_at', $anio),
                'actividades as completadas_act'  => fn($q) => $q->whereYear('created_at', $anio)->where('estado', 'completada'),
                'actividades as vencidas_act'     => fn($q) => $q->whereYear('created_at', $anio)->where('estado', 'vencida'),
            ])
            ->get()
            ->map(function ($u) {
                $u->porcentaje = $u->total_act > 0
                    ? round(($u->completadas_act / $u->total_act) * 100) : 0;
                $u->semaforo = match(true) {
                    $u->porcentaje >= 75 => 'success',
                    $u->porcentaje >= 50 => 'warning',
                    default              => 'danger',
                };
                return $u;
            })
            ->sortBy('porcentaje')->values();

        $sin_ev_por_unidad = UnidadOrganica::where('activo', true)
            ->withCount(['actividades as sin_evidencia' => fn($q) => $q
                ->whereYear('created_at', $anio)
                ->whereNotIn('estado', ['pendiente'])
                ->whereDoesntHave('evidencias')])
            ->get()->sortByDesc('sin_evidencia')->values();

        return view('content.cumplimiento.panel-sci', compact(
            'kpis', 'proximas', 'vencidas', 'incumplidores',
            'avance_unidades', 'sin_ev_por_unidad', 'hoy'
        ));
    }

    // ── Responsables (vista + datos AJAX) ────────────────────────────────────

    public function responsables(Request $request)
    {
        $unidadId  = $request->input('unidad_organica_id');
        $modulo    = $request->input('modulo');
        $ejeId     = $request->input('eje_id');
        $anio      = $request->input('anio', now()->year);

        $responsables = $this->calcularResponsables($unidadId, $modulo, $ejeId, $anio);

        $totales = [
            'responsables'   => $responsables->count(),
            'en_riesgo'      => $responsables->where('stat_semaforo', 'danger')->count(),
            'sin_evidencia'  => $responsables->sum('stat_sin_evidencia'),
            'vencidas_total' => $responsables->sum('stat_vencidas'),
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'totales'      => $totales,
                'responsables' => $responsables->map(fn($u) => [
                    'id'            => $u->id,
                    'name'          => $u->name,
                    'cargo'         => $u->cargo?->nombre ?? 'Sin cargo',
                    'unidad'        => $u->unidadOrganica?->sigla ?? '—',
                    'inicial'       => strtoupper(substr($u->name, 0, 1)),
                    'total'         => $u->stat_total,
                    'completadas'   => $u->stat_completadas,
                    'vencidas'      => $u->stat_vencidas,
                    'sin_evidencia' => $u->stat_sin_evidencia,
                    'ev_pendiente'  => $u->stat_ev_pendiente,
                    'porcentaje'    => $u->stat_porcentaje,
                    'semaforo'      => $u->stat_semaforo,
                    'dias_retraso'  => $u->stat_dias_retraso,
                ]),
            ]);
        }

        $unidades = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
        $ejes     = SciEje::where('activo', true)->orderBy('anio', 'desc')->orderBy('orden')->get();
        $anios    = range(now()->year, now()->year - 3);

        return view('content.cumplimiento.responsables', compact(
            'responsables', 'unidades', 'ejes', 'anios',
            'unidadId', 'modulo', 'ejeId', 'anio', 'totales'
        ));
    }

    private function calcularResponsables(?string $unidadId, ?string $modulo, ?string $ejeId, int $anio)
    {
        return User::where('estado', 'activo')
            ->whereHas('actividadesResponsable')
            ->with(['unidadOrganica', 'cargo'])
            ->when($unidadId, fn($q) => $q->where('unidad_organica_id', $unidadId))
            ->get()
            ->map(function (User $user) use ($anio, $modulo, $ejeId) {
                $base = Actividad::whereHas('responsables', fn($q) => $q->where('users.id', $user->id))
                    ->whereYear('created_at', $anio)
                    ->when($modulo, fn($q) => $q->where('modulo', $modulo));

                if ($ejeId) {
                    $base->whereHas('sciPregunta.componente', fn($q) => $q->where('sci_eje_id', $ejeId));
                }

                $total       = (clone $base)->count();
                $completadas = (clone $base)->where('estado', 'completada')->count();
                $vencidas    = (clone $base)->where('estado', 'vencida')->count();
                $en_proceso  = (clone $base)->whereIn('estado', ['pendiente', 'en_proceso'])->count();
                $observadas  = (clone $base)->where('estado', 'observado')->count();

                $sin_evidencia = (clone $base)
                    ->whereNotIn('estado', ['pendiente'])
                    ->whereDoesntHave('evidencias')->count();

                $evidencia_pendiente = (clone $base)
                    ->whereHas('evidencias', fn($q) => $q->where('estado', 'pendiente'))->count();

                $diasRetraso = (clone $base)
                    ->where('estado', 'vencida')->whereNotNull('fecha_limite')
                    ->selectRaw('AVG(DATEDIFF(NOW(), fecha_limite)) as promedio')
                    ->value('promedio');

                $porcentaje = $total > 0 ? round(($completadas / $total) * 100) : 0;
                $semaforo   = $porcentaje >= 75 ? 'success' : ($porcentaje >= 50 ? 'warning' : 'danger');

                $user->stat_total          = $total;
                $user->stat_completadas    = $completadas;
                $user->stat_vencidas       = $vencidas;
                $user->stat_en_proceso     = $en_proceso;
                $user->stat_observadas     = $observadas;
                $user->stat_sin_evidencia  = $sin_evidencia;
                $user->stat_ev_pendiente   = $evidencia_pendiente;
                $user->stat_porcentaje     = $porcentaje;
                $user->stat_semaforo       = $semaforo;
                $user->stat_dias_retraso   = $diasRetraso ? round($diasRetraso) : 0;

                return $user;
            })
            ->sortBy('stat_porcentaje')->values();
    }

    // ── Sin Evidencia (vista + datos AJAX) ────────────────────────────────────

    public function sinEvidencia(Request $request)
    {
        $unidadId      = $request->input('unidad_organica_id');
        $modulo        = $request->input('modulo');
        $ejeId         = $request->input('eje_id');
        $responsableId = $request->input('responsable_id');
        $prioridad     = $request->input('prioridad');
        $anio          = $request->input('anio', now()->year);

        $query = Actividad::with([
                'sciPregunta.componente',
                'integridadPregunta.componente',
                'unidadOrganica',
                'responsables',
            ])
            ->whereNotIn('estado', ['pendiente'])
            ->whereDoesntHave('evidencias')
            ->whereYear('created_at', $anio)
            ->when($unidadId,      fn($q) => $q->where('unidad_organica_id', $unidadId))
            ->when($modulo,        fn($q) => $q->where('modulo', $modulo))
            ->when($prioridad,     fn($q) => $q->where('prioridad', $prioridad))
            ->when($responsableId, fn($q) => $q->whereHas('responsables', fn($r) => $r->where('users.id', $responsableId)))
            ->when($ejeId,         fn($q) => $q->whereHas('sciPregunta.componente', fn($r) => $r->where('sci_eje_id', $ejeId)))
            ->orderByRaw("FIELD(estado,'vencida','observado','en_proceso','completada')")
            ->orderBy('fecha_limite');

        $baseQuery = Actividad::whereNotIn('estado', ['pendiente'])
            ->whereDoesntHave('evidencias')
            ->whereYear('created_at', $anio);

        $stats = [
            'total'      => (clone $baseQuery)->count(),
            'vencidas'   => (clone $baseQuery)->where('estado', 'vencida')->count(),
            'en_proceso' => (clone $baseQuery)->whereIn('estado', ['en_proceso', 'observado'])->count(),
            'alta_prio'  => (clone $baseQuery)->where('prioridad', 'alta')->count(),
        ];

        if ($request->ajax() || $request->wantsJson()) {
            $actividades = $query->get();
            $hoy = now();
            return response()->json([
                'stats' => $stats,
                'actividades' => $actividades->map(fn($act) => [
                    'id'           => $act->id,
                    'nombre'       => $act->nombre,
                    'codigo'       => $act->codigo,
                    'modulo'       => $act->modulo,
                    'unidad'       => $act->unidadOrganica?->sigla ?? '—',
                    'componente'   => $act->modulo === 'integridad'
                        ? $act->integridadPregunta?->componente?->nombre
                        : $act->sciPregunta?->componente?->nombre,
                    'responsables' => $act->responsables->take(2)->map(fn($r) => [
                        'name' => $r->name,
                        'tipo' => $r->pivot->tipo[0],
                        'color' => $r->pivot->tipo === 'principal' ? 'primary' : 'secondary',
                    ])->values(),
                    'estado'       => $act->estado,
                    'estado_label' => $act->estado_label,
                    'estado_color' => match($act->estado) {
                        'completada' => 'success', 'vencida' => 'danger',
                        'observado'  => 'info',    default   => 'warning',
                    },
                    'prioridad'    => $act->prioridad,
                    'prioridad_color' => match($act->prioridad) {
                        'alta' => 'danger', 'media' => 'warning', default => 'secondary',
                    },
                    'avance'       => $act->avance,
                    'fecha_limite' => $act->fecha_limite?->format('d/m/Y'),
                    'vencida'      => $act->fecha_limite && $act->fecha_limite->lt($hoy),
                    'dias_retraso' => $act->fecha_limite && $act->fecha_limite->lt($hoy)
                        ? (int) round($hoy->diffInDays($act->fecha_limite))
                        : null,
                ]),
            ]);
        }

        $actividades  = $query->paginate(20)->withQueryString();
        $unidades     = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
        $ejes         = SciEje::where('activo', true)->orderBy('anio', 'desc')->orderBy('orden')->get();
        $responsables = User::where('estado', 'activo')->orderBy('name')->get();
        $anios        = range(now()->year, now()->year - 3);

        return view('content.cumplimiento.sin-evidencia', compact(
            'actividades', 'stats', 'unidades', 'ejes', 'responsables', 'anios',
            'unidadId', 'modulo', 'ejeId', 'responsableId', 'prioridad', 'anio'
        ));
    }

    // ── Exportar ─────────────────────────────────────────────────────────────

    public function exportar(Request $request)
    {
        $anio      = (int) $request->input('anio', now()->year);
        $unidadId  = $request->input('unidad_organica_id') ? (int) $request->input('unidad_organica_id') : null;
        $modulo    = $request->input('modulo');
        $ejeId     = $request->input('eje_id') ? (int) $request->input('eje_id') : null;
        $formato   = $request->input('formato', 'excel');

        if ($formato === 'pdf') {
            $responsables = $this->calcularResponsables(
                $unidadId ? (string) $unidadId : null,
                $modulo,
                $ejeId ? (string) $ejeId : null,
                $anio
            );

            $sinEvidencia = Actividad::with(['sciPregunta.componente', 'integridadPregunta.componente', 'unidadOrganica', 'responsables'])
                ->whereNotIn('estado', ['pendiente'])
                ->whereDoesntHave('evidencias')
                ->whereYear('created_at', $anio)
                ->when($unidadId, fn($q) => $q->where('unidad_organica_id', $unidadId))
                ->when($modulo,   fn($q) => $q->where('modulo', $modulo))
                ->orderByRaw("FIELD(estado,'vencida','observado','en_proceso','completada')")
                ->orderBy('fecha_limite')->get();

            $totales = [
                'responsables'   => $responsables->count(),
                'en_riesgo'      => $responsables->where('stat_semaforo', 'danger')->count(),
                'sin_evidencia'  => $responsables->sum('stat_sin_evidencia'),
                'vencidas_total' => $responsables->sum('stat_vencidas'),
            ];

            $filtro_unidad = $unidadId ? UnidadOrganica::find($unidadId)?->nombre : null;
            $filtro_modulo = $modulo;

            $pdf = Pdf::loadView('exports.cumplimiento-pdf', compact(
                'responsables', 'sinEvidencia', 'totales', 'anio',
                'filtro_unidad', 'filtro_modulo'
            ))->setPaper('a4', 'landscape');

            return $pdf->download("PULSO-Cumplimiento-{$anio}.pdf");
        }

        return Excel::download(
            new CumplimientoExport($anio, $unidadId, null),
            "PULSO-Cumplimiento-{$anio}.xlsx"
        );
    }
}
