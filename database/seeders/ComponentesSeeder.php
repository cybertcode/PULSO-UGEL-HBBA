<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComponentesSeeder extends Seeder
{
    public function run(): void
    {
        $componentes = [
            [1, 'Compromiso e Integridad',        'tabler-shield-check',  'ambos'],
            [2, 'Gestión de Riesgos',              'tabler-chart-pie',     'ambos'],
            [3, 'Actividades de Control',          'tabler-checklist',     'ambos'],
            [4, 'Información y Comunicación',      'tabler-messages',      'ambos'],
            [5, 'Supervisión',                     'tabler-eye',           'ambos'],
            [6, 'Ambiente de Control',             'tabler-building',      'sci'],
            [7, 'Evaluación de Riesgos',           'tabler-alert-circle',  'sci'],
            [8, 'Respuesta al Riesgo',             'tabler-shield-bolt',   'sci'],
            [9, 'Seguimiento',                     'tabler-timeline',      'sci'],
        ];

        foreach ($componentes as [$num, $nombre, $icono, $tipo]) {
            DB::table('componentes')->updateOrInsert(
                ['numero' => $num],
                [
                    'nombre'     => $nombre,
                    'icono'      => $icono,
                    'tipo'       => $tipo,
                    'activo'     => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
