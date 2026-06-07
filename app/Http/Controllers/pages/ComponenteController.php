<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Componente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ComponenteController extends Controller
{
    public function index()
    {
        $componentes = Componente::withCount([
            'actividades',
            'actividades as completadas_count' => fn($q) => $q->where('estado', 'completada'),
        ])->orderBy('numero')->get();

        return view('content.administracion.componentes.index', compact('componentes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero'      => 'required|integer|min:1|unique:componentes,numero',
            'nombre'      => 'required|string|max:255',
            'icono'       => 'nullable|string|max:80',
            'tipo'        => 'nullable|string|max:80',
            'descripcion' => 'nullable|string',
            'activo'      => 'boolean',
        ]);

        $validated['activo'] = $request->boolean('activo', true);

        Componente::create($validated);

        return back()->with('success', 'Componente creado correctamente.');
    }

    public function update(Request $request, Componente $componente)
    {
        $validated = $request->validate([
            'numero'      => ['required', 'integer', 'min:1', Rule::unique('componentes', 'numero')->ignore($componente)],
            'nombre'      => 'required|string|max:255',
            'icono'       => 'nullable|string|max:80',
            'tipo'        => 'nullable|string|max:80',
            'descripcion' => 'nullable|string',
            'activo'      => 'boolean',
        ]);

        $validated['activo'] = $request->boolean('activo', $componente->activo);

        $componente->update($validated);

        return back()->with('success', 'Componente actualizado correctamente.');
    }

    public function toggle(Componente $componente)
    {
        $componente->update(['activo' => !$componente->activo]);
        $estado = $componente->activo ? 'activado' : 'desactivado';

        return back()->with('success', "Componente {$estado} correctamente.");
    }

    public function destroy(Componente $componente)
    {
        if ($componente->actividades()->exists()) {
            return back()->with('error', 'No se puede eliminar: el componente tiene actividades asociadas.');
        }

        $componente->delete();

        return back()->with('success', 'Componente eliminado correctamente.');
    }
}
