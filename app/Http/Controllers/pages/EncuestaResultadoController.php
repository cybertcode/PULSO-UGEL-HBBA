<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Exports\EncuestaResultadosExport;
use App\Models\Encuesta;
use App\Models\EncuestaRespuesta;
use App\Models\EncuestaRespuestaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class EncuestaResultadoController extends Controller
{
    public function index(Encuesta $encuesta)
    {
        $encuesta->load(['preguntas.opciones', 'creador']);

        $totalDestinatarios = EncuestaRespuesta::where('encuesta_id', $encuesta->id)->count();
        $totalCompletadas   = EncuestaRespuesta::where('encuesta_id', $encuesta->id)
            ->where('completada', true)->count();

        $porcentaje = $totalDestinatarios > 0
            ? round(($totalCompletadas / $totalDestinatarios) * 100, 1)
            : 0;

        return view('content.encuestas.resultados', compact(
            'encuesta', 'totalDestinatarios', 'totalCompletadas', 'porcentaje'
        ));
    }

    public function datos(Encuesta $encuesta)
    {
        $encuesta->load(['preguntas.opciones']);

        $resultado = [];

        foreach ($encuesta->preguntas as $pregunta) {
            $item = [
                'id'    => $pregunta->id,
                'texto' => $pregunta->texto,
                'tipo'  => $pregunta->tipo,
                'tipo_label' => $pregunta->tipo_label,
            ];

            if (in_array($pregunta->tipo, ['opcion_multiple', 'seleccion_multiple'])) {
                $conteos = EncuestaRespuestaDetalle::where('pregunta_id', $pregunta->id)
                    ->select('opcion_id', DB::raw('COUNT(*) as total'))
                    ->groupBy('opcion_id')
                    ->get()
                    ->keyBy('opcion_id');

                $labels = [];
                $data   = [];
                foreach ($pregunta->opciones as $opcion) {
                    $labels[] = $opcion->texto;
                    $data[]   = $conteos->get($opcion->id)?->total ?? 0;
                }

                $item['labels'] = $labels;
                $item['data']   = $data;

            } elseif ($pregunta->tipo === 'escala') {
                $conteos = EncuestaRespuestaDetalle::where('pregunta_id', $pregunta->id)
                    ->select('texto_respuesta', DB::raw('COUNT(*) as total'))
                    ->groupBy('texto_respuesta')
                    ->get()
                    ->keyBy('texto_respuesta');

                $labels = ['1', '2', '3', '4', '5'];
                $data   = array_map(fn($v) => $conteos->get($v)?->total ?? 0, $labels);

                $totalResp  = array_sum($data);
                $promedio   = $totalResp > 0
                    ? round(array_sum(array_map(fn($i) => ($i + 1) * $data[$i], array_keys($data))) / $totalResp, 2)
                    : 0;

                $item['labels']   = $labels;
                $item['data']     = $data;
                $item['promedio'] = $promedio;

            } elseif ($pregunta->tipo === 'texto_libre') {
                $respuestas = EncuestaRespuestaDetalle::where('pregunta_id', $pregunta->id)
                    ->with('respuesta.usuario:id,name,dni')
                    ->get()
                    ->map(fn($d) => [
                        'usuario'  => $d->respuesta->usuario->name ?? 'Anónimo',
                        'respuesta' => $d->texto_respuesta,
                        'fecha'    => $d->created_at->format('d/m/Y'),
                    ]);

                $item['respuestas'] = $respuestas;
            }

            $resultado[] = $item;
        }

        // Tabla de participantes
        $participantes = EncuestaRespuesta::where('encuesta_id', $encuesta->id)
            ->with('usuario:id,name,dni')
            ->orderByDesc('completada')
            ->orderByDesc('completada_at')
            ->get()
            ->map(fn($r) => [
                'usuario'       => $r->usuario->name ?? 'Desconocido',
                'dni'           => $r->usuario->dni ?? '-',
                'completada'    => $r->completada,
                'completada_at' => $r->completada_at?->format('d/m/Y H:i'),
            ]);

        return response()->json([
            'preguntas'     => $resultado,
            'participantes' => $participantes,
        ]);
    }

    public function exportar(Encuesta $encuesta)
    {
        $filename = 'encuesta-' . $encuesta->id . '-resultados.xlsx';
        return Excel::download(new EncuestaResultadosExport($encuesta), $filename);
    }
}
