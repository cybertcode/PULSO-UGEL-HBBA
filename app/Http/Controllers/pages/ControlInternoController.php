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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ControlInternoController extends Controller
{
    public function index(Request $request)
    {
        $anio = $request->input('anio', now()->year);
        $user = Auth::user();

        $baseStats = Actividad::where('modulo', 'sci')->visiblesParaUsuario($user);
        $stats = [
            'total'       => (clone $baseStats)->count(),
            'completadas' => (clone $baseStats)->where('estado', 'completada')->count(),
            'en_proceso'  => (clone $baseStats)->where('estado', 'en_proceso')->count(),
            'observados'  => (clone $baseStats)->where('estado', 'observado')->count(),
            'vencidas'    => (clone $baseStats)
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
        $unidades     = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
        $responsables = User::where('estado', 'activo')->orderBy('name')->get();
        $anios        = Actividad::where('modulo', 'sci')->selectRaw('DISTINCT anio')->whereNotNull('anio')->orderByDesc('anio')->pluck('anio');

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
            }
        });

        return back()->with('success', "Actividad «{$validated['nombre']}» creada correctamente.");
    }

    public function update(Request $request, Actividad $actividad)
    {
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

        if ($validated['estado'] === 'completada' && !$actividad->fecha_cumplimiento) {
            $validated['fecha_cumplimiento'] = now();
            $validated['avance'] = 100;
        }

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

        return back()->with('success', 'Actividad actualizada correctamente.');
    }

    public function destroy(Actividad $actividad)
    {
        $actividad->delete();
        return back()->with('success', 'Actividad eliminada.');
    }

    public function updateAvance(Request $request, Actividad $actividad)
    {
        abort_unless($actividad->puedeEditarUsuario(), 403, 'No tienes permiso para actualizar esta actividad.');

        $request->validate(['avance' => 'required|integer|min:0|max:100']);

        $avance = $request->avance;
        $estado = match(true) {
            $avance >= 100 => 'completada',
            $avance > 0    => 'en_proceso',
            default        => $actividad->estado,
        };

        $actividad->update([
            'avance'             => $avance,
            'estado'             => $estado,
            'fecha_cumplimiento' => $avance >= 100 ? now() : $actividad->fecha_cumplimiento,
        ]);

        return response()->json(['ok' => true, 'avance' => $avance, 'estado' => $estado]);
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
}
