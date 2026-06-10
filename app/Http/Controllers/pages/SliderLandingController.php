<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\SliderLanding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderLandingController extends Controller
{
    public function index()
    {
        $slides = SliderLanding::orderBy('orden')->orderBy('id')->get();
        return view('content.slider-landing.index', compact('slides'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo'               => 'required|in:noticia,evento,normativa',
            'titulo'             => 'required|string|max:255',
            'descripcion'        => 'nullable|string|max:1000',
            'contenido'          => 'nullable|string',
            'autor'              => 'nullable|string|max:100',
            'etiqueta'           => 'nullable|string|max:80',
            'color_gradiente'    => 'nullable|string|max:300',
            'imagen_file'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'imagen_portada_file'=> 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'url_accion'         => 'nullable|url|max:255',
            'texto_accion'       => 'nullable|string|max:80',
            'orden'              => 'nullable|integer|min:0',
            'activo'             => 'boolean',
        ]);

        if ($request->hasFile('imagen_file')) {
            $data['imagen_url'] = Storage::url(
                $request->file('imagen_file')->store('slider-landing', 'public')
            );
        }
        if ($request->hasFile('imagen_portada_file')) {
            $data['imagen_portada_url'] = Storage::url(
                $request->file('imagen_portada_file')->store('slider-landing', 'public')
            );
        }
        unset($data['imagen_file'], $data['imagen_portada_file']);

        $data['orden'] ??= SliderLanding::max('orden') + 1;
        $data['activo'] = $request->boolean('activo', true);

        SliderLanding::create($data);

        return redirect()->route('slider-landing.index')
            ->with('success', 'Slide creado correctamente.');
    }

    public function update(Request $request, SliderLanding $sliderLanding)
    {
        $data = $request->validate([
            'tipo'               => 'required|in:noticia,evento,normativa',
            'titulo'             => 'required|string|max:255',
            'descripcion'        => 'nullable|string|max:1000',
            'contenido'          => 'nullable|string',
            'autor'              => 'nullable|string|max:100',
            'etiqueta'           => 'nullable|string|max:80',
            'color_gradiente'    => 'nullable|string|max:300',
            'imagen_file'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'eliminar_imagen'    => 'nullable|boolean',
            'imagen_portada_file'=> 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'url_accion'         => 'nullable|url|max:255',
            'texto_accion'       => 'nullable|string|max:80',
            'orden'              => 'nullable|integer|min:0',
            'activo'             => 'boolean',
        ]);

        if ($request->hasFile('imagen_file')) {
            if ($sliderLanding->imagen_url && str_starts_with($sliderLanding->imagen_url, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $sliderLanding->imagen_url));
            }
            $data['imagen_url'] = Storage::url(
                $request->file('imagen_file')->store('slider-landing', 'public')
            );
        } elseif ($request->boolean('eliminar_imagen')) {
            if ($sliderLanding->imagen_url && str_starts_with($sliderLanding->imagen_url, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $sliderLanding->imagen_url));
            }
            $data['imagen_url'] = null;
        }

        if ($request->hasFile('imagen_portada_file')) {
            if ($sliderLanding->imagen_portada_url && str_starts_with($sliderLanding->imagen_portada_url, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $sliderLanding->imagen_portada_url));
            }
            $data['imagen_portada_url'] = Storage::url(
                $request->file('imagen_portada_file')->store('slider-landing', 'public')
            );
        }

        unset($data['imagen_file'], $data['eliminar_imagen'], $data['imagen_portada_file']);
        $data['activo'] = $request->boolean('activo');
        $sliderLanding->update($data);

        return redirect()->route('slider-landing.index')
            ->with('success', 'Slide actualizado correctamente.');
    }

    public function destroy(SliderLanding $sliderLanding)
    {
        // Eliminar imagen del storage al borrar el slide
        if ($sliderLanding->imagen_url && str_starts_with($sliderLanding->imagen_url, '/storage/')) {
            Storage::disk('public')->delete(
                str_replace('/storage/', '', $sliderLanding->imagen_url)
            );
        }
        $sliderLanding->delete();
        return redirect()->route('slider-landing.index')
            ->with('success', 'Slide eliminado.');
    }

    public function toggleActivo(SliderLanding $sliderLanding)
    {
        $sliderLanding->update(['activo' => !$sliderLanding->activo]);
        return response()->json(['activo' => $sliderLanding->fresh()->activo]);
    }
}
