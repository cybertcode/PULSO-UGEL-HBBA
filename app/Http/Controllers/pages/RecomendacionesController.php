<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Recomendacion;
use App\Models\Actividad;
use App\Models\UnidadOrganica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class RecomendacionesController extends Controller
{
    // ── Página shell (solo carga el HTML, los datos vienen por AJAX) ──────────
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user->can('actividades.ver-todas')) {
            $unidades    = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
            $usuarios    = User::where('estado', 'activo')->orderBy('name')->get();
            $actividades = Actividad::visiblesParaUsuario($user)->orderBy('nombre')->get();
        } elseif ($user->can('actividades.ver-unidad')) {
            $unidades    = UnidadOrganica::where('activo', true)->where('id', $user->unidad_organica_id)->get();
            $usuarios    = User::where('estado', 'activo')->where('unidad_organica_id', $user->unidad_organica_id)->orderBy('name')->get();
            $actividades = Actividad::visiblesParaUsuario($user)->orderBy('nombre')->get();
        } else {
            $unidades    = collect();
            $usuarios    = User::where('id', $user->id)->get();
            $actividades = Actividad::visiblesParaUsuario($user)->orderBy('nombre')->get();
        }

        $tipos = [
            'observacion'   => 'Observación',
            'recomendacion' => 'Recomendación',
            'mejora'        => 'Oportunidad de Mejora',
        ];

        $origenes = ['SCI', 'OCI', 'DRE', 'Auditoría', 'Autocontrol', 'MINEDU', 'Propia'];

        return view('content.recomendaciones.index', compact(
            'unidades', 'usuarios', 'actividades', 'tipos', 'origenes'
        ));
    }

    // ── Scope de visibilidad reutilizable ────────────────────────────────────
    private function applyScope($query, $user)
    {
        if (!$user->can('actividades.ver-todas')) {
            if ($user->can('actividades.ver-unidad') && !empty($user->unidad_organica_id)) {
                $query->where(function ($q) use ($user) {
                    $q->where('unidad_organica_id', $user->unidad_organica_id)
                      ->orWhere('responsable_id', $user->id);
                });
            } else {
                $query->where('responsable_id', $user->id);
            }
        }
        return $query;
    }

    // ── Endpoint JSON: lista + stats + paginación ────────────────────────────
    public function data(Request $request)
    {
        $modulo = $request->input('modulo', 'sci');
        $user   = \Illuminate\Support\Facades\Auth::user();

        $query = Recomendacion::with(['unidadOrganica', 'responsable', 'actividad'])
            ->where('modulo', $modulo);

        $this->applyScope($query, $user);

        $query->orderByDesc('created_at');

        if ($request->filled('estado'))    $query->where('estado', $request->estado);
        if ($request->filled('tipo'))      $query->where('tipo', $request->tipo);
        if ($request->filled('prioridad')) $query->where('prioridad', $request->prioridad);
        if ($request->filled('origen'))    $query->where('origen', $request->origen);
        if ($request->filled('unidad'))    $query->where('unidad_organica_id', $request->unidad);
        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(fn($q) => $q
                ->where('titulo', 'like', "%$b%")
                ->orWhere('descripcion', 'like', "%$b%")
                ->orWhere('numero_sgd', 'like', "%$b%")
            );
        }

        $paginated = $query->paginate(15)->withQueryString();

        // Stats respetando el mismo scope de visibilidad
        $base = Recomendacion::where('modulo', $modulo);
        $this->applyScope($base, $user);

        $stats = [
            'total'      => (clone $base)->count(),
            'pendientes' => (clone $base)->whereIn('estado', ['pendiente', 'en_proceso'])->count(),
            'atendidas'  => (clone $base)->where('estado', 'atendida')->count(),
            'rechazadas' => (clone $base)->where('estado', 'rechazada')->count(),
            'vencidas'   => (clone $base)->whereIn('estado', ['pendiente', 'en_proceso'])
                                ->where('fecha_limite', '<', now())->count(),
            'alta_prior' => (clone $base)->where('prioridad', 'alta')
                                ->whereIn('estado', ['pendiente', 'en_proceso'])->count(),
            'por_vencer' => (clone $base)->whereIn('estado', ['pendiente', 'en_proceso'])
                                ->whereBetween('fecha_limite', [now(), now()->addDays(7)])->count(),
        ];

        // tabStats también filtrados por scope
        $tabSci = $this->applyScope(Recomendacion::where('modulo', 'sci')->whereIn('estado', ['pendiente', 'en_proceso']), $user);
        $tabInt = $this->applyScope(Recomendacion::where('modulo', 'integridad')->whereIn('estado', ['pendiente', 'en_proceso']), $user);
        $tabStats = [
            'sci'        => $tabSci->count(),
            'integridad' => $tabInt->count(),
        ];

        // Serializar filas
        $rows = $paginated->map(function ($rec) {
            return [
                'id'            => $rec->id,
                'titulo'        => $rec->titulo,
                'descripcion'   => $rec->descripcion,
                'tipo'          => $rec->tipo,
                'tipo_label'    => $rec->tipo_label,
                'tipo_color'    => $rec->tipo_color,
                'modulo'        => $rec->modulo,
                'unidad_id'     => $rec->unidad_organica_id,
                'unidad_sigla'  => optional($rec->unidadOrganica)->sigla,
                'unidad_nombre' => optional($rec->unidadOrganica)->nombre,
                'responsable_id'    => $rec->responsable_id,
                'responsable_nombre'=> optional($rec->responsable)->name,
                'actividad_id'  => $rec->actividad_id,
                'actividad_nombre' => optional($rec->actividad)->nombre,
                'estado'        => $rec->estado,
                'estado_label'  => $rec->estado_label,
                'estado_color'  => $rec->estado_color,
                'prioridad'     => $rec->prioridad,
                'prioridad_color' => $rec->prioridad_color,
                'origen'        => $rec->origen,
                'numero_sgd'    => $rec->numero_sgd,
                'fecha_emision' => optional($rec->fecha_emision)->format('Y-m-d'),
                'fecha_limite'  => optional($rec->fecha_limite)->format('Y-m-d'),
                'fecha_atencion'=> optional($rec->fecha_atencion)->format('Y-m-d'),
                'fecha_limite_fmt' => optional($rec->fecha_limite)->format('d/m/Y'),
                'esta_vencida'  => $rec->esta_vencida,
                'dias_restantes'=> $rec->dias_restantes,
                'observaciones' => $rec->observaciones,
            ];
        });

        return response()->json([
            'data'      => $rows,
            'stats'     => $stats,
            'tabStats'  => $tabStats,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'from'         => $paginated->firstItem(),
                'to'           => $paginated->lastItem(),
            ],
        ]);
    }

    // ── Store ─────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo'             => 'required|string|max:255',
            'descripcion'        => 'nullable|string',
            'tipo'               => 'required|in:observacion,recomendacion,mejora',
            'modulo'             => 'required|in:sci,integridad',
            'actividad_id'       => 'nullable|exists:actividades,id',
            'unidad_organica_id' => 'nullable|exists:unidades_organicas,id',
            'responsable_id'     => 'nullable|exists:users,id',
            'estado'             => 'required|in:pendiente,en_proceso,atendida,rechazada',
            'prioridad'          => 'required|in:alta,media,baja',
            'fecha_emision'      => 'nullable|date',
            'fecha_limite'       => 'nullable|date',
            'numero_sgd'         => 'nullable|string|max:50',
            'origen'             => 'nullable|string|max:50',
            'observaciones'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $rec = Recomendacion::create($request->only([
            'titulo', 'descripcion', 'tipo', 'modulo', 'actividad_id', 'unidad_organica_id',
            'responsable_id', 'estado', 'prioridad', 'fecha_emision', 'fecha_limite',
            'numero_sgd', 'origen', 'observaciones',
        ]));

        return response()->json(['success' => true, 'id' => $rec->id], 201);
    }

    // ── Update ────────────────────────────────────────────────────────────────
    public function update(Request $request, Recomendacion $recomendacion)
    {
        Gate::authorize('recomendaciones.editar');

        $validator = Validator::make($request->all(), [
            'titulo'             => 'required|string|max:255',
            'descripcion'        => 'nullable|string',
            'tipo'               => 'required|in:observacion,recomendacion,mejora',
            'modulo'             => 'required|in:sci,integridad',
            'actividad_id'       => 'nullable|exists:actividades,id',
            'unidad_organica_id' => 'nullable|exists:unidades_organicas,id',
            'responsable_id'     => 'nullable|exists:users,id',
            'estado'             => 'required|in:pendiente,en_proceso,atendida,rechazada',
            'prioridad'          => 'required|in:alta,media,baja',
            'fecha_emision'      => 'nullable|date',
            'fecha_limite'       => 'nullable|date',
            'fecha_atencion'     => 'nullable|date',
            'numero_sgd'         => 'nullable|string|max:50',
            'origen'             => 'nullable|string|max:50',
            'observaciones'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only([
            'titulo', 'descripcion', 'tipo', 'modulo', 'actividad_id', 'unidad_organica_id',
            'responsable_id', 'estado', 'prioridad', 'fecha_emision', 'fecha_limite',
            'numero_sgd', 'origen', 'observaciones',
        ]);

        if ($request->estado === 'atendida' && !$recomendacion->fecha_atencion) {
            $data['fecha_atencion'] = now()->toDateString();
        }
        if ($request->filled('fecha_atencion')) {
            $data['fecha_atencion'] = $request->fecha_atencion;
        }

        $recomendacion->update($data);
        return response()->json(['success' => true]);
    }

    // ── Destroy ───────────────────────────────────────────────────────────────
    public function destroy(Recomendacion $recomendacion)
    {
        Gate::authorize('recomendaciones.eliminar');
        $recomendacion->delete();
        return response()->json(['success' => true]);
    }

    // ── Marcar atendida ───────────────────────────────────────────────────────
    public function marcarAtendida(Recomendacion $recomendacion)
    {
        $recomendacion->update([
            'estado'         => 'atendida',
            'fecha_atencion' => now()->toDateString(),
        ]);
        return response()->json(['success' => true]);
    }
}
