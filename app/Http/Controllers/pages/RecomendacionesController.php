<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Recomendacion;
use App\Models\Actividad;
use App\Models\UnidadOrganica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecomendacionesController extends Controller
{
    public function index(Request $request)
    {
        $query = Recomendacion::with(['unidadOrganica', 'responsable', 'actividad'])
            ->orderByDesc('created_at');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }
        if ($request->filled('unidad')) {
            $query->where('unidad_organica_id', $request->unidad);
        }
        if ($request->filled('buscar')) {
            $query->where('titulo', 'like', '%' . $request->buscar . '%');
        }

        $recomendaciones = $query->paginate(15)->withQueryString();

        $stats = [
            'total'       => Recomendacion::count(),
            'pendientes'  => Recomendacion::whereIn('estado', ['pendiente', 'en_proceso'])->count(),
            'atendidas'   => Recomendacion::where('estado', 'atendida')->count(),
            'vencidas'    => Recomendacion::whereIn('estado', ['pendiente', 'en_proceso'])
                                ->where('fecha_limite', '<', now())->count(),
            'alta_prior'  => Recomendacion::where('prioridad', 'alta')
                                ->whereIn('estado', ['pendiente', 'en_proceso'])->count(),
        ];

        $unidades    = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
        $usuarios    = User::orderBy('name')->get();
        $actividades = Actividad::orderBy('nombre')->get();

        $tipos = [
            'observacion'   => 'Observación',
            'recomendacion' => 'Recomendación',
            'mejora'        => 'Oportunidad de Mejora',
        ];

        $origenes = ['SCI', 'OCI', 'DRE', 'Auditoría', 'Autocontrol'];

        return view('content.recomendaciones.index', compact(
            'recomendaciones', 'stats', 'unidades', 'usuarios', 'actividades', 'tipos', 'origenes'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo'            => 'required|string|max:255',
            'descripcion'       => 'nullable|string',
            'tipo'              => 'required|in:observacion,recomendacion,mejora',
            'actividad_id'      => 'nullable|exists:actividades,id',
            'unidad_organica_id'=> 'nullable|exists:unidades_organicas,id',
            'responsable_id'    => 'nullable|exists:users,id',
            'estado'            => 'required|in:pendiente,en_proceso,atendida,rechazada',
            'prioridad'         => 'required|in:alta,media,baja',
            'fecha_emision'     => 'nullable|date',
            'fecha_limite'      => 'nullable|date',
            'numero_sgd'        => 'nullable|string|max:50',
            'origen'            => 'nullable|string|max:50',
            'observaciones'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Corrija los errores del formulario.');
        }

        Recomendacion::create($request->only([
            'titulo', 'descripcion', 'tipo', 'actividad_id', 'unidad_organica_id',
            'responsable_id', 'estado', 'prioridad', 'fecha_emision', 'fecha_limite',
            'numero_sgd', 'origen', 'observaciones',
        ]));

        return back()->with('success', 'Recomendación registrada correctamente.');
    }

    public function update(Request $request, Recomendacion $recomendacion)
    {
        $validator = Validator::make($request->all(), [
            'titulo'            => 'required|string|max:255',
            'descripcion'       => 'nullable|string',
            'tipo'              => 'required|in:observacion,recomendacion,mejora',
            'actividad_id'      => 'nullable|exists:actividades,id',
            'unidad_organica_id'=> 'nullable|exists:unidades_organicas,id',
            'responsable_id'    => 'nullable|exists:users,id',
            'estado'            => 'required|in:pendiente,en_proceso,atendida,rechazada',
            'prioridad'         => 'required|in:alta,media,baja',
            'fecha_emision'     => 'nullable|date',
            'fecha_limite'      => 'nullable|date',
            'fecha_atencion'    => 'nullable|date',
            'numero_sgd'        => 'nullable|string|max:50',
            'origen'            => 'nullable|string|max:50',
            'observaciones'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Corrija los errores del formulario.');
        }

        $data = $request->only([
            'titulo', 'descripcion', 'tipo', 'actividad_id', 'unidad_organica_id',
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
        return back()->with('success', 'Recomendación actualizada correctamente.');
    }

    public function destroy(Recomendacion $recomendacion)
    {
        $recomendacion->delete();
        return back()->with('success', 'Recomendación eliminada.');
    }

    public function marcarAtendida(Recomendacion $recomendacion)
    {
        $recomendacion->update([
            'estado'        => 'atendida',
            'fecha_atencion'=> now()->toDateString(),
        ]);
        return response()->json(['success' => true]);
    }
}
