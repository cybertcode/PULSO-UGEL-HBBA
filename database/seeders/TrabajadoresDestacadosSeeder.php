<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrabajadoresDestacadosSeeder extends Seeder
{
    public function run(): void
    {
        // Eliminar registros con mes=0 (corruptos)
        DB::table('trabajadores_destacados')->where('mes', 0)->delete();

        $trabajadores = [
            // Enero 2026
            [
                'unidad_organica_id'      => 6,
                'nombre'                  => 'Sandra Milagros León Coronado',
                'cargo'                   => 'Contadora Pública',
                'dni'                     => '47231896',
                'correo'                  => 's.leon@ugel.gob.pe',
                'foto_ruta'               => null,
                'puntaje_cumplimiento'    => 96.00,
                'puntaje_puntualidad'     => 94.00,
                'puntaje_participacion'   => 90.00,
                'puntaje_responsabilidad' => 92.00,
                'anio'                    => '2026',
                'mes'                     => 1,
                'categoria'               => 'Control Interno',
                'motivo'                  => 'Implementación exitosa del sistema de control de caja chica con cero observaciones en auditoría interna de enero 2026.',
                'numero_resolucion'       => 'RD N° 0043-2026-UGEL-HCB',
                'resolucion_ruta'         => null,
                'activo'                  => true,
                'registrado_por'          => 1,
                'created_at'              => '2026-01-30 17:00:00',
                'updated_at'              => '2026-01-30 17:00:00',
            ],
            [
                'unidad_organica_id'      => 3,
                'nombre'                  => 'Pedro Antonio Huanca Mamani',
                'cargo'                   => 'Especialista Pedagógico',
                'dni'                     => '43981234',
                'correo'                  => 'p.huanca@ugel.gob.pe',
                'foto_ruta'               => null,
                'puntaje_cumplimiento'    => 91.00,
                'puntaje_puntualidad'     => 89.00,
                'puntaje_participacion'   => 93.00,
                'puntaje_responsabilidad' => 90.00,
                'anio'                    => '2026',
                'mes'                     => 1,
                'categoria'               => 'Modelo de Integridad',
                'motivo'                  => 'Liderazgo en socialización del Código de Ética con 12 instituciones educativas visitadas durante enero 2026.',
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
                'nombre'                  => 'Rosa Isabel Vargas Tarazona',
                'cargo'                   => 'Especialista en Recursos Humanos',
                'dni'                     => '45678123',
                'correo'                  => 'r.vargas@ugel.gob.pe',
                'foto_ruta'               => null,
                'puntaje_cumplimiento'    => 94.00,
                'puntaje_puntualidad'     => 91.00,
                'puntaje_participacion'   => 89.00,
                'puntaje_responsabilidad' => 93.00,
                'anio'                    => '2026',
                'mes'                     => 2,
                'categoria'               => 'Control Interno',
                'motivo'                  => 'Regularización al 100% de legajos de personal y actualización del escalafón institucional en tiempo récord.',
                'numero_resolucion'       => 'RD N° 0089-2026-UGEL-HCB',
                'resolucion_ruta'         => null,
                'activo'                  => true,
                'registrado_por'          => 1,
                'created_at'              => '2026-02-27 17:00:00',
                'updated_at'              => '2026-02-27 17:00:00',
            ],
            [
                'unidad_organica_id'      => 2,
                'nombre'                  => 'Jorge Luis Ramírez Castillo',
                'cargo'                   => 'Jefe de Administración',
                'dni'                     => '42315678',
                'correo'                  => 'j.ramirez@ugel.gob.pe',
                'foto_ruta'               => null,
                'puntaje_cumplimiento'    => 90.00,
                'puntaje_puntualidad'     => 88.00,
                'puntaje_participacion'   => 87.00,
                'puntaje_responsabilidad' => 91.00,
                'anio'                    => '2026',
                'mes'                     => 2,
                'categoria'               => 'Gestión Institucional',
                'motivo'                  => 'Implementación del sistema de citas previas para atención al ciudadano, reduciendo tiempos de espera en 60%.',
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
                'nombre'                  => 'Ana Lucía Torres Espinoza',
                'cargo'                   => 'Asesora Legal',
                'dni'                     => '46123789',
                'correo'                  => 'a.torres@ugel.gob.pe',
                'foto_ruta'               => null,
                'puntaje_cumplimiento'    => 95.00,
                'puntaje_puntualidad'     => 93.00,
                'puntaje_participacion'   => 88.00,
                'puntaje_responsabilidad' => 94.00,
                'anio'                    => '2026',
                'mes'                     => 3,
                'categoria'               => 'Integridad',
                'motivo'                  => 'Elaboración del plan de gestión de riesgos de corrupción con identificación de 12 procesos críticos y controles preventivos.',
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
                'cargo'                   => 'Tesorero',
                'dni'                     => '44567890',
                'correo'                  => 'j.soto@ugel.gob.pe',
                'foto_ruta'               => null,
                'puntaje_cumplimiento'    => 89.00,
                'puntaje_puntualidad'     => 92.00,
                'puntaje_participacion'   => 85.00,
                'puntaje_responsabilidad' => 90.00,
                'anio'                    => '2026',
                'mes'                     => 3,
                'categoria'               => 'Control Interno',
                'motivo'                  => 'Estandarización del proceso de viáticos reduciendo tiempo de procesamiento de 5 a 2 días hábiles sin observaciones.',
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

        $this->command->info("✅ Trabajadores destacados: {$inserted} nuevos (ene–mar 2026), corruptos mes=0 eliminados.");
    }
}
