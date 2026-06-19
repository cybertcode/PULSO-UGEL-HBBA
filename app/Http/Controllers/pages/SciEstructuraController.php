<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\SciEje;
use App\Models\SciComponente;
use App\Models\SciPregunta;
use Illuminate\Http\Request;

class SciEstructuraController extends Controller
{
    private const ICONOS = [
        'tabler-crown', 'tabler-shield-check', 'tabler-chart-pie', 'tabler-chart-bar',
        'tabler-clipboard-list', 'tabler-alert-triangle', 'tabler-messages',
        'tabler-message-circle', 'tabler-eye', 'tabler-speakerphone', 'tabler-activity',
        'tabler-user-check', 'tabler-users', 'tabler-building', 'tabler-file-certificate',
        'tabler-scale', 'tabler-lock', 'tabler-target', 'tabler-trending-up',
        'tabler-checkup-list', 'tabler-puzzle', 'tabler-compass', 'tabler-flag',
        'tabler-microscope',
    ];

    // ── Vista principal ──────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $anio  = $request->input('anio', now()->year);
        $anios = SciEje::selectRaw('DISTINCT anio')->orderByDesc('anio')->pluck('anio');

        $ejes = SciEje::with(['componentes.preguntas'])
            ->where('anio', $anio)
            ->orderBy('orden')
            ->get();

        return view('content.administracion.sci-estructura.index', compact('ejes', 'anio', 'anios'));
    }

    // ── EJES ─────────────────────────────────────────────────────────────────

    public function storeEje(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'anio'        => 'required|integer|min:2020|max:2099',
            'activo'      => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo', true);
        $data['orden']  = (SciEje::where('anio', $data['anio'])->max('orden') ?? 0) + 1;

        $eje = SciEje::create($data);
        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => "Eje «{$eje->nombre}» creado.", 'eje' => $eje]);
        }
        return back()->with('success', "Eje «{$eje->nombre}» creado.");
    }

    public function updateEje(Request $request, SciEje $eje)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'anio'        => 'required|integer|min:2020|max:2099',
            'activo'      => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo', $eje->activo);
        $eje->update($data);
        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Eje actualizado.', 'eje' => $eje->fresh()]);
        }
        return back()->with('success', 'Eje actualizado.');
    }

    public function destroyEje(Request $request, SciEje $eje)
    {
        if ($eje->componentes()->exists()) {
            if ($request->expectsJson()) return response()->json(['ok' => false, 'message' => 'No se puede eliminar: el eje tiene componentes asociados.'], 422);
            return back()->with('error', 'No se puede eliminar: el eje tiene componentes asociados.');
        }
        $eje->delete();
        if ($request->expectsJson()) return response()->json(['ok' => true, 'message' => 'Eje eliminado.']);
        return back()->with('success', 'Eje eliminado.');
    }

    // ── COMPONENTES ──────────────────────────────────────────────────────────

    public function storeComponente(Request $request)
    {
        $data = $request->validate([
            'eje_id'      => 'required|exists:sci_ejes,id',
            'nombre'      => 'required|string|max:255',
            'icono'       => 'nullable|string|max:80',
            'descripcion' => 'nullable|string|max:1000',
            'activo'      => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo', true);
        $data['orden']  = (SciComponente::where('eje_id', $data['eje_id'])->max('orden') ?? 0) + 1;

        $comp = SciComponente::create($data);
        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => "Componente «{$comp->nombre}» creado.", 'componente' => array_merge($comp->toArray(), ['preguntas_count' => 0, 'url_destroy' => route('adm-sci.componente.destroy', $comp)])]);
        }
        return back()->with('success', "Componente «{$comp->nombre}» creado.");
    }

    public function updateComponente(Request $request, SciComponente $componente)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'icono'       => 'nullable|string|max:80',
            'descripcion' => 'nullable|string|max:1000',
            'activo'      => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo', $componente->activo);
        $componente->update($data);
        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Componente actualizado.', 'componente' => $componente->fresh()]);
        }
        return back()->with('success', 'Componente actualizado.');
    }

    public function reorderComponentes(Request $request)
    {
        $items = $request->validate(['items' => 'required|array', 'items.*.id' => 'required|exists:sci_componentes,id', 'items.*.orden' => 'required|integer|min:0'])['items'];
        foreach ($items as $item) {
            SciComponente::where('id', $item['id'])->update(['orden' => $item['orden']]);
        }
        return response()->json(['ok' => true]);
    }

    public function reorderPreguntas(Request $request)
    {
        $items = $request->validate(['items' => 'required|array', 'items.*.id' => 'required|exists:sci_preguntas,id', 'items.*.orden' => 'required|integer|min:0'])['items'];
        foreach ($items as $item) {
            SciPregunta::where('id', $item['id'])->update(['orden' => $item['orden']]);
        }
        return response()->json(['ok' => true]);
    }

    public function toggleEje(SciEje $eje)
    {
        $eje->update(['activo' => !$eje->activo]);
        return response()->json([
            'ok'     => true,
            'activo' => $eje->activo,
            'message'=> $eje->activo ? "Eje «{$eje->nombre}» activado." : "Eje «{$eje->nombre}» desactivado.",
        ]);
    }

    public function destroyComponente(Request $request, SciComponente $componente)
    {
        if ($componente->preguntas()->exists()) {
            if ($request->expectsJson()) return response()->json(['ok' => false, 'message' => 'No se puede eliminar: el componente tiene preguntas asociadas.'], 422);
            return back()->with('error', 'No se puede eliminar: el componente tiene preguntas asociadas.');
        }
        $componente->delete();
        if ($request->expectsJson()) return response()->json(['ok' => true, 'message' => 'Componente eliminado.']);
        return back()->with('success', 'Componente eliminado.');
    }

    // ── PREGUNTAS ────────────────────────────────────────────────────────────

    public function storePregunta(Request $request)
    {
        $data = $request->validate([
            'componente_id' => 'required|exists:sci_componentes,id',
            'nombre'        => 'required|string|min:1|max:1000',
            'link_ficha'    => 'nullable|url|max:1000',
            'activo'        => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo', true);
        $data['orden']  = (SciPregunta::where('componente_id', $data['componente_id'])->max('orden') ?? 0) + 1;

        $preg = SciPregunta::create($data);
        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Pregunta registrada.', 'pregunta' => array_merge($preg->toArray(), ['url_destroy' => route('adm-sci.pregunta.destroy', $preg)])]);
        }
        return back()->with('success', 'Pregunta registrada.');
    }

    public function updatePregunta(Request $request, SciPregunta $pregunta)
    {
        $data = $request->validate([
            'nombre'     => 'required|string|min:1|max:1000',
            'link_ficha' => 'nullable|url|max:1000',
            'activo'     => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo', $pregunta->activo);
        $pregunta->update($data);
        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Pregunta actualizada.', 'pregunta' => $pregunta->fresh()]);
        }
        return back()->with('success', 'Pregunta actualizada.');
    }

    public function destroyPregunta(Request $request, SciPregunta $pregunta)
    {
        if ($pregunta->actividades()->exists()) {
            if ($request->expectsJson()) return response()->json(['ok' => false, 'message' => 'No se puede eliminar: la pregunta tiene actividades asociadas.'], 422);
            return back()->with('error', 'No se puede eliminar: la pregunta tiene actividades asociadas.');
        }
        $pregunta->delete();
        if ($request->expectsJson()) return response()->json(['ok' => true, 'message' => 'Pregunta eliminada.']);
        return back()->with('success', 'Pregunta eliminada.');
    }

    // ── API para cascada AJAX ────────────────────────────────────────────────

    public function apiEjes(Request $request)
    {
        $anio = $request->input('anio', now()->year);
        return response()->json(
            SciEje::where('anio', $anio)->where('activo', true)->orderBy('orden')->get(['id', 'nombre'])
        );
    }

    public function apiComponentes(Request $request)
    {
        $request->validate(['eje_id' => 'required|exists:sci_ejes,id']);
        return response()->json(
            SciComponente::where('eje_id', $request->eje_id)->where('activo', true)->orderBy('orden')->get(['id', 'nombre'])
        );
    }

    public function apiPreguntas(Request $request)
    {
        $request->validate(['componente_id' => 'required|exists:sci_componentes,id']);
        return response()->json(
            SciPregunta::where('componente_id', $request->componente_id)->where('activo', true)->orderBy('orden')->get(['id', 'nombre', 'link_ficha'])
        );
    }
}
