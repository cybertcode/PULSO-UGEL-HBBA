<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfiguracionInstitucionalSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('configuracion_institucional')->updateOrInsert(
            ['id' => 1],
            [
                'nombre_institucion'      => 'Unidad de Gestión Educativa Local Huánuco',
                'sigla'                   => 'UGEL Huánuco',
                'ugel_codigo'             => '100101',
                'region'                  => 'Huánuco',
                'provincia'               => 'Huánuco',
                'departamento'            => 'Huánuco',
                'distrito'                => 'Huánuco',
                'ubigeo'                  => '100101',
                'direccion'               => 'Jr. General Prado N° 345, Huánuco',
                'sitio_web'               => 'https://www.ugel-huanuco.gob.pe',
                'timezone'                => 'America/Lima',
                'director'                => 'María Elena Quispe Huamán',
                'coordinador_sci'         => 'Carlos Alberto Flores Mendoza',
                'correo_institucional'    => 'sci@ugel-huanuco.gob.pe',
                'anio_gestion'            => 2025,
                'umbral_verde'            => 75,
                'umbral_amarillo'         => 50,
                'notif_vencimiento'       => true,
                'notif_dias_anticipacion' => 7,
                'notif_avance_bajo'       => true,
                'notif_umbral_avance'     => 30,
                'notif_email'             => false,
                'created_at'              => now(),
                'updated_at'              => now(),
            ]
        );
    }
}
