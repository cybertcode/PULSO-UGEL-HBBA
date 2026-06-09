<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\ActividadHistorial;
use App\Models\Componente;
use App\Models\UnidadOrganica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ControlInternoController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total'       => Actividad::count(),
            'completadas' => Actividad::where('estado', 'completada')->count(),
            'en_proceso'  => Actividad::where('estado', 'en_proceso')->count(),
            'observados'  => Actividad::where('estado', 'observado')->count(),
            'vencidas'    => Actividad::whereNotIn('estado', ['completada', 'observado'])
                              ->whereDate('fecha_limite', '<', now())->count(),
        ];

        $query = Actividad::with(['componente', 'unidadOrganica', 'responsables'])
            ->orderBy('fecha_limite');

        if ($request->filled('componente_id')) {
            $query->where('componente_id', $request->componente_id);
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
        $componentes  = Componente::where('activo', true)->orderBy('numero')->get();
        $unidades     = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
        $responsables = User::where('estado', 'activo')->orderBy('name')->get();

        return view('content.control-interno.index', compact(
            'stats', 'actividades', 'componentes', 'unidades', 'responsables'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'             => 'required|string|max:255',
            'componente_id'      => 'required|exists:componentes,id',
            'unidad_organica_id' => 'nullable|exists:unidades_organicas,id',
            'fecha_limite'       => 'required|date',
            'fecha_inicio'       => 'nullable|date|before_or_equal:fecha_limite',
            'prioridad'          => 'required|in:alta,media,baja',
            'numero_sgd'         => 'nullable|string|max:50',
            'descripcion'        => 'nullable|string',
            'observaciones'      => 'nullable|string',
            'responsables'       => 'nullable|array',
            'responsables.*'     => 'exists:users,id',
            'tipos'              => 'nullable|array',
            'tipos.*'            => 'in:principal,colaborador,supervisor',
        ]);

        $validated['creado_por'] = Auth::id();
        $validated['estado']     = 'pendiente';
        $validated['avance']     = 0;

        $anio  = now()->year;
        $count = Actividad::whereYear('created_at', $anio)->withTrashed()->count() + 1;
        $validated['codigo'] = 'SCI-' . $anio . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

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
            'nombre'             => 'required|string|max:255',
            'componente_id'      => 'required|exists:componentes,id',
            'unidad_organica_id' => 'nullable|exists:unidades_organicas,id',
            'fecha_limite'       => 'required|date',
            'fecha_inicio'       => 'nullable|date|before_or_equal:fecha_limite',
            'avance'             => 'nullable|integer|min:0|max:100',
            'estado'             => 'required|in:pendiente,en_proceso,completada,observado,vencida',
            'prioridad'          => 'required|in:alta,media,baja',
            'numero_sgd'         => 'nullable|string|max:50',
            'descripcion'        => 'nullable|string',
            'observaciones'      => 'nullable|string',
            'responsables'       => 'nullable|array',
            'responsables.*'     => 'exists:users,id',
            'tipos'              => 'nullable|array',
            'tipos.*'            => 'in:principal,colaborador,supervisor',
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
