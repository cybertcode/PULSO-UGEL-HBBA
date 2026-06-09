<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\UnidadOrganica;
use App\Models\User;
use Illuminate\Http\Request;

class UnidadesOrganicasController extends Controller
{
    private function usuariosParaSelect()
    {
        return User::with('cargo')
            ->whereIn('estado', ['activo', 'pendiente'])
            ->orderBy('name')
            ->get(['id', 'name', 'cargo_id']);
    }

    public function index()
    {
        $unidades = UnidadOrganica::with('responsable.cargo')->orderBy('nombre')->get();
        $usuarios = $this->usuariosParaSelect();
        return view('content.unidades-organicas.index', compact('unidades', 'usuarios'));
    }

    private function generarCodigo(string $nombre): string
    {
        // Toma las iniciales de cada palabra (máx 6 chars), en mayúsculas
        $palabras = preg_split('/\s+/', trim($nombre));
        $base = strtoupper(implode('', array_map(fn($p) => substr($p, 0, 1), $palabras)));
        $base = substr(preg_replace('/[^A-Z0-9]/', '', $base), 0, 6);

        if (!UnidadOrganica::where('codigo', $base)->exists()) {
            return $base;
        }

        $i = 2;
        while (UnidadOrganica::where('codigo', $base . $i)->exists()) {
            $i++;
        }
        return $base . $i;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'         => 'required|string|max:255',
            'sigla'          => 'nullable|string|max:20',
            'responsable_id' => 'nullable|exists:users,id',
            'correo'         => 'nullable|email|max:100',
            'telefono'       => 'nullable|string|max:20',
            'descripcion'    => 'nullable|string|max:500',
        ]);

        $validated['codigo'] = $this->generarCodigo($validated['nombre']);
        $validated['activo'] = true;

        UnidadOrganica::create($validated);
        return back()->with('success', 'Unidad orgánica creada correctamente.');
    }

    public function update(Request $request, UnidadOrganica $unidad)
    {
        $validated = $request->validate([
            'nombre'         => 'required|string|max:255',
            'sigla'          => 'nullable|string|max:20',
            'responsable_id' => 'nullable|exists:users,id',
            'correo'         => 'nullable|email|max:100',
            'telefono'       => 'nullable|string|max:20',
            'descripcion'    => 'nullable|string|max:500',
            'activo'         => 'nullable|boolean',
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
