<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Exports\EncuestaResultadosExport;
use App\Models\Encuesta;
use App\Models\EncuestaRespuesta;
use App\Models\EncuestaRespuestaDetalle;
use Barryvdh\DomPDF\Facade\Pdf;
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

            if (in_array($pregunta->tipo, ['opcion_multiple', 'seleccion_multiple', 'desplegable'])) {
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

            } elseif (in_array($pregunta->tipo, ['si_no', 'verdadero_falso'])) {
                $opciones = $pregunta->tipo === 'si_no'
                    ? ['si' => 'Sí', 'no' => 'No']
                    : ['verdadero' => 'Verdadero', 'falso' => 'Falso'];

                $conteos = EncuestaRespuestaDetalle::where('pregunta_id', $pregunta->id)
                    ->select('texto_respuesta', DB::raw('COUNT(*) as total'))
                    ->groupBy('texto_respuesta')
                    ->get()
                    ->keyBy('texto_respuesta');

                $item['labels'] = array_values($opciones);
                $item['data']   = array_map(fn($k) => $conteos->get($k)?->total ?? 0, array_keys($opciones));

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

        // Tabla de participantes — respondieron
        $respuestas = EncuestaRespuesta::where('encuesta_id', $encuesta->id)
            ->with('usuario:id,name,dni,unidad_organica_id')
            ->orderByDesc('completada')
            ->orderByDesc('completada_at')
            ->get();

        $respondieronIds = $respuestas->pluck('usuario_id')->filter()->unique();

        $participantes = $respuestas->map(fn($r) => [
            'usuario'       => $r->usuario->name ?? 'Desconocido',
            'dni'           => $r->usuario->dni  ?? '-',
            'completada'    => $r->completada,
            'completada_at' => $r->completada_at?->format('d/m/Y H:i'),
            'iniciada_at'   => $r->iniciada_at?->format('d/m/Y H:i'),
        ]);

        // Quienes NO respondieron (destinatarios que no tienen registro)
        $encuesta->load('destinatarios');
        $todosDestinatariosIds = $encuesta->resolverDestinatarios();
        $pendientes = \App\Models\User::whereIn('id', $todosDestinatariosIds)
            ->whereNotIn('id', $respondieronIds)
            ->where('estado', 'activo')
            ->get(['id','name','dni'])
            ->map(fn($u) => [
                'usuario' => $u->name,
                'dni'     => $u->dni ?? '-',
            ]);

        return response()->json([
            'preguntas'     => $resultado,
            'participantes' => $participantes,
            'pendientes'    => $pendientes,
        ]);
    }

    public function exportar(Encuesta $encuesta)
    {
        $filename = 'encuesta-' . $encuesta->id . '-resultados.xlsx';
        return Excel::download(new EncuestaResultadosExport($encuesta), $filename);
    }

    public function exportarPdf(Encuesta $encuesta)
    {
        $encuesta->load(['preguntas.opciones', 'destinatarios']);

        // ── Preguntas con datos ──
        $preguntas = [];
        foreach ($encuesta->preguntas as $pregunta) {
            $item = [
                'id'    => $pregunta->id,
                'texto' => $pregunta->texto,
                'tipo'  => $pregunta->tipo,
            ];

            if (in_array($pregunta->tipo, ['opcion_multiple', 'seleccion_multiple', 'desplegable'])) {
                $conteos = EncuestaRespuestaDetalle::where('pregunta_id', $pregunta->id)
                    ->select('opcion_id', DB::raw('COUNT(*) as total'))
                    ->groupBy('opcion_id')->get()->keyBy('opcion_id');
                $item['labels'] = $pregunta->opciones->pluck('texto')->toArray();
                $item['data']   = $pregunta->opciones->map(fn($o) => $conteos->get($o->id)?->total ?? 0)->toArray();

            } elseif (in_array($pregunta->tipo, ['si_no', 'verdadero_falso'])) {
                $keys   = $pregunta->tipo === 'si_no' ? ['si','no'] : ['verdadero','falso'];
                $conteos = EncuestaRespuestaDetalle::where('pregunta_id', $pregunta->id)
                    ->select('texto_respuesta', DB::raw('COUNT(*) as total'))
                    ->groupBy('texto_respuesta')->get()->keyBy('texto_respuesta');
                $item['labels'] = $pregunta->tipo === 'si_no' ? ['Sí','No'] : ['Verdadero','Falso'];
                $item['data']   = array_map(fn($k) => $conteos->get($k)?->total ?? 0, $keys);

            } elseif ($pregunta->tipo === 'escala') {
                $conteos = EncuestaRespuestaDetalle::where('pregunta_id', $pregunta->id)
                    ->select('texto_respuesta', DB::raw('COUNT(*) as total'))
                    ->groupBy('texto_respuesta')->get()->keyBy('texto_respuesta');
                $labels = ['1','2','3','4','5'];
                $data   = array_map(fn($v) => $conteos->get($v)?->total ?? 0, $labels);
                $totalR = array_sum($data);
                $item['labels']   = $labels;
                $item['data']     = $data;
                $item['promedio'] = $totalR > 0
                    ? round(array_sum(array_map(fn($i) => ($i + 1) * $data[$i], array_keys($data))) / $totalR, 2)
                    : 0;

            } elseif ($pregunta->tipo === 'texto_libre') {
                $item['data'] = [];
                $item['respuestas'] = EncuestaRespuestaDetalle::where('pregunta_id', $pregunta->id)
                    ->with('respuesta.usuario:id,name,dni')
                    ->get()
                    ->map(fn($d) => [
                        'usuario'   => $d->respuesta->usuario->name ?? 'Anónimo',
                        'respuesta' => $d->texto_respuesta,
                        'fecha'     => $d->created_at->format('d/m/Y'),
                    ])->toArray();
            }

            $preguntas[] = $item;
        }

        // ── Respondieron ──
        $respuestas = EncuestaRespuesta::where('encuesta_id', $encuesta->id)
            ->with('usuario:id,name,dni,unidad_organica_id', 'usuario.unidadOrganica:id,nombre')
            ->orderByDesc('completada')->orderByDesc('completada_at')
            ->get();

        $respondieron = $respuestas->map(fn($r) => [
            'usuario'       => $r->usuario->name ?? 'Desconocido',
            'dni'           => $r->usuario->dni  ?? '—',
            'unidad'        => $r->usuario->unidadOrganica->nombre ?? '—',
            'completada'    => $r->completada,
            'completada_at' => $r->completada_at?->format('d/m/Y H:i'),
            'iniciada_at'   => $r->iniciada_at?->format('d/m/Y H:i'),
        ])->toArray();

        // ── Pendientes ──
        $respondieronIds = $respuestas->pluck('usuario_id')->filter()->unique();
        $todosIds        = $encuesta->resolverDestinatarios();
        $pendientes = \App\Models\User::whereIn('id', $todosIds)
            ->whereNotIn('id', $respondieronIds)
            ->where('estado', 'activo')
            ->with('unidadOrganica:id,nombre')
            ->get(['id','name','dni','unidad_organica_id'])
            ->map(fn($u) => [
                'usuario' => $u->name,
                'dni'     => $u->dni ?? '—',
                'unidad'  => $u->unidadOrganica->nombre ?? '—',
            ])->toArray();

        $pdf = Pdf::loadView('content.encuestas.pdf-resultados', compact(
            'encuesta', 'preguntas', 'respondieron', 'pendientes'
        ))->setPaper('a4', 'portrait');

        $filename = 'encuesta-' . $encuesta->id . '-resultados.pdf';
        return $pdf->download($filename);
    }
}
