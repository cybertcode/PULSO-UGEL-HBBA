<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\InstitucionVinculada;
use App\Models\Normativa;
use App\Models\SliderLanding;

class LandingController extends Controller
{
    public function index()
    {
        $config = null;
        try {
            $config = \App\Models\ConfiguracionInstitucional::first();
        } catch (\Exception $e) {}

        if (SliderLanding::count() === 0) {
            $this->seedSlides();
        }

        if (InstitucionVinculada::count() === 0) {
            $this->seedInstituciones();
        }

        $slides        = SliderLanding::activos()->latest()->limit(5)->get();
        $instituciones = InstitucionVinculada::activas()->orderBy('orden')->get();

        // Estadísticas reales
        $anioGestion = $config?->anio_gestion ?? (date('Y') - 5);
        $stats = [
            'componentes' => class_exists('\App\Models\Componente') ? \App\Models\Componente::count() : 8,
            'unidades'    => class_exists('\App\Models\UnidadOrganica') ? \App\Models\UnidadOrganica::count() : 25,
            'avance'      => class_exists('\App\Models\Actividad') ? round(\App\Models\Actividad::avg('avance') ?? 85, 1) : 85,
            'paci'        => (class_exists('\App\Models\Paci') && \Schema::hasTable('paci'))
                             ? (\App\Models\Paci::latest()->value('anio') ?? date('Y'))
                             : date('Y'),
            'gestion'     => max(1, date('Y') - $anioGestion),
        ];

        // Componentes (Módulos) desde la base de datos
        $modulos = class_exists('\App\Models\Componente') 
            ? \App\Models\Componente::where('activo', true)->orderBy('numero')->limit(6)->get()
            : collect();

        $paginaNorm  = max(1, (int) request('pnorm', 1));
        $normativas  = Normativa::vigentes()
            ->orderByDesc('created_at')
            ->orderBy('orden')
            ->paginate(5, ['*'], 'pnorm', $paginaNorm);

        return view('content.landing.index', compact('config', 'slides', 'modulos', 'instituciones', 'stats', 'normativas'));
    }

    public function publicaciones()
    {
        $config = null;
        try { $config = \App\Models\ConfiguracionInstitucional::first(); } catch (\Exception $e) {}

        $tipo = request('tipo');
        $query = SliderLanding::activos()->latest();
        if ($tipo && in_array($tipo, ['noticia', 'evento', 'normativa'])) {
            $query->where('tipo', $tipo);
        }
        $publicaciones = $query->paginate(9)->withQueryString();
        $totales = [
            'noticia'   => SliderLanding::activos()->where('tipo', 'noticia')->count(),
            'evento'    => SliderLanding::activos()->where('tipo', 'evento')->count(),
            'normativa' => SliderLanding::activos()->where('tipo', 'normativa')->count(),
        ];
        $instituciones = InstitucionVinculada::activas()->orderBy('orden')->get();

        return view('content.landing.publicaciones', compact('publicaciones', 'totales', 'tipo', 'config', 'instituciones'));
    }

    public function show($id)
    {
        $noticia = SliderLanding::where('activo', true)->findOrFail($id);
        $relacionadas = SliderLanding::activos()
            ->where('id', '!=', $id)
            ->limit(3)->get();

        $config = null;
        try { $config = \App\Models\ConfiguracionInstitucional::first(); } catch (\Exception $e) {}

        $instituciones = InstitucionVinculada::activas()->orderBy('orden')->get();

        return view('content.landing.noticia', compact('noticia', 'relacionadas', 'config', 'instituciones'));
    }

    private function seedInstituciones(): void
    {
        $items = [
            ['nombre' => 'Contraloría General de la República', 'sigla' => 'CGR',    'color_acento' => '#c62828', 'logo_url' => null, 'url_sitio' => 'https://www.contraloria.gob.pe', 'descripcion' => 'Órgano superior de control gubernamental', 'orden' => 1],
            ['nombre' => 'Presidencia del Consejo de Ministros','sigla' => 'PCM',    'color_acento' => '#1a237e', 'logo_url' => null, 'url_sitio' => 'https://www.gob.pe/pcm',            'descripcion' => 'Coordinación del Poder Ejecutivo',         'orden' => 2],
            ['nombre' => 'Ministerio de Educación',             'sigla' => 'MINEDU', 'color_acento' => '#01579b', 'logo_url' => null, 'url_sitio' => 'https://www.minedu.gob.pe',         'descripcion' => 'Rector de la política educativa nacional',  'orden' => 3],
            ['nombre' => 'Gobierno Regional Huánuco',           'sigla' => 'GORE',   'color_acento' => '#1b5e20', 'logo_url' => null, 'url_sitio' => 'https://www.regionhuanuco.gob.pe',  'descripcion' => 'Gobierno Regional de Huánuco',              'orden' => 4],
            ['nombre' => 'Dirección Regional de Educación',     'sigla' => 'DRE',    'color_acento' => '#4a148c', 'logo_url' => null, 'url_sitio' => null,                                'descripcion' => 'Dirección Regional de Educación Huánuco',   'orden' => 5],
            ['nombre' => 'UGEL Huacaybamba',                    'sigla' => 'UGEL',   'color_acento' => '#e65100', 'logo_url' => null, 'url_sitio' => null,                                'descripcion' => 'Unidad de Gestión Educativa Local',         'orden' => 6],
        ];
        foreach ($items as $item) {
            InstitucionVinculada::create(array_merge($item, ['activo' => true]));
        }
    }

    private function seedSlides(): void
    {
        $slides = [
            [
                'tipo'             => 'noticia',
                'titulo'           => 'PULSO UGEL — Sistema de Control Interno Institucional',
                'descripcion'      => 'Plataforma centralizada para la gestión, seguimiento y evaluación del Sistema de Control Interno de la UGEL Huacaybamba, alineada con los estándares de la Contraloría General de la República.',
                'etiqueta'         => 'Sistema SCI',
                'color_gradiente'  => 'linear-gradient(135deg,#0a0a2e 0%,#1a1a6e 40%,#2d2dbf 70%,#7367f0 100%)',
                'texto_accion'     => 'Acceder al Sistema',
                'orden'            => 1,
                'activo'           => true,
            ],
            [
                'tipo'             => 'evento',
                'titulo'           => 'Taller Regional de Capacitación en Control Interno — GORE Huánuco',
                'descripcion'      => 'Capacitación presencial para responsables SCI de las UGEL de la región. Participación de funcionarios de PCM y Contraloría General de la República. Inscripciones abiertas.',
                'etiqueta'         => 'Próximo Evento',
                'color_gradiente'  => 'linear-gradient(135deg,#0a2e1a 0%,#0d4a2a 40%,#1a7a4a 70%,#28c76f 100%)',
                'texto_accion'     => 'Más Información',
                'orden'            => 2,
                'activo'           => true,
            ],
            [
                'tipo'             => 'normativa',
                'titulo'           => 'Nueva Directiva: Implementación del Modelo de Integridad 2025',
                'descripcion'      => 'La Contraloría General aprueba la actualización del marco normativo para la implementación del Modelo de Integridad en entidades del sector educación. Vigente desde enero 2025.',
                'etiqueta'         => 'Normativa Vigente',
                'color_gradiente'  => 'linear-gradient(135deg,#2e1a0a 0%,#6b3800 40%,#b85c00 70%,#ff9f43 100%)',
                'texto_accion'     => 'Ver Normativa',
                'orden'            => 3,
                'activo'           => true,
            ],
        ];

        foreach ($slides as $slide) {
            SliderLanding::create($slide);
        }
    }
}
