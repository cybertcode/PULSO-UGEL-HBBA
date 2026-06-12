<?php

namespace Database\Seeders;

use App\Models\Normativa;
use App\Models\User;
use Illuminate\Database\Seeder;

class NormativasSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@admin.com')->first();
        $creadorId = $admin?->id ?? 1;

        $normativas = [
            [
                'nombre'          => 'Ley N° 28716 — Ley de Control Interno de las Entidades del Estado',
                'codigo'          => 'Ley N° 28716',
                'descripcion'     => 'Establece las normas para regular la elaboración, aprobación, implantación, funcionamiento, perfeccionamiento y evaluación del control interno en las entidades del Estado, con el propósito de cautelar y fortalecer los sistemas administrativos y operativos con acciones y actividades de control previo, simultáneo y posterior, contra los actos y prácticas indebidas o de corrupción.',
                'tipo'            => 'ley',
                'alcance'         => 'nacional',
                'modulo'          => 'sci',
                'link_externo'    => 'https://www.gob.pe/institucion/contraloria/normas-legales/3302-28716',
                'fecha_emision'   => '2006-04-18',
                'vigente'         => true,
                'entidad_emisora' => 'Congreso de la República del Perú',
                'orden'           => 1,
                'creado_por'      => $creadorId,
            ],
            [
                'nombre'          => 'Directiva N° 006-2019-CG/INTEG — Implementación del Sistema de Control Interno en las Entidades del Estado',
                'codigo'          => 'Directiva N° 006-2019-CG/INTEG',
                'descripcion'     => 'Establece los procedimientos para la implementación del Sistema de Control Interno (SCI) en las entidades del Estado. Incluye el Plan de Implementación, plazos y responsabilidades. Es el marco principal que rige el PACI de la UGEL Huacaybamba.',
                'tipo'            => 'directiva',
                'alcance'         => 'nacional',
                'modulo'          => 'sci',
                'link_externo'    => 'https://www.contraloria.gob.pe/wps/wcm/connect/cgrnew/as_contraloria/normativa/directivas',
                'fecha_emision'   => '2019-12-05',
                'vigente'         => true,
                'entidad_emisora' => 'Contraloría General de la República (CGR)',
                'observacion'     => 'Directiva principal del SCI. Todos los servidores con rol en el PACI deben conocerla.',
                'orden'           => 2,
                'creado_por'      => $creadorId,
            ],
            [
                'nombre'          => 'D.S. N° 054-2018-PCM — Aprueban Lineamientos de Organización del Estado',
                'codigo'          => 'D.S. N° 054-2018-PCM',
                'descripcion'     => 'Decreto Supremo que aprueba los Lineamientos de Organización del Estado, estableciendo principios y reglas generales para el diseño y funcionamiento de las entidades de la Administración Pública.',
                'tipo'            => 'decreto',
                'alcance'         => 'nacional',
                'modulo'          => 'general',
                'link_externo'    => 'https://www.gob.pe/institucion/pcm/normas-legales/267472-054-2018-pcm',
                'fecha_emision'   => '2018-05-24',
                'vigente'         => true,
                'entidad_emisora' => 'Presidencia del Consejo de Ministros (PCM)',
                'orden'           => 3,
                'creado_por'      => $creadorId,
            ],
            [
                'nombre'          => 'Resolución de Contraloría N° 320-2006-CG — Normas de Control Interno',
                'codigo'          => 'R.C. N° 320-2006-CG',
                'descripcion'     => 'Aprueba las Normas de Control Interno para el Sector Público, basadas en el modelo COSO. Establece los 5 componentes del SCI: Ambiente de Control, Evaluación de Riesgos, Actividades de Control, Información y Comunicación, y Supervisión.',
                'tipo'            => 'resolucion',
                'alcance'         => 'nacional',
                'modulo'          => 'sci',
                'link_externo'    => 'https://www.contraloria.gob.pe/wps/wcm/connect/cgrnew/as_contraloria/normativa/resolucion-contraloria',
                'fecha_emision'   => '2006-10-30',
                'vigente'         => true,
                'entidad_emisora' => 'Contraloría General de la República (CGR)',
                'orden'           => 4,
                'creado_por'      => $creadorId,
            ],
            [
                'nombre'          => 'Manual para Implementar el Sistema de Control Interno — CGR',
                'codigo'          => 'Manual SCI-CGR-2016',
                'descripcion'     => 'Guía práctica de la CGR para la implementación progresiva del Sistema de Control Interno. Incluye herramientas, plantillas y ejemplos de aplicación para entidades de los tres niveles de gobierno.',
                'tipo'            => 'manual',
                'alcance'         => 'nacional',
                'modulo'          => 'sci',
                'link_externo'    => 'https://apps.contraloria.gob.pe/packanticorrupcion/control_interno.html',
                'tutorial_url'    => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'tutorial_tipo'   => 'youtube',
                'fecha_emision'   => '2016-01-01',
                'vigente'         => true,
                'entidad_emisora' => 'Contraloría General de la República (CGR)',
                'observacion'     => 'Recurso de apoyo para el equipo SCI. Revisar especialmente el Capítulo 3 sobre evaluación de riesgos.',
                'orden'           => 5,
                'creado_por'      => $creadorId,
            ],
            [
                'nombre'          => 'Política Nacional de Integridad y Lucha contra la Corrupción — D.S. N° 092-2017-PCM',
                'codigo'          => 'D.S. N° 092-2017-PCM',
                'descripcion'     => 'Define la Política Nacional de Integridad y Lucha contra la Corrupción, estableciendo los lineamientos y mecanismos para la promoción de la integridad pública y la prevención de actos de corrupción en las entidades del Estado.',
                'tipo'            => 'decreto',
                'alcance'         => 'nacional',
                'modulo'          => 'integridad',
                'link_externo'    => 'https://www.gob.pe/institucion/pcm/normas-legales/14626-092-2017-pcm',
                'fecha_emision'   => '2017-09-14',
                'vigente'         => true,
                'entidad_emisora' => 'Presidencia del Consejo de Ministros (PCM)',
                'orden'           => 6,
                'creado_por'      => $creadorId,
            ],
            [
                'nombre'          => 'Resolución Directoral N° 001-2026-UGEL-HCB — Constitución del Comité de Control Interno 2026',
                'codigo'          => 'RD-001-2026-UGEL-HCB',
                'descripcion'     => 'Resuelve la constitución del Comité de Control Interno de la UGEL Huacaybamba para el ejercicio 2026, designando a sus miembros titulares y alternos, y estableciendo sus funciones conforme a la Directiva N° 006-2019-CG/INTEG.',
                'tipo'            => 'resolucion',
                'alcance'         => 'institucional',
                'modulo'          => 'sci',
                'fecha_emision'   => '2026-01-20',
                'vigente'         => true,
                'fecha_vigencia'  => '2026-12-31',
                'entidad_emisora' => 'UGEL Huacaybamba — Dirección',
                'observacion'     => 'Documento institucional de constitución del Comité. Vigente hasta el 31/12/2026.',
                'orden'           => 7,
                'creado_por'      => $creadorId,
            ],
            [
                'nombre'          => 'Ley N° 27806 — Ley de Transparencia y Acceso a la Información Pública',
                'codigo'          => 'Ley N° 27806',
                'descripcion'     => 'Tiene por finalidad promover la transparencia de los actos del Estado y regular el derecho fundamental del acceso a la información. Establece los plazos y formatos para la publicación de información en el Portal de Transparencia.',
                'tipo'            => 'ley',
                'alcance'         => 'nacional',
                'modulo'          => 'integridad',
                'link_externo'    => 'https://www.gob.pe/institucion/minjusdh/normas-legales/269977-27806',
                'fecha_emision'   => '2002-08-02',
                'vigente'         => true,
                'entidad_emisora' => 'Congreso de la República del Perú',
                'orden'           => 8,
                'creado_por'      => $creadorId,
            ],
        ];

        foreach ($normativas as $datos) {
            Normativa::updateOrCreate(
                ['codigo' => $datos['codigo'], 'nombre' => $datos['nombre']],
                $datos
            );
        }

        $this->command->info('✓ Normativas sembradas: ' . count($normativas) . ' registros para UGEL Huacaybamba.');
    }
}
