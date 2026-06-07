<?php

namespace Database\Seeders;

use App\Models\TrabajadorDestacado;
use Illuminate\Database\Seeder;

class TrabajadoresDestacadosSeeder extends Seeder
{
    public function run(): void
    {
        TrabajadorDestacado::query()->forceDelete();

        // Unidades: 1=Dirección, 2=Adm, 3=Pedagogía, 4=Institucional,
        //           5=Asesoría, 6=Contabilidad, 7=Logística, 8=RRHH, 9=Tesorería, 10=Infraestructura

        $trabajadores = [
            // --- Mensual junio 2026 ---
            [
                'unidad_organica_id'      => 3,
                'nombre'                  => 'Juan Carlos Atoche Llontop',
                'cargo'                   => 'Especialista en Gestión Pedagógica',
                'dni'                     => '45231876',
                'correo'                  => 'j.atoche@ugel.gob.pe',
                'puntaje_cumplimiento'    => 95,
                'puntaje_puntualidad'     => 92,
                'puntaje_participacion'   => 88,
                'puntaje_responsabilidad' => 90,
                'anio'                    => 2026,
                'mes'                     => 6,
                'categoria'               => 'Control Interno',
                'motivo'                  => 'Cumplimiento excepcional del Plan de Control Interno del Área de Gestión Pedagógica con el 98% de actividades completadas.',
                'numero_resolucion'       => 'RD N° 1457-2026-UGEL-HCB',
                'activo'                  => true,
            ],
            [
                'unidad_organica_id'      => 8,
                'nombre'                  => 'María Fernanda Rojas Valenzuela',
                'cargo'                   => 'Especialista en Recursos Humanos',
                'dni'                     => '47893214',
                'correo'                  => 'm.rojas@ugel.gob.pe',
                'puntaje_cumplimiento'    => 90,
                'puntaje_puntualidad'     => 85,
                'puntaje_participacion'   => 88,
                'puntaje_responsabilidad' => 83,
                'anio'                    => 2026,
                'mes'                     => 6,
                'categoria'               => 'Modelo de Integridad',
                'motivo'                  => 'Liderazgo en la implementación del Modelo de Integridad Institucional y promoción de la ética pública.',
                'numero_resolucion'       => 'RD N° 1458-2026-UGEL-HCB',
                'activo'                  => true,
            ],
            [
                'unidad_organica_id'      => 6,
                'nombre'                  => 'Luis Alberto Quispe Mamani',
                'cargo'                   => 'Contador Público',
                'dni'                     => '43125698',
                'correo'                  => 'l.quispe@ugel.gob.pe',
                'puntaje_cumplimiento'    => 88,
                'puntaje_puntualidad'     => 82,
                'puntaje_participacion'   => 80,
                'puntaje_responsabilidad' => 84,
                'anio'                    => 2026,
                'mes'                     => 6,
                'categoria'               => 'Control Interno',
                'motivo'                  => 'Destacada gestión en la implementación de controles internos en el área de Contabilidad.',
                'numero_resolucion'       => 'RD N° 1459-2026-UGEL-HCB',
                'activo'                  => true,
            ],
            // --- Mensual mayo 2026 ---
            [
                'unidad_organica_id'      => 2,
                'nombre'                  => 'Carmen Rosa Mejía Sánchez',
                'cargo'                   => 'Jefa de Oficina de Administración',
                'dni'                     => '41256789',
                'correo'                  => 'c.mejia@ugel.gob.pe',
                'puntaje_cumplimiento'    => 85,
                'puntaje_puntualidad'     => 80,
                'puntaje_participacion'   => 82,
                'puntaje_responsabilidad' => 82,
                'anio'                    => 2026,
                'mes'                     => 5,
                'categoria'               => 'Buenas Prácticas',
                'motivo'                  => 'Implementación exitosa del sistema de gestión documental y digitalización de procesos administrativos.',
                'numero_resolucion'       => 'RD N° 1389-2026-UGEL-HCB',
                'activo'                  => true,
            ],
            [
                'unidad_organica_id'      => 9,
                'nombre'                  => 'Roberto Enrique Silva Paredes',
                'cargo'                   => 'Especialista en Tesorería',
                'dni'                     => '46782341',
                'correo'                  => 'r.silva@ugel.gob.pe',
                'puntaje_cumplimiento'    => 92,
                'puntaje_puntualidad'     => 88,
                'puntaje_participacion'   => 75,
                'puntaje_responsabilidad' => 90,
                'anio'                    => 2026,
                'mes'                     => 5,
                'categoria'               => 'Control Interno',
                'motivo'                  => 'Gestión eficiente de fondos públicos y cumplimiento al 100% de actividades de control en Tesorería.',
                'numero_resolucion'       => 'RD N° 1390-2026-UGEL-HCB',
                'activo'                  => true,
            ],
            // --- Mensual abril 2026 ---
            [
                'unidad_organica_id'      => 10,
                'nombre'                  => 'Sofía Alejandra Vega Castillo',
                'cargo'                   => 'Especialista en Infraestructura',
                'dni'                     => '48234567',
                'correo'                  => 's.vega@ugel.gob.pe',
                'puntaje_cumplimiento'    => 91,
                'puntaje_puntualidad'     => 89,
                'puntaje_participacion'   => 86,
                'puntaje_responsabilidad' => 93,
                'anio'                    => 2026,
                'mes'                     => 4,
                'categoria'               => 'Control Interno',
                'motivo'                  => 'Supervisión efectiva de obras educativas y cumplimiento del 95% del plan de mantenimiento preventivo.',
                'numero_resolucion'       => 'RD N° 1312-2026-UGEL-HCB',
                'activo'                  => true,
            ],
            // --- Anual 2025 ---
            [
                'unidad_organica_id'      => 1,
                'nombre'                  => 'Elena Patricia Vargas Huamán',
                'cargo'                   => 'Directora UGEL Huacaybamba',
                'dni'                     => '40123456',
                'correo'                  => 'direccion@ugel.gob.pe',
                'puntaje_cumplimiento'    => 96,
                'puntaje_puntualidad'     => 94,
                'puntaje_participacion'   => 92,
                'puntaje_responsabilidad' => 95,
                'anio'                    => 2025,
                'mes'                     => null,
                'categoria'               => 'Modelo de Integridad',
                'motivo'                  => 'Reconocimiento anual por liderazgo institucional en la implementación del Modelo de Integridad 2025 con el más alto porcentaje de avance de la región.',
                'numero_resolucion'       => 'RD N° 1201-2025-UGEL-HCB',
                'activo'                  => true,
            ],
            [
                'unidad_organica_id'      => 7,
                'nombre'                  => 'Marco Antonio Príncipe López',
                'cargo'                   => 'Especialista en Logística',
                'dni'                     => '44512367',
                'correo'                  => 'm.principe@ugel.gob.pe',
                'puntaje_cumplimiento'    => 88,
                'puntaje_puntualidad'     => 85,
                'puntaje_participacion'   => 84,
                'puntaje_responsabilidad' => 87,
                'anio'                    => 2025,
                'mes'                     => null,
                'categoria'               => 'Buenas Prácticas',
                'motivo'                  => 'Implementación del sistema de inventarios y control de bienes patrimoniales con reducción del 40% en tiempos de gestión.',
                'numero_resolucion'       => 'RD N° 1202-2025-UGEL-HCB',
                'activo'                  => true,
            ],
        ];

        foreach ($trabajadores as $data) {
            TrabajadorDestacado::create($data);
        }
    }
}
