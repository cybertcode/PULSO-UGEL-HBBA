<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\BuenaPractica;
use App\Models\UnidadOrganica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class BuenasPracticasController extends Controller
{
    public function index(Request $request)
    {
        $esGestor = Gate::check('buenas-practicas.ver');
        $user     = Auth::user();

        // Gestor ve todas; usuario normal solo las suyas + las aprobadas/activas
        $query = BuenaPractica::with(['unidadOrganica', 'responsable', 'propuestoPor'])
            ->orderByDesc('created_at');

        if (!$esGestor) {
            // Ve las propias (cualquier estado) + las de otros que no sean "propuesta"
            $uid = $user->id;
            $query->where(fn($q) => $q
                ->where('propuesto_por', $uid)
                ->orWhereNot('estado', 'propuesta')
            );
        }

        // Filtros
        if ($request->filled('tab') && $request->tab === 'propuestas') {
            $query->where('estado', 'propuesta');
        } elseif (!$request->filled('tab') || $request->tab === 'todas') {
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            } else {
                // Por defecto excluir propuestas en la vista general (gestor las ve en su tab)
                if ($esGestor && !$request->filled('estado')) {
                    $query->where('estado', '!=', 'propuesta');
                }
            }
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }
        if ($request->filled('unidad')) {
            $query->where('unidad_organica_id', $request->unidad);
        }
        if ($request->filled('buscar')) {
            $query->where('titulo', 'like', '%' . $request->buscar . '%');
        }

        $practicas = $query->paginate(15)->withQueryString();

        $pendientesPropuestas = $esGestor
            ? BuenaPractica::where('estado', 'propuesta')->count()
            : 0;

        $stats = [
            'total'             => BuenaPractica::whereNot('estado', 'propuesta')->count(),
            'completadas'       => BuenaPractica::where('estado', 'completada')->count(),
            'en_implementacion' => BuenaPractica::where('estado', 'en_implementacion')->count(),
            'promedio_avance'   => (int) round(BuenaPractica::whereNot('estado', 'propuesta')->avg('avance') ?? 0),
            'propuestas'        => $pendientesPropuestas,
        ];

        $unidades   = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
        $usuarios   = User::orderBy('name')->get();
        $categorias = [
            'gestion'       => 'Gestión',
            'transparencia' => 'Transparencia',
            'integridad'    => 'Integridad',
            'innovacion'    => 'Innovación',
            'participacion' => 'Participación',
        ];

        return view('content.buenas-practicas.index', compact(
            'practicas', 'stats', 'unidades', 'usuarios', 'categorias',
            'esGestor', 'pendientesPropuestas'
        ));
    }

    // Cualquier usuario propone — estado inicial = propuesta
    public function proponer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo'            => 'required|string|max:255',
            'descripcion'       => 'nullable|string',
            'categoria'         => 'required|in:gestion,transparencia,integridad,innovacion,participacion',
            'unidad_organica_id'=> 'nullable|exists:unidades_organicas,id',
            'fecha_inicio'      => 'nullable|date',
            'fecha_termino'     => 'nullable|date|after_or_equal:fecha_inicio',
            'evidencias'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Corrija los errores del formulario.');
        }

        BuenaPractica::create([
            'titulo'             => $request->titulo,
            'descripcion'        => $request->descripcion,
            'categoria'          => $request->categoria,
            'unidad_organica_id' => $request->unidad_organica_id,
            'fecha_inicio'       => $request->fecha_inicio,
            'fecha_termino'      => $request->fecha_termino,
            'evidencias'         => $request->evidencias,
            'estado'             => 'propuesta',
            'avance'             => 0,
            'propuesto_por'      => Auth::id(),
            'creado_por'         => Auth::id(),
        ]);

        return back()->with('success', 'Propuesta enviada correctamente. El responsable la revisará pronto.');
    }

    // Gestor crea directamente (estado configurable)
    public function store(Request $request)
    {
        Gate::authorize('buenas-practicas.ver');

        $validator = Validator::make($request->all(), [
            'titulo'            => 'required|string|max:255',
            'descripcion'       => 'nullable|string',
            'categoria'         => 'required|in:gestion,transparencia,integridad,innovacion,participacion',
            'unidad_organica_id'=> 'nullable|exists:unidades_organicas,id',
            'responsable_id'    => 'nullable|exists:users,id',
            'estado'            => 'required|in:en_implementacion,completada,pendiente,suspendida',
            'avance'            => 'required|integer|min:0|max:100',
            'fecha_inicio'      => 'nullable|date',
            'fecha_termino'     => 'nullable|date|after_or_equal:fecha_inicio',
            'numero_sgd'        => 'nullable|string|max:50',
            'impacto'           => 'nullable|in:alto,medio,bajo',
            'evidencias'        => 'nullable|string',
            'observaciones'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Corrija los errores del formulario.');
        }

        BuenaPractica::create($request->only([
            'titulo', 'descripcion', 'categoria', 'unidad_organica_id',
            'responsable_id', 'estado', 'avance', 'fecha_inicio', 'fecha_termino',
            'numero_sgd', 'impacto', 'evidencias', 'observaciones',
        ]));

        return back()->with('success', 'Buena práctica registrada correctamente.');
    }

    // Gestor aprueba una propuesta (cambia estado y asigna responsable)
    public function aprobar(Request $request, BuenaPractica $buenaPractica)
    {
        Gate::authorize('buenas-practicas.ver');

        $request->validate([
            'estado'         => 'required|in:en_implementacion,pendiente',
            'responsable_id' => 'nullable|exists:users,id',
            'observaciones'  => 'nullable|string',
            'impacto'        => 'nullable|in:alto,medio,bajo',
        ]);

        $buenaPractica->update([
            'estado'         => $request->estado,
            'responsable_id' => $request->responsable_id,
            'observaciones'  => $request->observaciones,
            'impacto'        => $request->impacto,
        ]);

        return back()->with('success', 'Propuesta aprobada correctamente.');
    }

    // Gestor rechaza una propuesta
    public function rechazar(Request $request, BuenaPractica $buenaPractica)
    {
        Gate::authorize('buenas-practicas.ver');

        $request->validate(['observaciones' => 'nullable|string']);

        $buenaPractica->update([
            'estado'        => 'suspendida',
            'observaciones' => $request->observaciones ?? 'Propuesta rechazada.',
        ]);

        return back()->with('success', 'Propuesta rechazada.');
    }

    public function update(Request $request, BuenaPractica $buenaPractica)
    {
        Gate::authorize('buenas-practicas.ver');

        $validator = Validator::make($request->all(), [
            'titulo'            => 'required|string|max:255',
            'descripcion'       => 'nullable|string',
            'categoria'         => 'required|in:gestion,transparencia,integridad,innovacion,participacion',
            'unidad_organica_id'=> 'nullable|exists:unidades_organicas,id',
            'responsable_id'    => 'nullable|exists:users,id',
            'estado'            => 'required|in:propuesta,en_implementacion,completada,pendiente,suspendida',
            'avance'            => 'required|integer|min:0|max:100',
            'fecha_inicio'      => 'nullable|date',
            'fecha_termino'     => 'nullable|date',
            'numero_sgd'        => 'nullable|string|max:50',
            'impacto'           => 'nullable|in:alto,medio,bajo',
            'evidencias'        => 'nullable|string',
            'observaciones'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Corrija los errores del formulario.');
        }

        $buenaPractica->update($request->only([
            'titulo', 'descripcion', 'categoria', 'unidad_organica_id',
            'responsable_id', 'estado', 'avance', 'fecha_inicio', 'fecha_termino',
            'numero_sgd', 'impacto', 'evidencias', 'observaciones',
        ]));

        return back()->with('success', 'Buena práctica actualizada correctamente.');
    }

    public function destroy(BuenaPractica $buenaPractica)
    {
        Gate::authorize('buenas-practicas.ver');
        $buenaPractica->delete();
        return back()->with('success', 'Buena práctica eliminada.');
    }

    public function updateAvance(Request $request, BuenaPractica $buenaPractica)
    {
        Gate::authorize('buenas-practicas.ver');
        $request->validate(['avance' => 'required|integer|min:0|max:100']);
        $buenaPractica->update(['avance' => $request->avance]);
        if ($request->avance == 100) {
            $buenaPractica->update(['estado' => 'completada']);
        }
        return response()->json(['success' => true, 'avance' => $buenaPractica->avance]);
    }
}
