<?php

namespace Database\Seeders;

use App\Models\HistorialRanking;
use Illuminate\Database\Seeder;

class HistorialRankingSeeder extends Seeder
{
    public function run(): void
    {
        HistorialRanking::query()->delete();

        // Posiciones mes anterior (mayo 2026) — base histórica para calcular variación en junio
        // unidad_organica_id: 1=Dir,2=Adm,3=Pedagog,4=Instit,5=Asesor,6=Contab,7=Logist,8=RRHH,9=Tesor,10=Infraestr
        $mayo = [
            ['unidad_organica_id' => 3,  'posicion' => 1,  'posicion_anterior' => 2,  'porcentaje' => 89.5, 'anio' => 2026, 'mes' => 5],
            ['unidad_organica_id' => 1,  'posicion' => 2,  'posicion_anterior' => 1,  'porcentaje' => 86.2, 'anio' => 2026, 'mes' => 5],
            ['unidad_organica_id' => 8,  'posicion' => 3,  'posicion_anterior' => 4,  'porcentaje' => 83.7, 'anio' => 2026, 'mes' => 5],
            ['unidad_organica_id' => 9,  'posicion' => 4,  'posicion_anterior' => 3,  'porcentaje' => 81.0, 'anio' => 2026, 'mes' => 5],
            ['unidad_organica_id' => 6,  'posicion' => 5,  'posicion_anterior' => 6,  'porcentaje' => 77.4, 'anio' => 2026, 'mes' => 5],
            ['unidad_organica_id' => 2,  'posicion' => 6,  'posicion_anterior' => 5,  'porcentaje' => 74.1, 'anio' => 2026, 'mes' => 5],
            ['unidad_organica_id' => 10, 'posicion' => 7,  'posicion_anterior' => 9,  'porcentaje' => 69.8, 'anio' => 2026, 'mes' => 5],
            ['unidad_organica_id' => 4,  'posicion' => 8,  'posicion_anterior' => 7,  'porcentaje' => 66.3, 'anio' => 2026, 'mes' => 5],
            ['unidad_organica_id' => 7,  'posicion' => 9,  'posicion_anterior' => 8,  'porcentaje' => 61.0, 'anio' => 2026, 'mes' => 5],
            ['unidad_organica_id' => 5,  'posicion' => 10, 'posicion_anterior' => 10, 'porcentaje' => 54.2, 'anio' => 2026, 'mes' => 5],
        ];

        // Posiciones mes actual (junio 2026)
        $junio = [
            ['unidad_organica_id' => 3,  'posicion' => 1,  'posicion_anterior' => 1,  'porcentaje' => 91.3, 'anio' => 2026, 'mes' => 6],
            ['unidad_organica_id' => 8,  'posicion' => 2,  'posicion_anterior' => 3,  'porcentaje' => 87.5, 'anio' => 2026, 'mes' => 6],
            ['unidad_organica_id' => 1,  'posicion' => 3,  'posicion_anterior' => 2,  'porcentaje' => 85.0, 'anio' => 2026, 'mes' => 6],
            ['unidad_organica_id' => 9,  'posicion' => 4,  'posicion_anterior' => 4,  'porcentaje' => 82.7, 'anio' => 2026, 'mes' => 6],
            ['unidad_organica_id' => 6,  'posicion' => 5,  'posicion_anterior' => 5,  'porcentaje' => 79.1, 'anio' => 2026, 'mes' => 6],
            ['unidad_organica_id' => 10, 'posicion' => 6,  'posicion_anterior' => 7,  'porcentaje' => 74.8, 'anio' => 2026, 'mes' => 6],
            ['unidad_organica_id' => 2,  'posicion' => 7,  'posicion_anterior' => 6,  'porcentaje' => 72.3, 'anio' => 2026, 'mes' => 6],
            ['unidad_organica_id' => 4,  'posicion' => 8,  'posicion_anterior' => 8,  'porcentaje' => 67.6, 'anio' => 2026, 'mes' => 6],
            ['unidad_organica_id' => 7,  'posicion' => 9,  'posicion_anterior' => 9,  'porcentaje' => 58.4, 'anio' => 2026, 'mes' => 6],
            ['unidad_organica_id' => 5,  'posicion' => 10, 'posicion_anterior' => 10, 'porcentaje' => 49.0, 'anio' => 2026, 'mes' => 6],
        ];

        foreach (array_merge($mayo, $junio) as $row) {
            HistorialRanking::create($row);
        }
    }
}
