<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfiguracionInstitucionalSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('configuracion_institucional')->count() === 0) {
            DB::table('configuracion_institucional')->insert([
                'nombre_institucion'     => 'UGEL Huacaybamba',
                'sigla'                  => 'UGEL-HCB',
                'ugel_codigo'            => '1003',
                'region'                 => 'Huánuco',
                'provincia'              => 'Huacaybamba',
                'director'               => '',
                'coordinador_sci'        => '',
                'correo_institucional'   => 'ugel.huacaybamba@minedu.gob.pe',
                'anio_gestion'           => date('Y'),
                'umbral_verde'           => 75,
                'umbral_amarillo'        => 50,
                'notif_vencimiento'      => true,
                'notif_dias_anticipacion'=> 7,
                'notif_avance_bajo'      => true,
                'notif_umbral_avance'    => 30,
                'notif_email'            => false,
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);
        }
    }
}
