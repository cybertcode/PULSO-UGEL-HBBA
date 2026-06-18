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
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class CumplimientoController extends Controller
{
    // ── Panel unificado SCI + Integridad ─────────────────────────────────────

    public function panelSci(Request $request)
    {
        $hoy    = now();
        $anio   = (int) $request->input('anio', $hoy->year);
        $modulo = $request->input('modulo', 'ambos'); // sci | integridad | ambos
        $user   = \Illuminate\Support\Facades\Auth::user();

        $base = fn() => Actividad::whereYear('created_at', $anio)
            ->visiblesParaUsuario($user)
            ->when($modulo !== 'ambos', fn($q) => $q->where('modulo', $modulo));

        // ── KPIs globales ────────────────────────────────────────────────────
        $kpis = [
            'total'       => ($base)()->count(),
            'completadas' => ($base)()->where('estado', 'completada')->count(),
            'vencidas'    => ($base)()->where('estado', 'vencida')->count(),
            'sin_ev'      => ($base)()->whereNotIn('estado', ['pendiente'])->whereDoesntHave('evidencias')->count(),
            'en_proceso'  => ($base)()->whereIn('estado', ['en_proceso', 'pendiente'])->count(),
            'observadas'  => ($base)()->where('estado', 'observado')->count(),
        ];
        $kpis['porcentaje_global'] = $kpis['total'] > 0
            ? round(($kpis['completadas'] / $kpis['total']) * 100) : 0;

        // ── KPIs por módulo (siempre para los tabs) ──────────────────────────
        $kpis_sci = [
            'total'       => Actividad::whereYear('created_at', $anio)->where('modulo', 'sci')->visiblesParaUsuario($user)->count(),
            'completadas' => Actividad::whereYear('created_at', $anio)->where('modulo', 'sci')->visiblesParaUsuario($user)->where('estado', 'completada')->count(),
            'vencidas'    => Actividad::whereYear('created_at', $anio)->where('modulo', 'sci')->visiblesParaUsuario($user)->where('estado', 'vencida')->count(),
            'sin_ev'      => Actividad::whereYear('created_at', $anio)->where('modulo', 'sci')->visiblesParaUsuario($user)->whereNotIn('estado', ['pendiente'])->whereDoesntHave('evidencias')->count(),
        ];
        $kpis_sci['porcentaje'] = $kpis_sci['total'] > 0
            ? round(($kpis_sci['completadas'] / $kpis_sci['total']) * 100) : 0;

        $kpis_integridad = [
            'total'       => Actividad::whereYear('created_at', $anio)->where('modulo', 'integridad')->visiblesParaUsuario($user)->count(),
            'completadas' => Actividad::whereYear('created_at', $anio)->where('modulo', 'integridad')->visiblesParaUsuario($user)->where('estado', 'completada')->count(),
            'vencidas'    => Actividad::whereYear('created_at', $anio)->where('modulo', 'integridad')->visiblesParaUsuario($user)->where('estado', 'vencida')->count(),
            'sin_ev'      => Actividad::whereYear('created_at', $anio)->where('modulo', 'integridad')->visiblesParaUsuario($user)->whereNotIn('estado', ['pendiente'])->whereDoesntHave('evidencias')->count(),
        ];
        $kpis_integridad['porcentaje'] = $kpis_integridad['total'] > 0
            ? round(($kpis_integridad['completadas'] / $kpis_integridad['total']) * 100) : 0;

        // ── Próximas a vencer (15 días) ──────────────────────────────────────
        $proximas = Actividad::with(['unidadOrganica', 'responsables'])
            ->visiblesParaUsuario($user)
            ->whereNotIn('estado', ['completada', 'vencida', 'observado'])
            ->whereDate('fecha_limite', '>=', $hoy->toDateString())
            ->whereDate('fecha_limite', '<=', $hoy->copy()->addDays(15)->toDateString())
            ->when($modulo !== 'ambos', fn($q) => $q->where('modulo', $modulo))
            ->orderBy('fecha_limite')
            ->limit(10)->get();

        // ── Vencidas recientes (30 días) ─────────────────────────────────────
        $vencidas = Actividad::with(['unidadOrganica', 'responsables'])
            ->visiblesParaUsuario($user)
            ->where('estado', 'vencida')
            ->whereDate('fecha_limite', '>=', $hoy->copy()->subDays(30)->toDateString())
            ->when($modulo !== 'ambos', fn($q) => $q->where('modulo', $modulo))
            ->orderByDesc('fecha_limite')
            ->limit(10)->get();

        // ── Incumplidores top 8 (solo para usuarios con permiso de gestión) ───
        $incumplidores = collect();
        if (Gate::allows('cumplimiento.exportar')) {
            $incumplidores = User::where('estado', 'activo')
                ->whereHas('actividadesResponsable')
                ->with(['unidadOrganica', 'cargos'])
                ->get()
                ->map(function (User $u) use ($anio, $modulo, $base) {
                    $q = Actividad::whereHas('responsables', fn($r) => $r->where('users.id', $u->id))
                        ->whereYear('created_at', $anio)
                        ->when($modulo !== 'ambos', fn($r) => $r->where('modulo', $modulo));
                    $u->inc_vencidas = (clone $q)->where('estado', 'vencida')->count();
                    $u->inc_sin_ev   = (clone $q)->whereNotIn('estado', ['pendiente'])->whereDoesntHave('evidencias')->count();
                    $u->inc_total    = $u->inc_vencidas + $u->inc_sin_ev;
                    $u->inc_unidad   = $u->unidadOrganica?->sigla ?? '—';
                    return $u;
                })
                ->filter(fn($u) => $u->inc_total > 0)
                ->sortByDesc('inc_total')->take(8)->values();
        }

        // ── Avance por unidad ────────────────────────────────────────────────
        $avance_unidades = UnidadOrganica::where('activo', true)
            ->withCount([
                'actividades as total_act'      => fn($q) => $q->whereYear('created_at', $anio)
                    ->when($modulo !== 'ambos', fn($r) => $r->where('modulo', $modulo)),
                'actividades as completadas_act' => fn($q) => $q->whereYear('created_at', $anio)
                    ->when($modulo !== 'ambos', fn($r) => $r->where('modulo', $modulo))->where('estado', 'completada'),
                'actividades as vencidas_act'    => fn($q) => $q->whereYear('created_at', $anio)
                    ->when($modulo !== 'ambos', fn($r) => $r->where('modulo', $modulo))->where('estado', 'vencida'),
            ])
            ->get()
            ->map(function ($u) {
                $u->porcentaje = $u->total_act > 0 ? round(($u->completadas_act / $u->total_act) * 100) : 0;
                $u->semaforo   = match(true) {
                    $u->porcentaje >= 75 => 'success',
                    $u->porcentaje >= 50 => 'warning',
                    default              => 'danger',
                };
                return $u;
            })
            ->sortBy('porcentaje')->values();

        $anios = range(now()->year, now()->year - 3);

        return view('content.cumplimiento.panel-sci', compact(
            'kpis', 'kpis_sci', 'kpis_integridad',
            'proximas', 'vencidas', 'incumplidores',
            'avance_unidades', 'hoy', 'anio', 'modulo', 'anios'
        ));
    }

    // ── Responsables (vista + datos AJAX) ────────────────────────────────────

    public function responsables(Request $request)
    {
        $unidadId  = $request->input('unidad_organica_id');
        $modulo    = $request->input('modulo');          // sci | integridad | ''
        $ejeId     = $request->input('eje_id');
        $anio      = (int) $request->input('anio', now()->year);
        $orden     = $request->input('orden', 'peor');   // peor | mejor | nombre
        $pagina    = (int) $request->input('pagina', 1);
        $porPagina = 15;

        $responsables = $this->calcularResponsables($unidadId, $modulo, $ejeId, $anio, $orden);

        $totales = [
            'responsables'   => $responsables->count(),
            'en_riesgo'      => $responsables->where('stat_semaforo', 'danger')->count(),
            'sin_evidencia'  => $responsables->sum('stat_sin_evidencia'),
            'vencidas_total' => $responsables->sum('stat_vencidas'),
        ];

        if ($request->ajax() || $request->wantsJson()) {
            $total   = $responsables->count();
            $paginados = $responsables->forPage($pagina, $porPagina);
            return response()->json([
                'totales'       => $totales,
                'meta'          => [
                    'total'      => $total,
                    'pagina'     => $pagina,
                    'por_pagina' => $porPagina,
                    'total_pags' => (int) ceil($total / $porPagina),
                ],
                'responsables'  => $paginados->map(fn($u) => [
                    'id'            => $u->id,
                    'name'          => $u->name,
                    'cargo'         => $u->cargos->first()?->nombre ?? 'Sin cargo',
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
                ])->values(),
            ]);
        }

        $paginados    = $responsables->forPage($pagina, $porPagina);
        $totalPags    = (int) ceil($responsables->count() / $porPagina);
        $unidades     = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
        $ejes         = SciEje::where('activo', true)->orderBy('anio', 'desc')->orderBy('orden')->get();
        $anios        = range(now()->year, now()->year - 3);

        return view('content.cumplimiento.responsables', compact(
            'paginados', 'responsables', 'unidades', 'ejes', 'anios',
            'unidadId', 'modulo', 'ejeId', 'anio', 'orden',
            'totales', 'pagina', 'totalPags', 'porPagina'
        ));
    }

    private function calcularResponsables(?string $unidadId, ?string $modulo, ?string $ejeId, int $anio, string $orden = 'peor')
    {
        $users = User::where('estado', 'activo')
            ->whereHas('actividadesResponsable')
            ->with(['unidadOrganica', 'cargos'])
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

                $user->stat_total         = $total;
                $user->stat_completadas   = $completadas;
                $user->stat_vencidas      = $vencidas;
                $user->stat_en_proceso    = $en_proceso;
                $user->stat_observadas    = $observadas;
                $user->stat_sin_evidencia = $sin_evidencia;
                $user->stat_ev_pendiente  = $evidencia_pendiente;
                $user->stat_porcentaje    = $porcentaje;
                $user->stat_semaforo      = $semaforo;
                $user->stat_dias_retraso  = $diasRetraso ? round($diasRetraso) : 0;

                return $user;
            })
            ->filter(fn($u) => $u->stat_total > 0);

        return match($orden) {
            'mejor' => $users->sortByDesc('stat_porcentaje')->values(),
            'nombre' => $users->sortBy('name')->values(),
            default  => $users->sortBy('stat_porcentaje')->values(), // peor primero
        };
    }

    // ── Sin Evidencia (vista + datos AJAX con paginación) ────────────────────

    public function sinEvidencia(Request $request)
    {
        $unidadId      = $request->input('unidad_organica_id');
        $modulo        = $request->input('modulo');
        $ejeId         = $request->input('eje_id');
        $responsableId = $request->input('responsable_id');
        $prioridad     = $request->input('prioridad');
        $anio          = (int) $request->input('anio', now()->year);
        $porPagina     = 20;

        $user = \Illuminate\Support\Facades\Auth::user();

        $query = Actividad::with([
                'sciPregunta.componente',
                'integridadPregunta.componente',
                'unidadOrganica',
                'responsables',
            ])
            ->visiblesParaUsuario($user)
            ->whereNotIn('estado', ['pendiente', 'completada'])
            ->where(fn($q) => $q
                ->whereDoesntHave('evidencias')
                ->orWhereHas('evidencias', fn($e) => $e->where('estado', 'rechazado'))
                ->orWhere('estado', 'observado')
            )
            ->whereYear('created_at', $anio)
            ->when($unidadId,      fn($q) => $q->where('unidad_organica_id', $unidadId))
            ->when($modulo,        fn($q) => $q->where('modulo', $modulo))
            ->when($prioridad,     fn($q) => $q->where('prioridad', $prioridad))
            ->when($responsableId, fn($q) => $q->whereHas('responsables', fn($r) => $r->where('users.id', $responsableId)))
            ->when($ejeId,         fn($q) => $q->whereHas('sciPregunta.componente', fn($r) => $r->where('sci_eje_id', $ejeId)))
            ->orderByDesc('created_at');

        // Stats con el mismo scope de visibilidad
        $baseStats = Actividad::visiblesParaUsuario($user)
            ->whereNotIn('estado', ['pendiente', 'completada'])
            ->where(fn($q) => $q
                ->whereDoesntHave('evidencias')
                ->orWhereHas('evidencias', fn($e) => $e->where('estado', 'rechazado'))
                ->orWhere('estado', 'observado')
            )
            ->whereYear('created_at', $anio);

        $stats = [
            'total'      => (clone $baseStats)->count(),
            'vencidas'   => (clone $baseStats)->where('estado', 'vencida')->count(),
            'en_proceso' => (clone $baseStats)->whereIn('estado', ['en_proceso', 'observado'])->count(),
            'observadas' => (clone $baseStats)->where('estado', 'observado')->count(),
            'alta_prio'  => (clone $baseStats)->where('prioridad', 'alta')->count(),
            'sci'        => (clone $baseStats)->where('modulo', 'sci')->count(),
            'integridad' => (clone $baseStats)->where('modulo', 'integridad')->count(),
        ];

        if ($request->ajax() || $request->wantsJson()) {
            $pagina   = (int) $request->input('pagina', 1);
            $hoy      = now();
            $total    = (clone $query)->count();
            $actividades = (clone $query)->forPage($pagina, $porPagina)->get();

            return response()->json([
                'stats' => $stats,
                'meta'  => [
                    'total'      => $total,
                    'pagina'     => $pagina,
                    'por_pagina' => $porPagina,
                    'total_pags' => (int) ceil($total / $porPagina),
                ],
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
                        'name'  => $r->name,
                        'tipo'  => strtoupper(substr($r->pivot->tipo, 0, 1)),
                        'color' => $r->pivot->tipo === 'principal' ? 'primary' : 'secondary',
                    ])->values(),
                    'estado'          => $act->estado,
                    'estado_label'    => $act->estado_label,
                    'estado_color'    => match($act->estado) {
                        'completada' => 'success', 'vencida' => 'danger',
                        'observado'  => 'info',    default   => 'warning',
                    },
                    'prioridad'       => $act->prioridad,
                    'prioridad_color' => match($act->prioridad) {
                        'alta' => 'danger', 'media' => 'warning', default => 'secondary',
                    },
                    'avance'          => $act->avance,
                    'fecha_limite'    => $act->fecha_limite?->format('d/m/Y'),
                    'vencida'         => $act->fecha_limite && $act->fecha_limite->lt($hoy),
                    'dias_retraso'    => $act->fecha_limite && $act->fecha_limite->lt($hoy)
                        ? (int) round($hoy->diffInDays($act->fecha_limite))
                        : null,
                    'created_at'      => $act->created_at->format('d/m/Y'),
                ]),
            ]);
        }

        $actividades  = $query->paginate($porPagina)->withQueryString();
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
        $anio     = (int) $request->input('anio', now()->year);
        $unidadId = $request->input('unidad_organica_id') ? (int) $request->input('unidad_organica_id') : null;
        $modulo   = $request->input('modulo');
        $ejeId    = $request->input('eje_id') ? (int) $request->input('eje_id') : null;
        $formato  = $request->input('formato', 'excel');

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
                ->orderByDesc('created_at')->get();

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

            return $pdf->download("PULSO-Seguimiento-{$anio}.pdf");
        }

        return Excel::download(
            new CumplimientoExport($anio, $unidadId, null),
            "PULSO-Seguimiento-{$anio}.xlsx"
        );
    }
}
