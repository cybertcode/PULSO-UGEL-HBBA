<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComponentesSeeder extends Seeder
{
    public function run(): void
    {
        // 9 componentes del Modelo de Integridad según Directiva N° 006-2019-CG-INTEG y DS 148-2024-PCM
        $componentes = [
            [1, 'Compromiso de Alta Dirección',                           'tabler-crown',          'ambos', 'Compromiso visible y activo de la alta dirección para promover una cultura de integridad institucional.'],
            [2, 'Gestión de Riesgos en Actos de Integridad',              'tabler-chart-pie',      'ambos', 'Identificación, análisis y respuesta a los riesgos de corrupción e integridad en la entidad.'],
            [3, 'Políticas de Integridad',                                 'tabler-file-certificate','ambos', 'Desarrollo e implementación de políticas institucionales que promuevan la integridad y la ética pública.'],
            [4, 'Transparencia, Datos Abiertos y Rendición de Cuentas',   'tabler-eye',            'ambos', 'Acceso a la información pública, datos abiertos y mecanismos de rendición de cuentas.'],
            [5, 'Controles Internos, Externos y Auditoría',               'tabler-shield-check',   'sci',   'Implementación del Sistema de Control Interno y articulación con el control externo.'],
            [6, 'Comunicación y Capacitación',                            'tabler-messages',       'ambos', 'Difusión de valores, capacitación en integridad y ética pública a todos los servidores.'],
            [7, 'Canal de Denuncias',                                     'tabler-speakerphone',   'ambos', 'Mecanismos seguros y confidenciales para la recepción y gestión de denuncias por actos de corrupción.'],
            [8, 'Supervisión y Monitoreo',                                'tabler-activity',       'ambos', 'Seguimiento continuo al cumplimiento del Modelo de Integridad y sus componentes.'],
            [9, 'Encargado del Modelo de Integridad',                     'tabler-user-check',     'integridad','Funcionario responsable de coordinar, articular y dar seguimiento al Modelo de Integridad.'],
        ];

        foreach ($componentes as [$num, $nombre, $icono, $tipo, $desc]) {
            DB::table('componentes')->updateOrInsert(
                ['numero' => $num],
                [
                    'nombre'      => $nombre,
                    'icono'       => $icono,
                    'tipo'        => $tipo,
                    'descripcion' => $desc,
                    'activo'      => true,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]
            );
        }
    }
}
