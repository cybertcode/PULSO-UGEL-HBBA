<?php

namespace App\Exports;

use App\Models\Encuesta;
use App\Models\EncuestaRespuesta;
use App\Models\EncuestaRespuestaDetalle;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EncuestaResultadosExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(protected Encuesta $encuesta) {}

    public function sheets(): array
    {
        return [
            new Sheets\EncuestaResumenSheet($this->encuesta),
            new Sheets\EncuestaDetalleSheet($this->encuesta),
        ];
    }
}

namespace App\Exports\Sheets;

use App\Models\Encuesta;
use App\Models\EncuestaRespuestaDetalle;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EncuestaResumenSheet implements FromArray, WithTitle, WithHeadings, WithStyles
{
    public function __construct(protected Encuesta $encuesta) {}

    public function title(): string
    {
        return 'Resumen por Pregunta';
    }

    public function headings(): array
    {
        return ['#', 'Pregunta', 'Tipo', 'Opción / Valor', 'Respuestas', '% del Total'];
    }

    public function array(): array
    {
        $this->encuesta->load(['preguntas.opciones']);
        $rows = [];

        foreach ($this->encuesta->preguntas as $i => $pregunta) {
            if (in_array($pregunta->tipo, ['opcion_multiple', 'seleccion_multiple'])) {
                $totalPregunta = EncuestaRespuestaDetalle::where('pregunta_id', $pregunta->id)->count();

                foreach ($pregunta->opciones as $opcion) {
                    $count = EncuestaRespuestaDetalle::where('pregunta_id', $pregunta->id)
                        ->where('opcion_id', $opcion->id)->count();

                    $rows[] = [
                        $i + 1,
                        $pregunta->texto,
                        $pregunta->tipo_label,
                        $opcion->texto,
                        $count,
                        $totalPregunta > 0 ? round(($count / $totalPregunta) * 100, 1) . '%' : '0%',
                    ];
                }

            } elseif ($pregunta->tipo === 'escala') {
                $totalPregunta = EncuestaRespuestaDetalle::where('pregunta_id', $pregunta->id)->count();

                for ($v = 1; $v <= 5; $v++) {
                    $count = EncuestaRespuestaDetalle::where('pregunta_id', $pregunta->id)
                        ->where('texto_respuesta', (string)$v)->count();

                    $rows[] = [
                        $i + 1,
                        $pregunta->texto,
                        'Escala (1-5)',
                        'Valor ' . $v,
                        $count,
                        $totalPregunta > 0 ? round(($count / $totalPregunta) * 100, 1) . '%' : '0%',
                    ];
                }

            } elseif ($pregunta->tipo === 'texto_libre') {
                $count = EncuestaRespuestaDetalle::where('pregunta_id', $pregunta->id)->count();
                $rows[] = [
                    $i + 1,
                    $pregunta->texto,
                    'Texto libre',
                    '(Ver hoja Detalle)',
                    $count,
                    '-',
                ];
            }
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class EncuestaDetalleSheet implements FromArray, WithTitle, WithHeadings, WithStyles
{
    public function __construct(protected Encuesta $encuesta) {}

    public function title(): string
    {
        return 'Detalle por Usuario';
    }

    public function headings(): array
    {
        return ['DNI', 'Usuario', 'Pregunta', 'Respuesta', 'Fecha'];
    }

    public function array(): array
    {
        $this->encuesta->load(['preguntas']);
        $rows = [];

        $respuestas = \App\Models\EncuestaRespuesta::where('encuesta_id', $this->encuesta->id)
            ->where('completada', true)
            ->with(['usuario:id,name,dni', 'detalles.pregunta', 'detalles.opcion'])
            ->get();

        foreach ($respuestas as $resp) {
            foreach ($resp->detalles as $detalle) {
                $respuestaTexto = $detalle->opcion
                    ? $detalle->opcion->texto
                    : ($detalle->texto_respuesta ?? '-');

                $rows[] = [
                    $resp->usuario->dni ?? '-',
                    $resp->usuario->name ?? 'Desconocido',
                    $detalle->pregunta->texto ?? '-',
                    $respuestaTexto,
                    $resp->completada_at?->format('d/m/Y H:i') ?? '-',
                ];
            }
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
