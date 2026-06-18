<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\InstitucionVinculada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InstitucionVinculadaController extends Controller
{
    public function index()
    {
        $instituciones = InstitucionVinculada::orderBy('orden')->orderBy('id')->get();
        return view('content.instituciones-vinculadas.index', compact('instituciones'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'       => 'required|string|max:255',
            'sigla'        => 'required|string|max:30',
            'logo_file'    => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'logo_url'     => 'nullable|url|max:500',
            'color_acento' => 'nullable|string|max:20',
            'url_sitio'    => 'nullable|url|max:1000',
            'descripcion'  => 'nullable|string|max:500',
            'orden'        => 'nullable|integer|min:0',
            'activo'       => 'boolean',
        ]);

        if ($request->hasFile('logo_file')) {
            $data['logo_ruta'] = $request->file('logo_file')
                ->store('instituciones-vinculadas', 'public');
            $data['logo_url'] = null;
        }
        unset($data['logo_file']);

        $data['orden']  ??= InstitucionVinculada::max('orden') + 1;
        $data['activo']   = $request->boolean('activo', true);

        InstitucionVinculada::create($data);

        return redirect()->route('instituciones-vinculadas.index')
            ->with('success', 'Institución creada correctamente.');
    }

    public function update(Request $request, InstitucionVinculada $institucionVinculada)
    {
        $data = $request->validate([
            'nombre'         => 'required|string|max:255',
            'sigla'          => 'required|string|max:30',
            'logo_file'      => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'logo_url'       => 'nullable|url|max:500',
            'eliminar_logo'  => 'nullable|boolean',
            'color_acento'   => 'nullable|string|max:20',
            'url_sitio'      => 'nullable|url|max:1000',
            'descripcion'    => 'nullable|string|max:500',
            'orden'          => 'nullable|integer|min:0',
            'activo'         => 'boolean',
        ]);

        if ($request->hasFile('logo_file')) {
            if ($institucionVinculada->logo_ruta) {
                Storage::disk('public')->delete($institucionVinculada->logo_ruta);
            }
            $data['logo_ruta'] = $request->file('logo_file')
                ->store('instituciones-vinculadas', 'public');
            $data['logo_url'] = null;
        } elseif ($request->boolean('eliminar_logo')) {
            if ($institucionVinculada->logo_ruta) {
                Storage::disk('public')->delete($institucionVinculada->logo_ruta);
            }
            $data['logo_ruta'] = null;
        }

        unset($data['logo_file'], $data['eliminar_logo']);
        $data['activo'] = $request->boolean('activo');
        $institucionVinculada->update($data);

        return redirect()->route('instituciones-vinculadas.index')
            ->with('success', 'Institución actualizada correctamente.');
    }

    public function destroy(InstitucionVinculada $institucionVinculada)
    {
        if ($institucionVinculada->logo_ruta) {
            Storage::disk('public')->delete($institucionVinculada->logo_ruta);
        }
        $institucionVinculada->delete();
        return redirect()->route('instituciones-vinculadas.index')
            ->with('success', 'Institución eliminada.');
    }

    public function toggleActivo(InstitucionVinculada $institucionVinculada)
    {
        $institucionVinculada->update(['activo' => !$institucionVinculada->activo]);
        return response()->json(['activo' => $institucionVinculada->fresh()->activo]);
    }
}
