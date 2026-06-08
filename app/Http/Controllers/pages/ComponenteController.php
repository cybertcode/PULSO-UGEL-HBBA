<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Componente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ComponenteController extends Controller
{
    private const CATEGORIAS = [
        'Ambiente de Control',
        'Evaluación de Riesgos',
        'Actividades de Control',
        'Información y Comunicación',
        'Supervisión y Monitoreo',
    ];

    private const ICONOS = [
        'tabler-crown', 'tabler-shield-check', 'tabler-chart-pie', 'tabler-chart-bar',
        'tabler-clipboard-list', 'tabler-alert-triangle', 'tabler-messages',
        'tabler-message-circle', 'tabler-eye', 'tabler-speakerphone', 'tabler-activity',
        'tabler-user-check', 'tabler-users', 'tabler-building', 'tabler-file-certificate',
        'tabler-scale', 'tabler-lock', 'tabler-target', 'tabler-trending-up',
        'tabler-checkup-list', 'tabler-puzzle', 'tabler-compass', 'tabler-flag',
        'tabler-microscope',
    ];

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
            'nombre'      => 'required|string|max:255',
            'icono'       => ['nullable', Rule::in(self::ICONOS)],
            'tipo'        => ['nullable', Rule::in(self::CATEGORIAS)],
            'descripcion' => 'nullable|string|max:1000',
            'activo'      => 'boolean',
        ]);

        $validated['activo'] = $request->boolean('activo', true);
        $validated['numero'] = (Componente::max('numero') ?? 0) + 1;

        Componente::create($validated);

        return back()->with('success', 'Componente creado correctamente.');
    }

    public function update(Request $request, Componente $componente)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:255',
            'icono'       => ['nullable', Rule::in(self::ICONOS)],
            'tipo'        => ['nullable', Rule::in(self::CATEGORIAS)],
            'descripcion' => 'nullable|string|max:1000',
            'activo'      => 'boolean',
        ]);

        $validated['activo'] = $request->boolean('activo', $componente->activo);

        $componente->update($validated);

        return back()->with('success', 'Componente actualizado correctamente.');
    }

    public function toggle(Componente $componente)
    {
        $componente->update(['activo' => !$componente->activo]);

        if (request()->expectsJson()) {
            return response()->json(['activo' => $componente->activo]);
        }

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
