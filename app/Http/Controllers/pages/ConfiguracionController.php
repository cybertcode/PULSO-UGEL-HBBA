<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionInstitucional;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $config = ConfiguracionInstitucional::firstOrCreate([], [
            'nombre_institucion' => 'Mi Institución',
            'sigla'              => 'MI-INST',
            'departamento'       => 'Lima',
            'provincia'          => 'Lima',
            'distrito'           => 'Lima',
            'ubigeo'             => '150101',
            'timezone'           => 'America/Lima',
            'anio_gestion'       => date('Y'),
            'umbral_verde'       => 75,
            'umbral_amarillo'    => 50,
        ]);

        return view('content.configuracion.index', compact('config'));
    }

    public function update(Request $request)
    {
        $config = ConfiguracionInstitucional::firstOrFail();

        $validated = $request->validate([
            'nombre_institucion'      => 'required|string|max:255',
            'sigla'                   => 'required|string|max:30',
            'ugel_codigo'             => 'nullable|string|max:20',
            'region'                  => 'nullable|string|max:100',
            'provincia'               => 'nullable|string|max:100',
            'departamento'            => 'nullable|string|max:100',
            'distrito'                => 'nullable|string|max:100',
            'ubigeo'                  => 'nullable|string|max:10',
            'direccion'               => 'nullable|string|max:255',
            'sitio_web'               => 'nullable|url|max:255',
            'timezone'                => 'nullable|string|max:50',
            'director'                => 'nullable|string|max:255',
            'coordinador_sci'         => 'nullable|string|max:255',
            'correo_institucional'    => 'nullable|email|max:255',
            'telefono'                => 'nullable|string|max:20',
            'anio_gestion'            => 'nullable|integer|min:2020|max:2099',
            'umbral_verde'            => 'required|integer|min:1|max:100',
            'umbral_amarillo'         => 'required|integer|min:1|max:100',
            'notif_vencimiento'       => 'boolean',
            'notif_dias_anticipacion' => 'nullable|integer|min:1|max:30',
            'notif_avance_bajo'       => 'boolean',
            'notif_umbral_avance'     => 'nullable|integer|min:1|max:100',
            'notif_email'             => 'boolean',
            'logo'                    => 'nullable|image|mimes:' . \App\Services\ImageService::ALLOWED_MIMES . '|max:' . \App\Services\ImageService::MAX_SIZE_KB,
            'remove_logo'             => 'nullable|boolean',
        ]);

        $images = app(ImageService::class);

        if ($request->boolean('remove_logo') && $config->logo_ruta) {
            $images->delete($config->logo_ruta);
            $validated['logo_ruta'] = null;
        }

        if ($request->hasFile('logo')) {
            $images->delete($config->logo_ruta);
            $validated['logo_ruta'] = $images->store($request->file('logo'), 'logos', maxWidth: 400);
        }

        unset($validated['logo'], $validated['remove_logo']);

        $validated['notif_vencimiento'] = $request->boolean('notif_vencimiento');
        $validated['notif_avance_bajo'] = $request->boolean('notif_avance_bajo');
        $validated['notif_email']       = $request->boolean('notif_email');
        $validated['notif_dias_anticipacion'] = $validated['notif_dias_anticipacion'] ?? $config->notif_dias_anticipacion;
        $validated['notif_umbral_avance']     = $validated['notif_umbral_avance'] ?? $config->notif_umbral_avance;

        $config->update($validated);

        // Invalidar caché de configuración
        \Illuminate\Support\Facades\Cache::forget('config_institucional');

        return back()->with('success', 'Configuración guardada correctamente.');
    }

}
