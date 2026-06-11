<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\IntegridadEtapa;
use App\Models\IntegridadComponente;
use App\Models\IntegridadPregunta;
use Illuminate\Http\Request;

class IntegridadEstructuraController extends Controller
{
    // ── Vista principal ──────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $anio  = $request->input('anio', now()->year);
        $anios = IntegridadEtapa::selectRaw('DISTINCT anio')->orderByDesc('anio')->pluck('anio');

        $etapas = IntegridadEtapa::with(['componentes.preguntas'])
            ->where('anio', $anio)
            ->orderBy('orden')
            ->get();

        return view('content.administracion.integridad-estructura.index', compact('etapas', 'anio', 'anios'));
    }

    // ── ETAPAS ───────────────────────────────────────────────────────────────

    public function storeEtapa(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'anio'        => 'required|integer|min:2020|max:2099',
            'activo'      => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo', true);
        $data['orden']  = (IntegridadEtapa::where('anio', $data['anio'])->max('orden') ?? 0) + 1;

        IntegridadEtapa::create($data);
        return back()->with('success', "Etapa «{$data['nombre']}» creada.");
    }

    public function updateEtapa(Request $request, IntegridadEtapa $etapa)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'anio'        => 'required|integer|min:2020|max:2099',
            'activo'      => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo', $etapa->activo);
        $etapa->update($data);
        return back()->with('success', 'Etapa actualizada.');
    }

    public function destroyEtapa(IntegridadEtapa $etapa)
    {
        if ($etapa->componentes()->exists()) {
            return back()->with('error', 'No se puede eliminar: la etapa tiene componentes asociados.');
        }
        $etapa->delete();
        return back()->with('success', 'Etapa eliminada.');
    }

    // ── COMPONENTES ──────────────────────────────────────────────────────────

    public function storeComponente(Request $request)
    {
        $data = $request->validate([
            'etapa_id'    => 'required|exists:integridad_etapas,id',
            'nombre'      => 'required|string|max:255',
            'icono'       => 'nullable|string|max:80',
            'descripcion' => 'nullable|string|max:1000',
            'activo'      => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo', true);
        $data['orden']  = (IntegridadComponente::where('etapa_id', $data['etapa_id'])->max('orden') ?? 0) + 1;

        IntegridadComponente::create($data);
        return back()->with('success', "Componente «{$data['nombre']}» creado.");
    }

    public function updateComponente(Request $request, IntegridadComponente $componente)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'icono'       => 'nullable|string|max:80',
            'descripcion' => 'nullable|string|max:1000',
            'activo'      => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo', $componente->activo);
        $componente->update($data);
        return back()->with('success', 'Componente actualizado.');
    }

    public function destroyComponente(IntegridadComponente $componente)
    {
        if ($componente->preguntas()->exists()) {
            return back()->with('error', 'No se puede eliminar: el componente tiene preguntas asociadas.');
        }
        $componente->delete();
        return back()->with('success', 'Componente eliminado.');
    }

    // ── PREGUNTAS ────────────────────────────────────────────────────────────

    public function storePregunta(Request $request)
    {
        $data = $request->validate([
            'componente_id' => 'required|exists:integridad_componentes,id',
            'nombre'        => 'required|string|max:500',
            'link_ficha'    => 'nullable|url|max:500',
            'activo'        => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo', true);
        $data['orden']  = (IntegridadPregunta::where('componente_id', $data['componente_id'])->max('orden') ?? 0) + 1;

        IntegridadPregunta::create($data);
        return back()->with('success', 'Pregunta registrada.');
    }

    public function updatePregunta(Request $request, IntegridadPregunta $pregunta)
    {
        $data = $request->validate([
            'nombre'     => 'required|string|max:500',
            'link_ficha' => 'nullable|url|max:500',
            'activo'     => 'boolean',
        ]);
        $data['activo'] = $request->boolean('activo', $pregunta->activo);
        $pregunta->update($data);
        return back()->with('success', 'Pregunta actualizada.');
    }

    public function destroyPregunta(IntegridadPregunta $pregunta)
    {
        if ($pregunta->actividades()->exists()) {
            return back()->with('error', 'No se puede eliminar: la pregunta tiene actividades asociadas.');
        }
        $pregunta->delete();
        return back()->with('success', 'Pregunta eliminada.');
    }

    // ── API para cascada AJAX ────────────────────────────────────────────────

    public function apiEtapas(Request $request)
    {
        $anio = $request->input('anio', now()->year);
        return response()->json(
            IntegridadEtapa::where('anio', $anio)->where('activo', true)->orderBy('orden')->get(['id', 'nombre'])
        );
    }

    public function apiComponentes(Request $request)
    {
        $request->validate(['etapa_id' => 'required|exists:integridad_etapas,id']);
        return response()->json(
            IntegridadComponente::where('etapa_id', $request->etapa_id)->where('activo', true)->orderBy('orden')->get(['id', 'nombre'])
        );
    }

    public function apiPreguntas(Request $request)
    {
        $request->validate(['componente_id' => 'required|exists:integridad_componentes,id']);
        return response()->json(
            IntegridadPregunta::where('componente_id', $request->componente_id)->where('activo', true)->orderBy('orden')->get(['id', 'nombre', 'link_ficha'])
        );
    }
}
