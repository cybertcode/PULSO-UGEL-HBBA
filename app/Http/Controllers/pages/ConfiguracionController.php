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
            'logo'                    => 'nullable|image|mimes:' . ImageService::ALLOWED_MIMES . '|max:' . ImageService::MAX_SIZE_KB,
            'remove_logo'             => 'nullable|boolean',
            'favicon'                 => 'nullable|image|mimes:' . ImageService::ALLOWED_MIMES . '|max:10240',
            'remove_favicon'          => 'nullable|boolean',
        ], [
            'nombre_institucion.required' => 'El nombre de la institución es obligatorio.',
            'nombre_institucion.max'      => 'El nombre de la institución no puede superar los 255 caracteres.',
            'sigla.required'              => 'La sigla es obligatoria.',
            'sigla.max'                   => 'La sigla no puede superar los 30 caracteres.',
            'ugel_codigo.max'             => 'El código UGEL no puede superar los 20 caracteres.',
            'region.max'                  => 'La región no puede superar los 100 caracteres.',
            'provincia.max'               => 'La provincia no puede superar los 100 caracteres.',
            'departamento.max'            => 'El departamento no puede superar los 100 caracteres.',
            'distrito.max'                => 'El distrito no puede superar los 100 caracteres.',
            'ubigeo.max'                  => 'El ubigeo no puede superar los 10 caracteres.',
            'direccion.max'               => 'La dirección no puede superar los 255 caracteres.',
            'sitio_web.url'               => 'El sitio web debe ser una URL válida (ej: https://www.ugel.gob.pe).',
            'sitio_web.max'               => 'El sitio web no puede superar los 255 caracteres.',
            'director.max'                => 'El nombre del director no puede superar los 255 caracteres.',
            'coordinador_sci.max'         => 'El nombre del coordinador SCI no puede superar los 255 caracteres.',
            'correo_institucional.email'  => 'El correo institucional debe tener un formato válido.',
            'correo_institucional.max'    => 'El correo institucional no puede superar los 255 caracteres.',
            'telefono.max'                => 'El teléfono no puede superar los 20 caracteres.',
            'anio_gestion.integer'        => 'El año de gestión debe ser un número entero.',
            'anio_gestion.min'            => 'El año de gestión no puede ser menor a 2020.',
            'anio_gestion.max'            => 'El año de gestión no puede ser mayor a 2099.',
            'umbral_verde.required'       => 'El umbral verde es obligatorio.',
            'umbral_verde.integer'        => 'El umbral verde debe ser un número entero.',
            'umbral_verde.min'            => 'El umbral verde debe ser al menos 1%.',
            'umbral_verde.max'            => 'El umbral verde no puede superar el 100%.',
            'umbral_amarillo.required'    => 'El umbral amarillo es obligatorio.',
            'umbral_amarillo.integer'     => 'El umbral amarillo debe ser un número entero.',
            'umbral_amarillo.min'         => 'El umbral amarillo debe ser al menos 1%.',
            'umbral_amarillo.max'         => 'El umbral amarillo no puede superar el 100%.',
            'notif_dias_anticipacion.integer' => 'Los días de anticipación deben ser un número entero.',
            'notif_dias_anticipacion.min'     => 'Los días de anticipación deben ser al menos 1.',
            'notif_dias_anticipacion.max'     => 'Los días de anticipación no pueden superar los 30 días.',
            'notif_umbral_avance.integer'     => 'El umbral de avance debe ser un número entero.',
            'notif_umbral_avance.min'         => 'El umbral de avance debe ser al menos 1%.',
            'notif_umbral_avance.max'         => 'El umbral de avance no puede superar el 100%.',
            'logo.image'                  => 'El logo debe ser una imagen válida.',
            'logo.mimes'                  => 'El logo debe estar en formato JPG, PNG, GIF, SVG o WebP.',
            'logo.max'                    => 'El logo no puede superar los 2 MB.',
            'favicon.image'               => 'El favicon debe ser una imagen válida.',
            'favicon.mimes'               => 'El favicon debe estar en formato JPG, PNG, GIF, SVG o WebP.',
            'favicon.max'                 => 'El favicon no puede superar los 5 MB.',
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

        if ($request->boolean('remove_favicon') && $config->favicon_ruta) {
            $images->delete($config->favicon_ruta);
            $validated['favicon_ruta'] = null;
        }

        if ($request->hasFile('favicon')) {
            $images->delete($config->favicon_ruta);
            $validated['favicon_ruta'] = $images->store($request->file('favicon'), 'favicons', maxWidth: 64, quality: 90);
        }

        unset($validated['logo'], $validated['remove_logo'], $validated['favicon'], $validated['remove_favicon']);

        $validated['notif_vencimiento'] = $request->boolean('notif_vencimiento');
        $validated['notif_avance_bajo'] = $request->boolean('notif_avance_bajo');
        $validated['notif_email']       = $request->boolean('notif_email');
        $validated['notif_dias_anticipacion'] ??= $config->notif_dias_anticipacion;
        $validated['notif_umbral_avance']     ??= $config->notif_umbral_avance;

        $config->update($validated);

        // Invalidar caché de configuración
        \Illuminate\Support\Facades\Cache::forget('config_institucional');

        return back()->with('success', 'Configuración guardada correctamente.');
    }

}
