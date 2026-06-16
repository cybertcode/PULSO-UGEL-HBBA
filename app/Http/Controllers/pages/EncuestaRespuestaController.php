<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Encuesta;
use App\Models\EncuestaRespuesta;
use App\Models\EncuestaRespuestaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EncuestaRespuestaController extends Controller
{
    public function show(Encuesta $encuesta)
    {
        abort_if(!in_array($encuesta->estado, ['publicada', 'cerrada']), 403, 'Esta encuesta no está disponible.');

        $respuesta = EncuestaRespuesta::where('encuesta_id', $encuesta->id)
            ->where('usuario_id', Auth::id())
            ->with('detalles.opcion')
            ->first();

        abort_if(!$respuesta, 403, 'No estás asignado a esta encuesta.');

        $soloLectura = $respuesta->completada || $encuesta->estado === 'cerrada';

        // Si aún puede responder, verificar plazo
        if (!$soloLectura && $encuesta->fecha_fin && $encuesta->fecha_fin->isPast()) {
            $soloLectura = true;
        }

        // Marcar como iniciada si es la primera vez que abre el formulario
        if (!$respuesta->iniciada_at && !$soloLectura) {
            $respuesta->update(['iniciada_at' => now()]);
        }

        $encuesta->load(['preguntas.opciones']);

        return view('content.encuestas.show-responder', compact('encuesta', 'respuesta', 'soloLectura'));
    }

    public function store(Request $request, Encuesta $encuesta)
    {
        abort_if($encuesta->estado !== 'publicada', 403);

        $respuesta = EncuestaRespuesta::where('encuesta_id', $encuesta->id)
            ->where('usuario_id', Auth::id())
            ->firstOrFail();

        abort_if($respuesta->completada, 403, 'Ya respondiste esta encuesta.');

        $encuesta->load(['preguntas.opciones']);

        // Validar preguntas requeridas
        foreach ($encuesta->preguntas->where('requerida', true) as $pregunta) {
            $campo = 'respuesta_' . $pregunta->id;
            if (empty($request->input($campo))) {
                return back()->withErrors([$campo => 'Esta pregunta es obligatoria.'])->withInput();
            }
        }

        DB::transaction(function () use ($request, $encuesta, $respuesta) {
            foreach ($encuesta->preguntas as $pregunta) {
                $campo = 'respuesta_' . $pregunta->id;
                $valor = $request->input($campo);

                if (empty($valor)) continue;

                if ($pregunta->tipo === 'seleccion_multiple') {
                    // Múltiples filas, una por opción seleccionada
                    foreach ((array)$valor as $opcionId) {
                        EncuestaRespuestaDetalle::create([
                            'respuesta_id'    => $respuesta->id,
                            'pregunta_id'     => $pregunta->id,
                            'opcion_id'       => $opcionId,
                            'texto_respuesta' => null,
                        ]);
                    }
                } elseif (in_array($pregunta->tipo, ['opcion_multiple', 'desplegable'])) {
                    // Opción única o desplegable → guarda el opcion_id
                    EncuestaRespuestaDetalle::create([
                        'respuesta_id'    => $respuesta->id,
                        'pregunta_id'     => $pregunta->id,
                        'opcion_id'       => $valor,
                        'texto_respuesta' => null,
                    ]);
                } else {
                    // escala, texto_libre, si_no, verdadero_falso → guarda texto
                    EncuestaRespuestaDetalle::create([
                        'respuesta_id'    => $respuesta->id,
                        'pregunta_id'     => $pregunta->id,
                        'opcion_id'       => null,
                        'texto_respuesta' => $valor,
                    ]);
                }
            }

            $respuesta->update([
                'completada'    => true,
                'completada_at' => now(),
            ]);
        });

        return redirect()->route('encuestas.index')
            ->with('success', '¡Gracias por responder! Tu respuesta fue registrada correctamente.');
    }
}
