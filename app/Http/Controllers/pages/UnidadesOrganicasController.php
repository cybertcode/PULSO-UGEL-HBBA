<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\UnidadOrganica;
use Illuminate\Http\Request;

class UnidadesOrganicasController extends Controller
{
    public function index()
    {
        $unidades = UnidadOrganica::orderBy('nombre')->get();
        return view('content.unidades-organicas.index', compact('unidades'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo'      => 'required|string|max:20|unique:unidades_organicas,codigo',
            'nombre'      => 'required|string|max:255',
            'sigla'       => 'nullable|string|max:20',
            'responsable' => 'nullable|string|max:255',
        ]);
        UnidadOrganica::create(array_merge($validated, ['activo' => true]));
        return back()->with('success', 'Unidad orgánica creada correctamente.');
    }

    public function update(Request $request, UnidadOrganica $unidad)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:255',
            'sigla'       => 'nullable|string|max:20',
            'responsable' => 'nullable|string|max:255',
            'activo'      => 'nullable|boolean',
        ]);
        $validated['activo'] = $request->boolean('activo', $unidad->activo);
        $unidad->update($validated);
        return back()->with('success', 'Unidad orgánica actualizada correctamente.');
    }

    public function toggle(UnidadOrganica $unidad)
    {
        $unidad->update(['activo' => !$unidad->activo]);
        return back()->with('success', 'Estado de la unidad actualizado.');
    }

    public function destroy(UnidadOrganica $unidad)
    {
        $unidad->delete();
        return back()->with('success', 'Unidad orgánica eliminada.');
    }
}
