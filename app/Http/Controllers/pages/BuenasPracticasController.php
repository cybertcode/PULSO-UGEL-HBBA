<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\BuenaPractica;
use App\Models\UnidadOrganica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BuenasPracticasController extends Controller
{
    public function index(Request $request)
    {
        $query = BuenaPractica::with(['unidadOrganica', 'responsable'])
            ->orderByDesc('created_at');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
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

        $stats = [
            'total'            => BuenaPractica::count(),
            'completadas'      => BuenaPractica::where('estado', 'completada')->count(),
            'en_implementacion'=> BuenaPractica::where('estado', 'en_implementacion')->count(),
            'promedio_avance'  => (int) round(BuenaPractica::avg('avance') ?? 0),
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
            'practicas', 'stats', 'unidades', 'usuarios', 'categorias'
        ));
    }

    public function store(Request $request)
    {
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

    public function update(Request $request, BuenaPractica $buenaPractica)
    {
        $validator = Validator::make($request->all(), [
            'titulo'            => 'required|string|max:255',
            'descripcion'       => 'nullable|string',
            'categoria'         => 'required|in:gestion,transparencia,integridad,innovacion,participacion',
            'unidad_organica_id'=> 'nullable|exists:unidades_organicas,id',
            'responsable_id'    => 'nullable|exists:users,id',
            'estado'            => 'required|in:en_implementacion,completada,pendiente,suspendida',
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
        $buenaPractica->delete();
        return back()->with('success', 'Buena práctica eliminada.');
    }

    public function updateAvance(Request $request, BuenaPractica $buenaPractica)
    {
        $request->validate(['avance' => 'required|integer|min:0|max:100']);
        $buenaPractica->update(['avance' => $request->avance]);
        if ($request->avance == 100) {
            $buenaPractica->update(['estado' => 'completada']);
        }
        return response()->json(['success' => true, 'avance' => $buenaPractica->avance]);
    }
}
