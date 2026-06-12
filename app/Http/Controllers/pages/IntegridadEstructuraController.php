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

        $etapas = IntegridadEtapa::with(['componentes'])
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

        $etapa = IntegridadEtapa::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'ok'      => true,
                'message' => "Etapa «{$etapa->nombre}» creada.",
                'etapa'   => [
                    'id'          => $etapa->id,
                    'nombre'      => $etapa->nombre,
                    'descripcion' => $etapa->descripcion,
                    'anio'        => $etapa->anio,
                    'activo'      => $etapa->activo,
                ],
            ]);
        }
        return back()->with('success', "Etapa «{$etapa->nombre}» creada.");
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

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Etapa actualizada.', 'etapa' => $etapa->fresh()]);
        }
        return back()->with('success', 'Etapa actualizada.');
    }

    public function destroyEtapa(Request $request, IntegridadEtapa $etapa)
    {
        if ($etapa->componentes()->exists()) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'No se puede eliminar: la etapa tiene componentes asociados.'], 422);
            }
            return back()->with('error', 'No se puede eliminar: la etapa tiene componentes asociados.');
        }
        $etapa->delete();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Etapa eliminada.']);
        }
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

        $comp = IntegridadComponente::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'ok'      => true,
                'message' => "Componente «{$comp->nombre}» creado.",
                'componente' => [
                    'id'             => $comp->id,
                    'nombre'         => $comp->nombre,
                    'icono'          => $comp->icono,
                    'descripcion'    => $comp->descripcion,
                    'activo'         => $comp->activo,
                    'preguntas_count'=> 0,
                    'url_destroy'    => route('adm-integridad.componente.destroy', $comp),
                ],
            ]);
        }
        return back()->with('success', "Componente «{$comp->nombre}» creado.");
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

        if ($request->expectsJson()) {
            $componente->refresh();
            return response()->json([
                'ok'      => true,
                'message' => 'Componente actualizado.',
                'componente' => [
                    'id'          => $componente->id,
                    'nombre'      => $componente->nombre,
                    'icono'       => $componente->icono,
                    'descripcion' => $componente->descripcion,
                    'activo'      => $componente->activo,
                ],
            ]);
        }
        return back()->with('success', 'Componente actualizado.');
    }

    public function destroyComponente(Request $request, IntegridadComponente $componente)
    {
        if ($componente->preguntas()->exists()) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'No se puede eliminar: el componente tiene preguntas asociadas.'], 422);
            }
            return back()->with('error', 'No se puede eliminar: el componente tiene preguntas asociadas.');
        }
        $componente->delete();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Componente eliminado.']);
        }
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

        $preg = IntegridadPregunta::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'ok'      => true,
                'message' => 'Pregunta registrada.',
                'pregunta' => [
                    'id'          => $preg->id,
                    'nombre'      => $preg->nombre,
                    'link_ficha'  => $preg->link_ficha,
                    'activo'      => $preg->activo,
                    'url_destroy' => route('adm-integridad.pregunta.destroy', $preg),
                ],
            ]);
        }
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

        if ($request->expectsJson()) {
            $pregunta->refresh();
            return response()->json([
                'ok'      => true,
                'message' => 'Pregunta actualizada.',
                'pregunta' => [
                    'id'         => $pregunta->id,
                    'nombre'     => $pregunta->nombre,
                    'link_ficha' => $pregunta->link_ficha,
                    'activo'     => $pregunta->activo,
                ],
            ]);
        }
        return back()->with('success', 'Pregunta actualizada.');
    }

    public function destroyPregunta(Request $request, IntegridadPregunta $pregunta)
    {
        if ($pregunta->actividades()->exists()) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'No se puede eliminar: la pregunta tiene actividades asociadas.'], 422);
            }
            return back()->with('error', 'No se puede eliminar: la pregunta tiene actividades asociadas.');
        }
        $pregunta->delete();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Pregunta eliminada.']);
        }
        return back()->with('success', 'Pregunta eliminada.');
    }

    // ── API admin para panel dinámico ────────────────────────────────────────

    public function apiComponentesAdmin(Request $request)
    {
        $request->validate(['etapa_id' => 'required|exists:integridad_etapas,id']);
        $componentes = IntegridadComponente::withCount('preguntas')
            ->where('etapa_id', $request->input('etapa_id'))
            ->orderBy('orden')
            ->get();

        return response()->json($componentes->map(fn($c) => [
            'id'              => $c->id,
            'nombre'          => $c->nombre,
            'icono'           => $c->icono,
            'descripcion'     => $c->descripcion,
            'activo'          => $c->activo,
            'preguntas_count' => $c->preguntas_count,
            'url_destroy'     => route('adm-integridad.componente.destroy', $c),
        ]));
    }

    public function apiPreguntasAdmin(Request $request)
    {
        $request->validate(['componente_id' => 'required|exists:integridad_componentes,id']);
        $preguntas = IntegridadPregunta::where('componente_id', $request->input('componente_id'))
            ->orderBy('orden')
            ->get();

        return response()->json($preguntas->map(fn($p) => [
            'id'          => $p->id,
            'nombre'      => $p->nombre,
            'link_ficha'  => $p->link_ficha,
            'activo'      => $p->activo,
            'url_destroy' => route('adm-integridad.pregunta.destroy', $p),
        ]));
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
            IntegridadComponente::where('etapa_id', $request->input('etapa_id'))->where('activo', true)->orderBy('orden')->get(['id', 'nombre'])
        );
    }

    public function apiPreguntas(Request $request)
    {
        $request->validate(['componente_id' => 'required|exists:integridad_componentes,id']);
        return response()->json(
            IntegridadPregunta::where('componente_id', $request->input('componente_id'))->where('activo', true)->orderBy('orden')->get(['id', 'nombre', 'link_ficha'])
        );
    }
}
