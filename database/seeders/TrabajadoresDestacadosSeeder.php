<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrabajadoresDestacadosSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('trabajadores_destacados')->where('mes', 0)->delete();

        $trabajadores = [
            // Enero 2026
            [
                'unidad_organica_id'      => 6,
                'nombre'                  => 'Ana Lucía Torres Espinoza',
                'cargo'                   => 'Responsable de Contabilidad',
                'dni'                     => '46734521',
                'correo'                  => 'contabilidad@ugelhuacaybamba.edu.pe',
                'foto_ruta'               => null,
                'puntaje_cumplimiento'    => 96.00,
                'puntaje_puntualidad'     => 94.00,
                'puntaje_participacion'   => 90.00,
                'puntaje_responsabilidad' => 92.00,
                'anio'                    => '2026',
                'mes'                     => 1,
                'categoria'               => 'Control Interno',
                'motivo'                  => 'Implementación exitosa del sistema de control de caja chica con cero observaciones en la revisión interna de enero 2026. Registro oportuno de conciliaciones bancarias de la UE 307.',
                'numero_resolucion'       => 'RD N° 0043-2026-UGEL-HCB',
                'resolucion_ruta'         => null,
                'activo'                  => true,
                'registrado_por'          => 1,
                'created_at'              => '2026-01-30 17:00:00',
                'updated_at'              => '2026-01-30 17:00:00',
            ],
            [
                'unidad_organica_id'      => 3,
                'nombre'                  => 'Marco Antonio Príncipe López',
                'cargo'                   => 'Especialista en Gestión Pedagógica',
                'dni'                     => '44512367',
                'correo'                  => 'especialista.agp@ugelhuacaybamba.edu.pe',
                'foto_ruta'               => null,
                'puntaje_cumplimiento'    => 91.00,
                'puntaje_puntualidad'     => 89.00,
                'puntaje_participacion'   => 93.00,
                'puntaje_responsabilidad' => 90.00,
                'anio'                    => '2026',
                'mes'                     => 1,
                'categoria'               => 'Modelo de Integridad',
                'motivo'                  => 'Liderazgo en la socialización del Código de Ética en 14 instituciones educativas de los distritos de Huacaybamba y Cochabamba durante enero 2026.',
                'numero_resolucion'       => 'RD N° 0044-2026-UGEL-HCB',
                'resolucion_ruta'         => null,
                'activo'                  => true,
                'registrado_por'          => 1,
                'created_at'              => '2026-01-30 17:00:00',
                'updated_at'              => '2026-01-30 17:00:00',
            ],
            // Febrero 2026
            [
                'unidad_organica_id'      => 8,
                'nombre'                  => 'Lucía Fernández Ríos',
                'cargo'                   => 'Responsable de Recursos Humanos',
                'dni'                     => '48956743',
                'correo'                  => 'rrhh@ugelhuacaybamba.edu.pe',
                'foto_ruta'               => null,
                'puntaje_cumplimiento'    => 94.00,
                'puntaje_puntualidad'     => 91.00,
                'puntaje_participacion'   => 89.00,
                'puntaje_responsabilidad' => 93.00,
                'anio'                    => '2026',
                'mes'                     => 2,
                'categoria'               => 'Control Interno',
                'motivo'                  => 'Regularización al 100% de legajos del personal docente y administrativo de la UGEL Huacaybamba, con actualización del escalafón para el proceso de evaluación docente 2026.',
                'numero_resolucion'       => 'RD N° 0089-2026-UGEL-HCB',
                'resolucion_ruta'         => null,
                'activo'                  => true,
                'registrado_por'          => 1,
                'created_at'              => '2026-02-27 17:00:00',
                'updated_at'              => '2026-02-27 17:00:00',
            ],
            [
                'unidad_organica_id'      => 2,
                'nombre'                  => 'Rosa Isabel Vargas Tarazona',
                'cargo'                   => 'Jefe(a) de Área de Gestión Administrativa',
                'dni'                     => '44512378',
                'correo'                  => 'administracion@ugelhuacaybamba.edu.pe',
                'foto_ruta'               => null,
                'puntaje_cumplimiento'    => 90.00,
                'puntaje_puntualidad'     => 88.00,
                'puntaje_participacion'   => 87.00,
                'puntaje_responsabilidad' => 91.00,
                'anio'                    => '2026',
                'mes'                     => 2,
                'categoria'               => 'Gestión Institucional',
                'motivo'                  => 'Implementación del protocolo de atención al ciudadano para trámites documentarios de docentes de zonas rurales de la provincia de Huacaybamba, reduciendo tiempos de espera en 60%.',
                'numero_resolucion'       => 'RD N° 0090-2026-UGEL-HCB',
                'resolucion_ruta'         => null,
                'activo'                  => true,
                'registrado_por'          => 1,
                'created_at'              => '2026-02-27 17:00:00',
                'updated_at'              => '2026-02-27 17:00:00',
            ],
            // Marzo 2026
            [
                'unidad_organica_id'      => 5,
                'nombre'                  => 'Roberto Enrique Chávez Palacios',
                'cargo'                   => 'Asesor(a) Jurídico(a)',
                'dni'                     => '41289076',
                'correo'                  => 'asesoria@ugelhuacaybamba.edu.pe',
                'foto_ruta'               => null,
                'puntaje_cumplimiento'    => 95.00,
                'puntaje_puntualidad'     => 93.00,
                'puntaje_participacion'   => 88.00,
                'puntaje_responsabilidad' => 94.00,
                'anio'                    => '2026',
                'mes'                     => 3,
                'categoria'               => 'Integridad',
                'motivo'                  => 'Elaboración del plan de gestión de riesgos de corrupción de la UGEL Huacaybamba identificando 12 procesos críticos y estableciendo controles preventivos alineados a la Directiva N° 006-2019-CG/INTEG.',
                'numero_resolucion'       => 'RD N° 0142-2026-UGEL-HCB',
                'resolucion_ruta'         => null,
                'activo'                  => true,
                'registrado_por'          => 1,
                'created_at'              => '2026-03-31 17:00:00',
                'updated_at'              => '2026-03-31 17:00:00',
            ],
            [
                'unidad_organica_id'      => 9,
                'nombre'                  => 'Juan Carlos Soto Benites',
                'cargo'                   => 'Responsable de Tesorería',
                'dni'                     => '49067854',
                'correo'                  => 'tesoreria@ugelhuacaybamba.edu.pe',
                'foto_ruta'               => null,
                'puntaje_cumplimiento'    => 89.00,
                'puntaje_puntualidad'     => 92.00,
                'puntaje_participacion'   => 85.00,
                'puntaje_responsabilidad' => 90.00,
                'anio'                    => '2026',
                'mes'                     => 3,
                'categoria'               => 'Control Interno',
                'motivo'                  => 'Estandarización del proceso de viáticos para comisiones de servicio a zonas rurales (Canchabamba y Pinra), reduciendo el tiempo de procesamiento de 5 a 2 días hábiles sin observaciones de auditoría.',
                'numero_resolucion'       => 'RD N° 0143-2026-UGEL-HCB',
                'resolucion_ruta'         => null,
                'activo'                  => true,
                'registrado_por'          => 1,
                'created_at'              => '2026-03-31 17:00:00',
                'updated_at'              => '2026-03-31 17:00:00',
            ],
        ];

        $inserted = 0;
        foreach ($trabajadores as $t) {
            $existe = DB::table('trabajadores_destacados')
                ->where('anio', $t['anio'])
                ->where('mes', $t['mes'])
                ->where('dni', $t['dni'])
                ->exists();
            if (!$existe) {
                DB::table('trabajadores_destacados')->insert($t);
                $inserted++;
            }
        }

        $this->command->info("✅ Trabajadores destacados: {$inserted} nuevos (ene–mar 2026).");
    }
}
