<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargosSeeder extends Seeder
{
    public function run(): void
    {
        $cargos = [
            // Dirección
            'Director(a) de UGEL',
            'Sub Director(a) de UGEL',
            'Secretario(a) de Dirección',

            // Gestión Institucional
            'Jefe(a) de Área de Gestión Institucional',
            'Coordinador(a) de Control Interno',
            'Especialista en Gestión Institucional',
            'Monitora de Integridad',
            'Analista Administrativo',

            // Gestión Pedagógica
            'Jefe(a) de Área de Gestión Pedagógica',
            'Especialista en Educación Primaria',
            'Especialista en Educación Secundaria',
            'Especialista en Educación Inicial',
            'Especialista en Gestión Pedagógica',
            'Especialista UGEL',

            // Administración
            'Jefe(a) de Oficina de Administración',
            'Asistente Administrativo',

            // Contabilidad
            'Responsable de Contabilidad',
            'Contador Público',
            'Técnico en Contabilidad',

            // Logística
            'Responsable de Logística',
            'Técnico en Logística',
            'Almacenero',

            // Recursos Humanos
            'Responsable de Recursos Humanos',
            'Técnico en Recursos Humanos',
            'Analista de Personal',

            // Tesorería
            'Responsable de Tesorería',
            'Técnico en Tesorería',
            'Cajero(a)',

            // Infraestructura
            'Responsable de Infraestructura',
            'Especialista en Infraestructura',
            'Inspector de Obras',

            // Asesoría Jurídica
            'Asesor(a) Jurídico(a)',
            'Técnico Legal',

            // Tecnología
            'Jefe(a) de Informática',
            'Especialista en Tecnologías de la Información',
            'Técnico en Sistemas',

            // Otros
            'Técnico Administrativo',
            'Auxiliar Administrativo',
            'Secretaria',
        ];

        foreach ($cargos as $nombre) {
            DB::table('cargos')->updateOrInsert(
                ['nombre' => $nombre],
                ['activo' => true, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
