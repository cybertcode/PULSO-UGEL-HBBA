<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use Illuminate\Http\Request;

class CargosController extends Controller
{
    public function index()
    {
        return response()->json(Cargo::activo()->orderBy('nombre')->get(['id', 'nombre']));
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
        $cargo->delete();

        return response()->json(['ok' => true]);
    }
}
