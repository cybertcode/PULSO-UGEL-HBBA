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
                'correo'         => 'direccion@ugelhuacaybamba.edu.pe',
                'telefono'       => '968741906',
                'descripcion'    => 'Órgano de dirección responsable de la conducción, planificación y supervisión de la gestión educativa e institucional de la UGEL Huacaybamba, con jurisdicción en los 4 distritos de la provincia: Huacaybamba, Cochabamba, Canchabamba y Pinra.',
            ],
            [
                'codigo'         => 'OAD',
                'nombre'         => 'Área de Gestión Administrativa',
                'sigla'          => 'AGA',
                'responsable_id' => null,
                'correo'         => 'administracion@ugelhuacaybamba.edu.pe',
                'telefono'       => '968741906',
                'descripcion'    => 'Órgano de apoyo responsable de la gestión de los recursos humanos, presupuesto, logística, servicios generales y tecnologías de información de la UGEL Huacaybamba.',
            ],
            [
                'codigo'         => 'AGP',
                'nombre'         => 'Área de Gestión Pedagógica',
                'sigla'          => 'AGP',
                'responsable_id' => null,
                'correo'         => 'pedagogia@ugelhuacaybamba.edu.pe',
                'telefono'       => '968741906',
                'descripcion'    => 'Órgano de línea responsable de conducir, orientar, asesorar y supervisar la gestión pedagógica de las instituciones educativas en los niveles de Inicial, Primaria y Secundaria de la provincia de Huacaybamba.',
            ],
            [
                'codigo'         => 'AGI',
                'nombre'         => 'Área de Gestión Institucional',
                'sigla'          => 'AGI',
                'responsable_id' => null,
                'correo'         => 'sci@ugelhuacaybamba.edu.pe',
                'telefono'       => '968741906',
                'descripcion'    => 'Órgano de línea responsable de la gestión institucional, implementación del Sistema de Control Interno (SCI) y del Modelo de Integridad de la UGEL Huacaybamba.',
            ],
            [
                'codigo'         => 'ASESOR',
                'nombre'         => 'Asesoría Jurídica',
                'sigla'          => 'AJ',
                'responsable_id' => null,
                'correo'         => 'asesoria@ugelhuacaybamba.edu.pe',
                'telefono'       => '968741906',
                'descripcion'    => 'Órgano de asesoramiento que brinda orientación y apoyo jurídico-legal a la Dirección y demás unidades orgánicas de la UGEL Huacaybamba, velando por el cumplimiento del marco normativo vigente.',
            ],
            [
                'codigo'         => 'CONT',
                'nombre'         => 'Contabilidad',
                'sigla'          => 'CONT',
                'responsable_id' => null,
                'correo'         => 'contabilidad@ugelhuacaybamba.edu.pe',
                'telefono'       => '968741906',
                'descripcion'    => 'Unidad responsable del registro contable, estados financieros, conciliaciones bancarias y cumplimiento de normas presupuestales conforme a la normativa del MEF y la Contraloría General de la República.',
            ],
            [
                'codigo'         => 'LOG',
                'nombre'         => 'Logística',
                'sigla'          => 'LOG',
                'responsable_id' => null,
                'correo'         => 'logistica@ugelhuacaybamba.edu.pe',
                'telefono'       => '968741906',
                'descripcion'    => 'Unidad responsable de la programación, adquisición, almacenamiento, distribución y control patrimonial de bienes y servicios institucionales, incluyendo el mantenimiento de locales educativos de la provincia.',
            ],
            [
                'codigo'         => 'RR_HH',
                'nombre'         => 'Recursos Humanos',
                'sigla'          => 'RRHH',
                'responsable_id' => null,
                'correo'         => 'rrhh@ugelhuacaybamba.edu.pe',
                'telefono'       => '968741906',
                'descripcion'    => 'Unidad responsable de la gestión del personal docente y administrativo, nombramientos, contrataciones, remuneraciones, legajos, control de asistencia y bienestar del servidor en la UGEL Huacaybamba.',
            ],
            [
                'codigo'         => 'TESOR',
                'nombre'         => 'Tesorería',
                'sigla'          => 'TES',
                'responsable_id' => null,
                'correo'         => 'tesoreria@ugelhuacaybamba.edu.pe',
                'telefono'       => '968741906',
                'descripcion'    => 'Unidad responsable de la administración de fondos públicos, pagos a proveedores y planillas, caja chica y conciliación de cuentas bancarias de la Unidad Ejecutora 307 - UGEL Huacaybamba.',
            ],
            [
                'codigo'         => 'INF',
                'nombre'         => 'Infraestructura',
                'sigla'          => 'INF',
                'responsable_id' => null,
                'correo'         => 'infraestructura@ugelhuacaybamba.edu.pe',
                'telefono'       => '968741906',
                'descripcion'    => 'Unidad responsable de la planificación, ejecución y supervisión de obras de infraestructura educativa, mantenimiento preventivo y correctivo de locales escolares en los 4 distritos de la provincia de Huacaybamba.',
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
