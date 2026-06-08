<?php

namespace App\Exports;

use App\Models\Actividad;
use App\Models\User;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class CumplimientoExport implements WithMultipleSheets
{
    public function __construct(
        private int     $anio,
        private ?int    $unidadId,
        private ?int    $componenteId,
    ) {}

    public function sheets(): array
    {
        return [
            new CumplimientoResponsablesSheet($this->anio, $this->unidadId, $this->componenteId),
            new CumplimientoSinEvidenciaSheet($this->anio, $this->unidadId, $this->componenteId),
        ];
    }
}

// ─── Hoja 1: Por Responsable ───────────────────────────────────────────────

class CumplimientoResponsablesSheet implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function __construct(
        private int  $anio,
        private ?int $unidadId,
        private ?int $componenteId,
    ) {}

    public function title(): string { return 'Por Responsable'; }

    public function collection()
    {
        $responsables = User::where('estado', 'activo')
            ->whereHas('actividadesResponsable')
            ->with('unidadOrganica')
            ->when($this->unidadId, fn($q) => $q->where('unidad_organica_id', $this->unidadId))
            ->get()
            ->map(function (User $user) {
                $base = Actividad::whereHas('responsables', fn($q) => $q->where('users.id', $user->id))
                    ->whereYear('created_at', $this->anio)
                    ->when($this->componenteId, fn($q) => $q->where('componente_id', $this->componenteId));

                $total       = (clone $base)->count();
                $completadas = (clone $base)->where('estado', 'completada')->count();
                $vencidas    = (clone $base)->where('estado', 'vencida')->count();
                $sin_ev      = (clone $base)->whereNotIn('estado',['pendiente'])->whereDoesntHave('evidencias')->count();
                $diasRetraso = (clone $base)->where('estado','vencida')->whereNotNull('fecha_limite')
                    ->selectRaw('AVG(DATEDIFF(NOW(), fecha_limite)) as promedio')->value('promedio');

                $porcentaje = $total > 0 ? round(($completadas / $total) * 100) : 0;
                $semaforo   = $porcentaje >= 75 ? 'Al día' : ($porcentaje >= 50 ? 'En proceso' : 'En riesgo');

                return [
                    $user->name,
                    $user->cargo ?? '—',
                    $user->unidadOrganica?->sigla ?? '—',
                    $total,
                    $completadas,
                    $vencidas,
                    $sin_ev,
                    $porcentaje . '%',
                    $diasRetraso ? round($diasRetraso) . ' días' : '0 días',
                    $semaforo,
                ];
            })
            ->sortBy(fn($r) => (int) $r[7]) // por porcentaje asc
            ->values();

        return collect($responsables);
    }

    public function headings(): array
    {
        return [
            'Responsable', 'Cargo', 'Unidad', 'Total Act.',
            'Completadas', 'Vencidas', 'Sin Evidencia',
            '% Cumplimiento', 'Retraso Promedio', 'Estado',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Colorear filas según estado
        for ($i = 2; $i <= $lastRow; $i++) {
            $estado = $sheet->getCell("J{$i}")->getValue();
            $bg = match($estado) {
                'En riesgo'  => 'FFFDE8E8',
                'En proceso' => 'FFFFF4E0',
                default      => 'FFE8F8EE',
            };
            $sheet->getStyle("A{$i}:J{$i}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB($bg);
        }

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
        return ['A'=>28,'B'=>24,'C'=>12,'D'=>10,'E'=>12,'F'=>10,'G'=>14,'H'=>16,'I'=>18,'J'=>12];
    }
}

// ─── Hoja 2: Sin Evidencia ─────────────────────────────────────────────────

class CumplimientoSinEvidenciaSheet implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function __construct(
        private int  $anio,
        private ?int $unidadId,
        private ?int $componenteId,
    ) {}

    public function title(): string { return 'Sin Evidencia'; }

    public function collection()
    {
        return Actividad::with(['componente', 'unidadOrganica', 'responsables'])
            ->whereNotIn('estado', ['pendiente'])
            ->whereDoesntHave('evidencias')
            ->whereYear('created_at', $this->anio)
            ->when($this->unidadId,     fn($q) => $q->where('unidad_organica_id', $this->unidadId))
            ->when($this->componenteId, fn($q) => $q->where('componente_id', $this->componenteId))
            ->orderByRaw("FIELD(estado,'vencida','observado','en_proceso','completada')")
            ->orderBy('fecha_limite')
            ->get()
            ->map(fn($a) => [
                $a->nombre,
                $a->codigo ?? '—',
                $a->componente?->nombre ?? '—',
                $a->unidadOrganica?->sigla ?? '—',
                $a->responsables->where('pivot.tipo','principal')->first()?->name
                    ?? $a->responsables->first()?->name ?? '—',
                ucfirst($a->estado),
                ucfirst($a->prioridad),
                $a->avance . '%',
                $a->fecha_limite?->format('d/m/Y') ?? '—',
                $a->fecha_limite && $a->fecha_limite->lt(now())
                    ? now()->diffInDays($a->fecha_limite) . ' días' : '—',
            ]);
    }

    public function headings(): array
    {
        return [
            'Actividad', 'Código', 'Componente', 'Unidad',
            'Responsable Principal', 'Estado', 'Prioridad',
            '% Avance', 'Fecha Límite', 'Días de Retraso',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        for ($i = 2; $i <= $lastRow; $i++) {
            $estado = $sheet->getCell("F{$i}")->getValue();
            $bg = match(strtolower($estado)) {
                'vencida'    => 'FFFDE8E8',
                'observado'  => 'FFE8F0FF',
                'en proceso' => 'FFFFF4E0',
                default      => 'FFFFFFF0',
            };
            $sheet->getStyle("A{$i}:J{$i}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB($bg);
        }

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFF9F43']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return ['A'=>38,'B'=>12,'C'=>26,'D'=>12,'E'=>26,'F'=>12,'G'=>10,'H'=>10,'I'=>14,'J'=>16];
    }
}
