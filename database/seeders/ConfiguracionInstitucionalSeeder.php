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
                'nombre_institucion'      => 'Unidad de Gestión Educativa Local Huacaybamba',
                'sigla'                   => 'UGEL Huacaybamba',
                'ugel_codigo'             => '100401',
                'region'                  => 'Huánuco',
                'provincia'               => 'Huacaybamba',
                'departamento'            => 'Huánuco',
                'distrito'                => 'Huacaybamba',
                'ubigeo'                  => '100401',
                'direccion'               => 'Av. 28 de Julio N° 502-504, Huacaybamba',
                'sitio_web'               => 'https://www.ugelhuacaybamba.edu.pe',
                'timezone'                => 'America/Lima',
                'director'                => 'Mg. Julio Luis Lozano Yllatopa',
                'coordinador_sci'         => 'Carlos Alberto Flores Mendoza',
                'correo_institucional'    => 'sci@ugelhuacaybamba.edu.pe',
                'anio_gestion'            => 2026,
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
