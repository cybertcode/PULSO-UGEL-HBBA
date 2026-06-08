<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Componente;
use App\Models\MatrizRiesgo;
use App\Models\UnidadOrganica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MatrizRiesgosController extends Controller
{
    public function index(Request $request)
    {
        $query = MatrizRiesgo::with(['componente', 'unidadOrganica', 'responsable']);

        if ($request->filled('clasificacion')) $query->where('clasificacion', $request->clasificacion);
        if ($request->filled('tipo'))          $query->where('tipo', $request->tipo);
        if ($request->filled('estado'))        $query->where('estado', $request->estado);
        if ($request->filled('componente'))    $query->where('componente_id', $request->componente);
        if ($request->filled('buscar'))        $query->where('nombre', 'like', '%' . $request->buscar . '%');

        $riesgos = $query->orderByDesc('probabilidad')->orderByDesc('impacto')->paginate(15)->withQueryString();

        $stats = [
            'total'    => MatrizRiesgo::count(),
            'criticos' => MatrizRiesgo::where('clasificacion', 'critico')->count(),
            'altos'    => MatrizRiesgo::where('clasificacion', 'alto')->count(),
            'activos'  => MatrizRiesgo::where('estado', 'activo')->count(),
        ];

        $componentes   = Componente::where('activo', true)->orderBy('numero')->get();
        $unidades      = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
        $usuarios      = User::orderBy('name')->get(['id', 'name']);
        $anio_actual   = now()->year;

        return view('content.matriz-riesgos.index', compact(
            'riesgos', 'stats', 'componentes', 'unidades', 'usuarios', 'anio_actual'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'                 => 'required|string|max:255',
            'codigo'                 => 'nullable|string|max:20',
            'descripcion'            => 'nullable|string',
            'componente_id'          => 'nullable|exists:componentes,id',
            'unidad_organica_id'     => 'nullable|exists:unidades_organicas,id',
            'tipo'                   => 'required|in:estrategico,operativo,cumplimiento,reporte,tecnologico',
            'probabilidad'           => 'required|integer|min:1|max:5',
            'impacto'                => 'required|integer|min:1|max:5',
            'controles_existentes'   => 'nullable|string',
            'acciones_tratamiento'   => 'nullable|string',
            'tipo_tratamiento'       => 'required|in:mitigar,aceptar,transferir,evitar',
            'responsable_id'         => 'nullable|exists:users,id',
            'fecha_revision'         => 'nullable|date',
            'estado'                 => 'required|in:activo,mitigado,aceptado,cerrado',
            'observaciones'          => 'nullable|string',
            'anio'                   => 'nullable|integer|min:2020|max:2050',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Corrija los errores.');
        }

        MatrizRiesgo::create($request->only([
            'nombre', 'codigo', 'descripcion', 'componente_id', 'unidad_organica_id',
            'tipo', 'probabilidad', 'impacto', 'controles_existentes',
            'acciones_tratamiento', 'tipo_tratamiento', 'responsable_id',
            'fecha_revision', 'estado', 'observaciones', 'anio',
        ]));

        return back()->with('success', 'Riesgo registrado correctamente.');
    }

    public function update(Request $request, MatrizRiesgo $matrizRiesgo)
    {
        $validator = Validator::make($request->all(), [
            'nombre'                 => 'required|string|max:255',
            'codigo'                 => 'nullable|string|max:20',
            'descripcion'            => 'nullable|string',
            'componente_id'          => 'nullable|exists:componentes,id',
            'unidad_organica_id'     => 'nullable|exists:unidades_organicas,id',
            'tipo'                   => 'required|in:estrategico,operativo,cumplimiento,reporte,tecnologico',
            'probabilidad'           => 'required|integer|min:1|max:5',
            'impacto'                => 'required|integer|min:1|max:5',
            'controles_existentes'   => 'nullable|string',
            'acciones_tratamiento'   => 'nullable|string',
            'tipo_tratamiento'       => 'required|in:mitigar,aceptar,transferir,evitar',
            'responsable_id'         => 'nullable|exists:users,id',
            'fecha_revision'         => 'nullable|date',
            'estado'                 => 'required|in:activo,mitigado,aceptado,cerrado',
            'observaciones'          => 'nullable|string',
            'anio'                   => 'nullable|integer|min:2020|max:2050',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Corrija los errores.');
        }

        $matrizRiesgo->update($request->only([
            'nombre', 'codigo', 'descripcion', 'componente_id', 'unidad_organica_id',
            'tipo', 'probabilidad', 'impacto', 'controles_existentes',
            'acciones_tratamiento', 'tipo_tratamiento', 'responsable_id',
            'fecha_revision', 'estado', 'observaciones', 'anio',
        ]));

        return back()->with('success', 'Riesgo actualizado correctamente.');
    }

    public function destroy(MatrizRiesgo $matrizRiesgo)
    {
        $matrizRiesgo->delete();
        return back()->with('success', 'Riesgo eliminado.');
    }
}
