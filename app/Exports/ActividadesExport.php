<?php

namespace App\Exports;

use App\Models\Actividad;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ActividadesExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function __construct(
        private int $anio,
        private ?string $modulo,
        private ?string $estado,
        private ?int $unidadId,
    ) {}

    public function collection()
    {
        $query = Actividad::with(['unidadOrganica', 'responsables'])
            ->whereYear('created_at', $this->anio);

        if ($this->modulo)   $query->where('modulo', $this->modulo);
        if ($this->estado)   $query->where('estado', $this->estado);
        if ($this->unidadId) $query->where('unidad_organica_id', $this->unidadId);

        return $query->orderBy('fecha_limite')->get()->map(fn($a) => [
            $a->codigo ?? $a->id,
            $a->nombre,
            $a->modulo === 'sci' ? 'Control Interno (SCI)' : 'Modelo de Integridad',
            $a->unidadOrganica->nombre ?? '—',
            $a->responsables->pluck('name')->implode(', ') ?: '—',
            ucfirst($a->estado),
            $a->prioridad ? ucfirst($a->prioridad) : '—',
            $a->fecha_limite?->format('d/m/Y') ?? '—',
            $a->avance . '%',
            $a->created_at->format('d/m/Y'),
        ]);
    }

    public function headings(): array
    {
        return ['Código', 'Actividad', 'Módulo', 'Unidad Orgánica', 'Responsable', 'Estado', 'Prioridad', 'Fecha Límite', '% Avance', 'Registrado'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF696CFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 14, 'B' => 40, 'C' => 24, 'D' => 28, 'E' => 24, 'F' => 14, 'G' => 12, 'H' => 14, 'I' => 10, 'J' => 14];
    }

    public function title(): string
    {
        return 'Actividades ' . $this->anio;
    }
}
