<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\ActividadHistorial;
use App\Models\SciEje;
use App\Models\SciComponente;
use App\Models\SciPregunta;
use App\Models\UnidadOrganica;
use App\Models\User;
use App\Notifications\ActividadAsignada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ControlInternoController extends Controller
{
    public function index(Request $request)
    {
        $anio = $request->input('anio', now()->year);
        $user = Auth::user();

        $baseStats = Actividad::where('modulo', 'sci')->visiblesParaUsuario($user);
        $stats = [
            'total'          => (clone $baseStats)->count(),
            'completadas'    => (clone $baseStats)->where('estado', 'completada')->count(),
            'en_proceso'     => (clone $baseStats)->where('estado', 'en_proceso')->count(),
            'observados'     => (clone $baseStats)->where('estado', 'observado')->count(),
            'ev_rechazadas'  => (clone $baseStats)->whereHas('evidencias', fn($q) => $q->where('estado', 'rechazado'))->count(),
            'vencidas'       => (clone $baseStats)
                                  ->whereNotIn('estado', ['completada', 'observado'])
                                  ->whereDate('fecha_limite', '<', now())->count(),
        ];

        $query = Actividad::with([
                'sciPregunta.componente.eje',
                'unidadOrganica',
                'responsables',
            ])
            ->where('modulo', 'sci')
            ->visiblesParaUsuario($user)
            ->orderBy('fecha_limite');

        if ($request->filled('anio')) {
            $query->where('anio', $request->anio);
        }
        if ($request->filled('eje_id')) {
            $query->whereHas('sciPregunta.componente', fn($q) => $q->where('eje_id', $request->eje_id));
        }
        if ($request->filled('componente_id')) {
            $query->whereHas('sciPregunta', fn($q) => $q->where('componente_id', $request->componente_id));
        }
        if ($request->filled('pregunta_id')) {
            $query->where('sci_pregunta_id', $request->pregunta_id);
        }
        if ($request->filled('unidad_id')) {
            $query->where('unidad_organica_id', $request->unidad_id);
        }
        if ($request->filled('responsable_id')) {
            $query->whereHas('responsables', fn($q) => $q->where('users.id', $request->responsable_id));
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }
        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(fn($q) => $q
                ->where('nombre', 'like', "%$b%")
                ->orWhere('codigo', 'like', "%$b%")
                ->orWhere('numero_sgd', 'like', "%$b%")
            );
        }

        $actividades  = $query->paginate(15)->withQueryString();
        $ejes         = SciEje::where('activo', true)->orderBy('anio', 'desc')->orderBy('orden')->get();
        $componentes  = $request->filled('eje_id')
                        ? SciComponente::where('eje_id', $request->eje_id)->where('activo', true)->orderBy('orden')->get()
                        : collect();
        $anios        = Actividad::where('modulo', 'sci')->selectRaw('DISTINCT anio')->whereNotNull('anio')->orderByDesc('anio')->pluck('anio');

        // Filtros de unidad y responsable solo disponibles para quien tiene visión amplia
        if ($user->can('actividades.ver-todas')) {
            $unidades     = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
            $responsables = User::where('estado', 'activo')->orderBy('name')->get();
        } elseif ($user->can('actividades.ver-unidad')) {
            $unidades     = UnidadOrganica::where('activo', true)->where('id', $user->unidad_organica_id)->get();
            $responsables = User::where('estado', 'activo')->where('unidad_organica_id', $user->unidad_organica_id)->orderBy('name')->get();
        } else {
            $unidades     = collect();
            $responsables = collect();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html'  => view('content.control-interno._tabla', compact('actividades'))->render(),
                'stats' => $stats,
                'total' => $actividades->total(),
                'from'  => $actividades->firstItem() ?? 0,
                'to'    => $actividades->lastItem() ?? 0,
                'pages' => $actividades->hasPages()
                    ? $actividades->links()->toHtml()
                    : '',
            ]);
        }

        return view('content.control-interno.index', compact(
            'stats', 'actividades', 'ejes', 'componentes', 'unidades', 'responsables', 'anio', 'anios'
        ));
    }

    public function store(Request $request)
    {
        Gate::authorize('control-interno.crear');

        $validated = $request->validate([
            'nombre'              => 'required|string|max:255',
            'modulo'              => 'required|in:sci,integridad',
            'anio'                => 'required|integer|min:2020|max:2099',
            'sci_pregunta_id'     => 'required_if:modulo,sci|nullable|exists:sci_preguntas,id',
            'integridad_pregunta_id' => 'required_if:modulo,integridad|nullable|exists:integridad_preguntas,id',
            'unidad_organica_id'  => 'nullable|exists:unidades_organicas,id',
            'fecha_limite'        => 'required|date',
            'fecha_inicio'        => 'nullable|date|before_or_equal:fecha_limite',
            'prioridad'           => 'required|in:alta,media,baja',
            'numero_sgd'          => 'nullable|string|max:50',
            'descripcion'         => 'nullable|string',
            'observaciones'       => 'nullable|string',
            'responsables'        => 'nullable|array',
            'responsables.*'      => 'exists:users,id',
            'tipos'               => 'nullable|array',
            'tipos.*'             => 'in:principal,colaborador,supervisor',
        ]);

        $validated['creado_por'] = Auth::id();
        $validated['estado']     = 'pendiente';
        $validated['avance']     = 0;

        $anio  = $validated['anio'];
        $modulo = $validated['modulo'];
        $prefix = strtoupper($modulo);
        $count = Actividad::where('modulo', $modulo)->whereYear('created_at', $anio)->withTrashed()->count() + 1;
        $validated['codigo'] = $prefix . '-' . $anio . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($validated, $request) {
            $actividad = Actividad::create(\Arr::except($validated, ['responsables', 'tipos']));

            ActividadHistorial::create([
                'actividad_id'   => $actividad->id,
                'usuario_id'     => Auth::id(),
                'campo'          => 'estado',
                'valor_anterior' => null,
                'valor_nuevo'    => 'pendiente',
                'descripcion'    => 'Actividad creada',
            ]);

            if (!empty($validated['responsables'])) {
                $tipos = $validated['tipos'] ?? [];
                $sync  = collect($validated['responsables'])
                    ->mapWithKeys(fn($id) => [$id => ['tipo' => $tipos[$id] ?? 'principal']])
                    ->toArray();
                $actividad->responsables()->sync($sync);

                // Notificar a cada responsable asignado (excepto quien crea la actividad)
                $creadorId = Auth::id();
                $actividad->load('responsables');
                foreach ($actividad->responsables as $resp) {
                    if ($resp->id === $creadorId) continue;
                    $resp->notify(new ActividadAsignada($actividad, 'nueva', $resp->pivot->tipo));
                }
            }
        });

        return back()->with('success', "Actividad «{$validated['nombre']}» creada correctamente.");
    }

    public function update(Request $request, Actividad $actividad)
    {
        Gate::authorize('control-interno.editar');
        abort_unless($actividad->puedeEditarUsuario(), 403, 'No tienes permiso para editar esta actividad.');

        $validated = $request->validate([
            'nombre'              => 'required|string|max:255',
            'sci_pregunta_id'     => 'nullable|exists:sci_preguntas,id',
            'integridad_pregunta_id' => 'nullable|exists:integridad_preguntas,id',
            'unidad_organica_id'  => 'nullable|exists:unidades_organicas,id',
            'fecha_limite'        => 'required|date',
            'fecha_inicio'        => 'nullable|date|before_or_equal:fecha_limite',
            'avance'              => 'nullable|integer|min:0|max:100',
            'estado'              => 'required|in:pendiente,en_proceso,completada,observado,vencida',
            'prioridad'           => 'required|in:alta,media,baja',
            'numero_sgd'          => 'nullable|string|max:50',
            'descripcion'         => 'nullable|string',
            'observaciones'       => 'nullable|string',
            'responsables'        => 'nullable|array',
            'responsables.*'      => 'exists:users,id',
            'tipos'               => 'nullable|array',
            'tipos.*'             => 'in:principal,colaborador,supervisor',
        ]);

        if ($validated['estado'] === 'completada') {
            if (!$actividad->evidencias()->where('estado', 'validado')->exists()) {
                return back()->withErrors(['estado' => 'No se puede marcar como completada sin al menos una evidencia validada.'])->withInput();
            }
            if (!$actividad->fecha_cumplimiento) {
                $validated['fecha_cumplimiento'] = now();
            }
            $validated['avance'] = 100;
        }

        $fechaAntes       = $actividad->fecha_limite?->toDateString();
        $responsablesAntes = $actividad->responsables->pluck('id')->toArray();
        $creadorId        = Auth::id();

        DB::transaction(function () use ($validated, $actividad) {
            $actividad->update(\Arr::except($validated, ['responsables', 'tipos']));

            if (!empty($validated['responsables'])) {
                $tipos = $validated['tipos'] ?? [];
                $sync  = collect($validated['responsables'])
                    ->mapWithKeys(fn($id) => [$id => ['tipo' => $tipos[$id] ?? 'principal']])
                    ->toArray();
                $actividad->responsables()->sync($sync);
            }
        });

        $actividad->load('responsables');

        // Notificar a responsables nuevos
        $responsablesDespues = $actividad->responsables->pluck('id')->toArray();
        $nuevosIds = array_diff($responsablesDespues, $responsablesAntes);
        foreach ($actividad->responsables->whereIn('id', $nuevosIds) as $resp) {
            if ($resp->id === $creadorId) continue;
            $resp->notify(new ActividadAsignada($actividad, 'nueva', $resp->pivot->tipo));
        }

        // Notificar a todos los responsables existentes si cambió la fecha límite
        $fechaDespues = $actividad->fecha_limite?->toDateString();
        if ($fechaAntes !== $fechaDespues && $fechaDespues) {
            foreach ($actividad->responsables->whereNotIn('id', $nuevosIds) as $resp) {
                if ($resp->id === $creadorId) continue;
                $resp->notify(new ActividadAsignada($actividad, 'fecha_limite', $resp->pivot->tipo));
            }
        }

        return back()->with('success', 'Actividad actualizada correctamente.');
    }

    public function destroy(Actividad $actividad)
    {
        Gate::authorize('control-interno.eliminar');
        $actividad->delete();
        return back()->with('success', 'Actividad eliminada.');
    }

    public function updateAvance(Request $request, Actividad $actividad)
    {
        abort_unless($actividad->puedeEditarUsuario(), 403, 'No tienes permiso para actualizar esta actividad.');

        $request->validate(['avance' => 'required|integer|min:0|max:100']);

        $avance = (int) $request->avance;

        if ($avance >= 100) {
            $tieneEvidenciaValidada = $actividad->evidencias()->where('estado', 'validado')->exists();
            $estado = $tieneEvidenciaValidada ? 'completada' : 'en_proceso';
        } elseif ($avance > 0 && in_array($actividad->estado, ['pendiente', 'observado'])) {
            $estado = 'en_proceso';
        } else {
            $estado = $actividad->estado;
        }

        $actividad->update([
            'avance'             => $avance,
            'estado'             => $estado,
            'fecha_cumplimiento' => $estado === 'completada' ? now() : $actividad->fecha_cumplimiento,
        ]);

        return response()->json([
            'ok'          => true,
            'avance'      => $avance,
            'estado'      => $estado,
            'advertencia' => ($avance >= 100 && $estado !== 'completada')
                ? 'Avance guardado al 100%, pero se requiere al menos una evidencia validada para completar la actividad.'
                : null,
        ]);
    }

    public function historial(Actividad $actividad)
    {
        abort_unless($actividad->puedeEditarUsuario(), 403);

        $historial = ActividadHistorial::with('usuario')
            ->where('actividad_id', $actividad->id)
            ->latest()
            ->get()
            ->map(fn($h) => [
                'campo'          => $h->campo,
                'campo_label'    => $h->campo_label,
                'valor_anterior' => $h->valor_anterior,
                'valor_nuevo'    => $h->valor_nuevo,
                'descripcion'    => $h->descripcion,
                'usuario'        => ['name' => $h->usuario?->name ?? 'Sistema'],
                'created_at'     => $h->created_at,
            ]);

        return response()->json($historial);
    }

    public function seguimientoResponsable(Request $request, User $user)
    {
        abort_unless(
            Auth::user()->can('actividades.ver-todas') || Auth::user()->can('actividades.ver-unidad'),
            403
        );

        $modulo = $request->input('modulo', 'sci');

        $actividades = Actividad::with([
                'sciPregunta.componente.eje',
                'integridadPregunta.componente.etapa',
                'evidencias',
            ])
            ->where('modulo', $modulo)
            ->whereHas('responsables', fn($q) => $q->where('users.id', $user->id))
            ->orderByRaw("FIELD(estado,'vencida','observado','en_proceso','pendiente','completada')")
            ->orderBy('fecha_limite')
            ->get();

        // Para cada actividad, obtener el último movimiento del responsable en historial
        $actividadIds = $actividades->pluck('id');
        $ultimosMovimientos = ActividadHistorial::whereIn('actividad_id', $actividadIds)
            ->where('usuario_id', $user->id)
            ->select('actividad_id', 'campo', 'descripcion', 'valor_nuevo', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('actividad_id')
            ->map(fn($items) => $items->first());

        $items = $actividades->map(function ($act) use ($ultimosMovimientos, $user) {
            $ult = $ultimosMovimientos->get($act->id);
            $diasRestantes = $act->fecha_limite
                ? (int) round(now()->diffInDays($act->fecha_limite, false))
                : null;

            $componente = $act->modulo === 'integridad'
                ? $act->integridadPregunta?->componente?->nombre
                : $act->sciPregunta?->componente?->nombre;

            $evTotal     = $act->evidencias->count();
            $evValidadas = $act->evidencias->where('estado', 'validado')->count();
            $evPendientes= $act->evidencias->where('estado', 'pendiente')->count();
            $evRechazadas= $act->evidencias->where('estado', 'rechazado')->count();

            return [
                'id'               => $act->id,
                'codigo'           => $act->codigo,
                'nombre'           => $act->nombre,
                'estado'           => $act->estado,
                'avance'           => $act->avance,
                'prioridad'        => $act->prioridad,
                'fecha_limite'     => $act->fecha_limite?->format('d/m/Y'),
                'dias_restantes'   => $diasRestantes,
                'componente'       => $componente,
                'ev_total'         => $evTotal,
                'ev_validadas'     => $evValidadas,
                'ev_pendientes'    => $evPendientes,
                'ev_rechazadas'    => $evRechazadas,
                'ultimo_movimiento'=> $ult ? [
                    'campo'       => $ult->campo,
                    'descripcion' => $ult->descripcion,
                    'valor_nuevo' => $ult->valor_nuevo,
                    'fecha'       => $ult->created_at->diffForHumans(),
                    'fecha_exact' => $ult->created_at->format('d/m/Y H:i'),
                ] : null,
            ];
        });

        $stats = [
            'total'       => $actividades->count(),
            'completadas' => $actividades->where('estado', 'completada')->count(),
            'en_proceso'  => $actividades->whereIn('estado', ['en_proceso', 'pendiente'])->count(),
            'vencidas'    => $actividades->where('estado', 'vencida')->count(),
            'observadas'  => $actividades->where('estado', 'observado')->count(),
            'sin_movimiento' => $items->filter(fn($i) => $i['ultimo_movimiento'] === null)->count(),
        ];

        return response()->json([
            'usuario' => [
                'id'     => $user->id,
                'name'   => $user->name,
                'cargo'  => $user->cargo ?? null,
                'unidad' => $user->unidadOrganica?->nombre ?? null,
            ],
            'stats'  => $stats,
            'items'  => $items->values(),
        ]);
    }
}
