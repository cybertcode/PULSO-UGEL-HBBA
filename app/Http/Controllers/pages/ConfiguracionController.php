<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionInstitucional;
use App\Models\UnidadOrganica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $config   = ConfiguracionInstitucional::firstOrCreate([], [
            'nombre_institucion' => 'UGEL Huacaybamba',
            'sigla'              => 'UGEL-HCB',
            'region'             => 'Huánuco',
            'provincia'          => 'Huacaybamba',
        ]);
        $unidades = UnidadOrganica::orderBy('nombre')->get();

        return view('content.configuracion.index', compact('config', 'unidades'));
    }

    public function update(Request $request)
    {
        $config = ConfiguracionInstitucional::firstOrFail();

        $validated = $request->validate([
            'nombre_institucion'      => 'required|string|max:255',
            'sigla'                   => 'required|string|max:30',
            'ugel_codigo'             => 'nullable|string|max:20',
            'region'                  => 'required|string|max:100',
            'provincia'               => 'required|string|max:100',
            'director'                => 'nullable|string|max:255',
            'coordinador_sci'         => 'nullable|string|max:255',
            'correo_institucional'    => 'nullable|email|max:255',
            'telefono'                => 'nullable|string|max:20',
            'anio_gestion'            => 'nullable|integer|min:2020|max:2099',
            'umbral_verde'            => 'required|integer|min:1|max:100',
            'umbral_amarillo'         => 'required|integer|min:1|max:100',
            'notif_vencimiento'       => 'boolean',
            'notif_dias_anticipacion' => 'required|integer|min:1|max:30',
            'notif_avance_bajo'       => 'boolean',
            'notif_umbral_avance'     => 'required|integer|min:1|max:100',
            'notif_email'             => 'boolean',
            'logo'                    => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($config->logo_ruta) Storage::disk('public')->delete($config->logo_ruta);
            $validated['logo_ruta'] = $request->file('logo')->store('logos', 'public');
        }
        unset($validated['logo']);

        $validated['notif_vencimiento'] = $request->boolean('notif_vencimiento');
        $validated['notif_avance_bajo'] = $request->boolean('notif_avance_bajo');
        $validated['notif_email']       = $request->boolean('notif_email');

        $config->update($validated);

        return back()->with('success', 'Configuración guardada correctamente.');
    }

    // CRUD Unidades Orgánicas
    public function storeUnidad(Request $request)
    {
        $validated = $request->validate([
            'codigo'      => 'required|string|max:20|unique:unidades_organicas,codigo',
            'nombre'      => 'required|string|max:255',
            'sigla'       => 'nullable|string|max:20',
            'responsable' => 'nullable|string|max:255',
        ]);
        UnidadOrganica::create(array_merge($validated, ['activo' => true]));
        return back()->with('success', 'Unidad orgánica creada.');
    }

    public function updateUnidad(Request $request, UnidadOrganica $unidad)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:255',
            'sigla'       => 'nullable|string|max:20',
            'responsable' => 'nullable|string|max:255',
            'activo'      => 'boolean',
        ]);
        $validated['activo'] = $request->boolean('activo');
        $unidad->update($validated);
        return back()->with('success', 'Unidad orgánica actualizada.');
    }
}
