<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnidadesOrganicasSeeder extends Seeder
{
    public function run(): void
    {
        $unidades = [
            ['codigo' => 'DIR',   'nombre' => 'Dirección',                                       'sigla' => 'DIR'],
            ['codigo' => 'OAD',   'nombre' => 'Oficina de Administración',                        'sigla' => 'OAD'],
            ['codigo' => 'AGP',   'nombre' => 'Área de Gestión Pedagógica',                       'sigla' => 'AGP'],
            ['codigo' => 'AGI',   'nombre' => 'Área de Gestión Institucional',                    'sigla' => 'AGI'],
            ['codigo' => 'ASESOR','nombre' => 'Asesoría Jurídica',                                'sigla' => 'ASESOR'],
            ['codigo' => 'CONT',  'nombre' => 'Contabilidad',                                     'sigla' => 'CONT'],
            ['codigo' => 'LOG',   'nombre' => 'Logística',                                        'sigla' => 'LOG'],
            ['codigo' => 'RR_HH', 'nombre' => 'Recursos Humanos',                                 'sigla' => 'RRHH'],
            ['codigo' => 'TESOR', 'nombre' => 'Tesorería',                                        'sigla' => 'TES'],
            ['codigo' => 'INF',   'nombre' => 'Infraestructura',                                  'sigla' => 'INF'],
        ];

        foreach ($unidades as $u) {
            DB::table('unidades_organicas')->updateOrInsert(
                ['codigo' => $u['codigo']],
                array_merge($u, ['activo' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
