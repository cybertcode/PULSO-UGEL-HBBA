<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnidadesOrganicasSeeder extends Seeder
{
    public function run(): void
    {
        $unidades = [
            [
                'codigo'         => 'DIR',
                'nombre'         => 'Dirección',
                'sigla'          => 'DIR',
                'responsable_id' => null,
                'correo'         => 'direccion@ugel.gob.pe',
                'telefono'       => '062-440001',
                'descripcion'    => 'Órgano de dirección responsable de la conducción, planificación y supervisión de la gestión institucional de la UGEL.',
            ],
            [
                'codigo'         => 'OAD',
                'nombre'         => 'Oficina de Administración',
                'sigla'          => 'OAD',
                'responsable_id' => null,
                'correo'         => 'administracion@ugel.gob.pe',
                'telefono'       => '062-440002',
                'descripcion'    => 'Órgano de apoyo responsable de la gestión de los recursos humanos, materiales, financieros y servicios generales de la institución.',
            ],
            [
                'codigo'         => 'AGP',
                'nombre'         => 'Área de Gestión Pedagógica',
                'sigla'          => 'AGP',
                'responsable_id' => null,
                'correo'         => 'pedagogia@ugel.gob.pe',
                'telefono'       => '062-440003',
                'descripcion'    => 'Órgano de línea responsable de conducir, orientar, asesorar y supervisar la gestión pedagógica e institucional de las instituciones educativas.',
            ],
            [
                'codigo'         => 'AGI',
                'nombre'         => 'Área de Gestión Institucional',
                'sigla'          => 'AGI',
                'responsable_id' => null,
                'correo'         => 'sci@ugel.gob.pe',
                'telefono'       => '062-440004',
                'descripcion'    => 'Órgano de línea responsable de la gestión institucional, implementación del Sistema de Control Interno y del Modelo de Integridad.',
            ],
            [
                'codigo'         => 'ASESOR',
                'nombre'         => 'Asesoría Jurídica',
                'sigla'          => 'ASESOR',
                'responsable_id' => null,
                'correo'         => 'asesor@ugel.gob.pe',
                'telefono'       => '062-440005',
                'descripcion'    => 'Órgano de asesoramiento que brinda orientación y apoyo jurídico-legal a la Dirección y demás unidades orgánicas.',
            ],
            [
                'codigo'         => 'CONT',
                'nombre'         => 'Contabilidad',
                'sigla'          => 'CONT',
                'responsable_id' => null,
                'correo'         => 'contabilidad@ugel.gob.pe',
                'telefono'       => '062-440006',
                'descripcion'    => 'Unidad responsable del registro contable, estados financieros, conciliaciones y cumplimiento de normas presupuestales.',
            ],
            [
                'codigo'         => 'LOG',
                'nombre'         => 'Logística',
                'sigla'          => 'LOG',
                'responsable_id' => null,
                'correo'         => 'logistica@ugel.gob.pe',
                'telefono'       => '062-440007',
                'descripcion'    => 'Unidad responsable de la programación, adquisición, almacenamiento y distribución de bienes y servicios institucionales.',
            ],
            [
                'codigo'         => 'RR_HH',
                'nombre'         => 'Recursos Humanos',
                'sigla'          => 'RRHH',
                'responsable_id' => null,
                'correo'         => 'rrhh@ugel.gob.pe',
                'telefono'       => '062-440008',
                'descripcion'    => 'Unidad responsable de la gestión del personal, remuneraciones, legajos, control de asistencia y bienestar del servidor.',
            ],
            [
                'codigo'         => 'TESOR',
                'nombre'         => 'Tesorería',
                'sigla'          => 'TES',
                'responsable_id' => null,
                'correo'         => 'tesoreria@ugel.gob.pe',
                'telefono'       => '062-440009',
                'descripcion'    => 'Unidad responsable de la administración de fondos públicos, pagos, caja chica y conciliación de cuentas bancarias institucionales.',
            ],
            [
                'codigo'         => 'INF',
                'nombre'         => 'Infraestructura',
                'sigla'          => 'INF',
                'responsable_id' => null,
                'correo'         => 'infraestructura@ugel.gob.pe',
                'telefono'       => '062-440010',
                'descripcion'    => 'Unidad responsable de la planificación, ejecución y supervisión de obras de infraestructura educativa y mantenimiento de locales escolares.',
            ],
        ];

        foreach ($unidades as $u) {
            DB::table('unidades_organicas')->updateOrInsert(
                ['codigo' => $u['codigo']],
                array_merge($u, [
                    'activo'     => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
