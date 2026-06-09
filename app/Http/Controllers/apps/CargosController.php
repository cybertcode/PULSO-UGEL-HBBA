<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use Illuminate\Http\Request;

class CargosController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('q') || $request->boolean('select')) {
            return response()->json(
                Cargo::activo()->orderBy('nombre')
                    ->when($request->q, fn($q, $v) => $q->where('nombre', 'like', "%$v%"))
                    ->get(['id', 'nombre'])
            );
        }
        $cargos = Cargo::withCount(['usuarios'])
            ->orderByDesc('created_at')
            ->get(['id', 'nombre', 'activo', 'created_at']);

        return response()->json($cargos->map(fn($c) => [
            'id'              => $c->id,
            'nombre'          => $c->nombre,
            'activo'          => $c->activo,
            'numero_usuarios' => $c->usuarios_count,
            'created_at'      => $c->created_at?->format('d/m/Y'),
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150|unique:cargos,nombre',
        ]);

        $cargo = Cargo::create($data);

        return response()->json($cargo, 201);
    }

    public function update(Request $request, Cargo $cargo)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150|unique:cargos,nombre,' . $cargo->id,
            'activo' => 'boolean',
        ]);

        $cargo->update($data);

        return response()->json($cargo);
    }

    public function destroy(Cargo $cargo)
    {
        if ($cargo->usuarios()->exists()) {
            return response()->json([
                'message' => "No se puede eliminar el cargo \"{$cargo->nombre}\" porque tiene {$cargo->usuarios()->count()} usuario(s) asignado(s). Reasigna o desvincula los usuarios primero.",
            ], 422);
        }

        $cargo->delete();

        return response()->json(['ok' => true]);
    }
}
