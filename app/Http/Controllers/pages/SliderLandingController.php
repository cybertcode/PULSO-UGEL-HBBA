<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\SliderLanding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SliderLandingController extends Controller
{
    public function index()
    {
        $slides    = SliderLanding::orderBy('orden')->orderBy('id')->get();
        $user      = Auth::user();
        $autorName = $user?->name ?? '';
        $autorCargo = $user?->cargo?->nombre ?? $user?->cargo?->nombre_cargo ?? null;
        return view('content.slider-landing.index', compact('slides', 'autorName', 'autorCargo'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo'         => 'required|in:noticia,evento,normativa',
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string|max:1000',
            'contenido'    => 'nullable|string',
            'autor'        => 'nullable|string|max:100',
            'etiqueta'     => 'nullable|string|max:80',
            'color_fondo'  => 'nullable|string|max:7',   // color hex simple
            'imagen_file'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'eliminar_imagen' => 'nullable|boolean',
            'url_accion'   => 'nullable|url|max:255',
            'texto_accion' => 'nullable|string|max:80',
            'orden'        => 'nullable|integer|min:0',
            'activo'       => 'boolean',
        ]);

        // Generar degradado a partir del color elegido
        if (!empty($data['color_fondo'])) {
            $hex = $data['color_fondo'];
            $data['color_gradiente'] = "linear-gradient(135deg, {$hex}dd 0%, {$hex} 60%, {$hex}bb 100%)";
        }
        unset($data['color_fondo']);

        if ($request->hasFile('imagen_file')) {
            $data['imagen_url'] = Storage::url(
                $request->file('imagen_file')->store('slider-landing', 'public')
            );
        }
        unset($data['imagen_file'], $data['eliminar_imagen']);

        $data['orden']  ??= SliderLanding::max('orden') + 1;
        $data['activo']   = $request->boolean('activo', true);

        // Autor por defecto = usuario logueado
        if (empty($data['autor'])) {
            $data['autor'] = Auth::user()?->name;
        }

        SliderLanding::create($data);

        return redirect()->route('slider-landing.index')
            ->with('success', 'Publicación creada correctamente.');
    }

    public function update(Request $request, SliderLanding $sliderLanding)
    {
        $data = $request->validate([
            'tipo'         => 'required|in:noticia,evento,normativa',
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string|max:1000',
            'contenido'    => 'nullable|string',
            'autor'        => 'nullable|string|max:100',
            'etiqueta'     => 'nullable|string|max:80',
            'color_fondo'  => 'nullable|string|max:7',
            'imagen_file'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'eliminar_imagen' => 'nullable|boolean',
            'url_accion'   => 'nullable|url|max:255',
            'texto_accion' => 'nullable|string|max:80',
            'orden'        => 'nullable|integer|min:0',
            'activo'       => 'boolean',
        ]);

        // Actualizar degradado si cambia el color
        if (!empty($data['color_fondo'])) {
            $hex = $data['color_fondo'];
            $data['color_gradiente'] = "linear-gradient(135deg, {$hex}dd 0%, {$hex} 60%, {$hex}bb 100%)";
        }
        unset($data['color_fondo']);

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

        unset($data['imagen_file'], $data['eliminar_imagen']);
        $data['activo'] = $request->boolean('activo');

        if (empty($data['autor'])) {
            $data['autor'] = Auth::user()?->name;
        }

        $sliderLanding->update($data);

        return redirect()->route('slider-landing.index')
            ->with('success', 'Publicación actualizada correctamente.');
    }

    public function destroy(SliderLanding $sliderLanding)
    {
        if ($sliderLanding->imagen_url && str_starts_with($sliderLanding->imagen_url, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $sliderLanding->imagen_url));
        }
        $sliderLanding->delete();
        return redirect()->route('slider-landing.index')
            ->with('success', 'Publicación eliminada.');
    }

    public function toggleActivo(SliderLanding $sliderLanding)
    {
        $sliderLanding->update(['activo' => !$sliderLanding->activo]);
        return response()->json(['activo' => $sliderLanding->fresh()->activo]);
    }
}
