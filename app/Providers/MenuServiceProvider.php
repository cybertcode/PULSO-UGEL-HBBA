<?php

namespace App\Providers;

use App\Models\ConfiguracionInstitucional;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $horizontalMenuJson = file_get_contents(base_path('resources/menu/horizontalMenu.json'));
        $horizontalMenuData = json_decode($horizontalMenuJson);

        View::composer('*', function ($view) use ($horizontalMenuData) {
            if (!Auth::check()) {
                $verticalMenuData = json_decode(file_get_contents(base_path('resources/menu/verticalMenu.json')));
                $view->with('menuData', [$verticalMenuData, $horizontalMenuData]);
                $view->with('configInstitucional', null);
                return;
            }

            $configInstitucional = ConfiguracionInstitucional::first();
            $view->with('configInstitucional', $configInstitucional);

            $menu = [];

            // ════════════════════════════════════════
            // GENERAL
            // ════════════════════════════════════════
            $menu[] = (object)['menuHeader' => 'General'];

            $menu[] = (object)[
                'url'  => '/dashboard',
                'name' => 'Inicio',
                'icon' => 'menu-icon icon-base ti tabler-smart-home',
                'slug' => 'dashboard',
                'i18n' => 'Resumen y estadísticas',
            ];

            $menu[] = (object)[
                'url'  => '/mis-actividades',
                'name' => 'Mis Actividades',
                'icon' => 'menu-icon icon-base ti tabler-checklist',
                'slug' => 'mis-actividades',
                'i18n' => 'Mis compromisos asignados',
            ];

            // ════════════════════════════════════════
            // SISTEMA DE CONTROL INTERNO
            // ════════════════════════════════════════
            if (Gate::check('control-interno.ver')) {
                $menu[] = (object)['menuHeader' => 'Sistema de Control Interno'];

                $menu[] = (object)[
                    'url'  => '/control-interno',
                    'name' => 'Actividades SCI',
                    'icon' => 'menu-icon icon-base ti tabler-clipboard-check',
                    'slug' => 'sci-control-interno',
                    'i18n' => 'Registro y seguimiento',
                ];
            }

            // ════════════════════════════════════════
            // MODELO DE INTEGRIDAD
            // ════════════════════════════════════════
            if (Gate::check('integridad.ver')) {
                $menu[] = (object)['menuHeader' => 'Modelo de Integridad'];

                $menu[] = (object)[
                    'url'  => '/modelo-integridad',
                    'name' => 'Actividades Integridad',
                    'icon' => 'menu-icon icon-base ti tabler-shield-check',
                    'slug' => 'sci-modelo-integridad',
                    'i18n' => 'Registro y seguimiento',
                ];
            }

            // ════════════════════════════════════════
            // MONITOREO  (compartido SCI + Integridad)
            // ════════════════════════════════════════
            $hasMonitoreo = Gate::check('control-interno.ver')
                         || Gate::check('integridad.ver')
                         || Gate::check('evidencias.ver')
                         || Gate::check('alertas.ver');

            if ($hasMonitoreo) {
                $menu[] = (object)['menuHeader' => 'Monitoreo'];

                if (Gate::check('control-interno.ver') || Gate::check('integridad.ver')) {
                    $menu[] = (object)[
                        'url'  => '/semaforo',
                        'name' => 'Semáforo',
                        'icon' => 'menu-icon icon-base ti tabler-traffic-lights',
                        'slug' => 'sci-semaforo',
                        'i18n' => 'SCI e Integridad',
                    ];
                }

                if (Gate::check('alertas.ver')) {
                    $menu[] = (object)[
                        'url'  => '/alertas',
                        'name' => 'Alertas',
                        'icon' => 'menu-icon icon-base ti tabler-bell',
                        'slug' => 'mon-alertas',
                        'i18n' => 'SCI e Integridad',
                    ];
                }

                if (Gate::check('evidencias.ver')) {
                    $menu[] = (object)[
                        'url'  => '/evidencias',
                        'name' => 'Evidencias',
                        'icon' => 'menu-icon icon-base ti tabler-files',
                        'slug' => 'sci-evidencias',
                        'i18n' => 'SCI e Integridad',
                    ];
                }
            }

            // ════════════════════════════════════════
            // SEGUIMIENTO Y ANÁLISIS (compartido SCI + Integridad)
            // ════════════════════════════════════════
            $hasSeguimiento = Gate::check('control-interno.ver')
                           || Gate::check('reportes.ver')
                           || Gate::check('recomendaciones.ver')
                           || Gate::check('buenas-practicas.ver')
                           || Gate::check('reconocimientos.ver');

            if ($hasSeguimiento) {
                $menu[] = (object)['menuHeader' => 'Seguimiento y Análisis'];

                if (Gate::check('control-interno.ver')) {
                    $cumSub = [];
                    $cumSub[] = (object)[
                        'url'  => '/cumplimiento/panel',
                        'name' => 'Panel de Control',
                        'slug' => 'cumplimiento.panel',
                        'i18n' => 'Resumen ejecutivo',
                    ];
                    $cumSub[] = (object)[
                        'url'  => '/cumplimiento/responsables',
                        'name' => 'Por Responsable',
                        'slug' => 'cumplimiento.responsables',
                        'i18n' => '¿Quién cumple y quién no?',
                    ];
                    $cumSub[] = (object)[
                        'url'  => '/cumplimiento/sin-evidencia',
                        'name' => 'Sin Evidencia',
                        'slug' => 'cumplimiento.sin-evidencia',
                        'i18n' => 'Actividades sin documentar',
                    ];
                    $menu[] = (object)[
                        'name'    => 'Seguimiento SCI',
                        'icon'    => 'menu-icon icon-base ti tabler-chart-dots',
                        'slug'    => ['cumplimiento.panel', 'cumplimiento.responsables', 'cumplimiento.sin-evidencia'],
                        'i18n'    => 'Plazos y evidencias',
                        'submenu' => $cumSub,
                    ];
                }

                if (Gate::check('reportes.ver')) {
                    $analisisSub = [];
                    $analisisSub[] = (object)[
                        'url'  => '/avance-unidades',
                        'name' => 'Avance por Unidades',
                        'slug' => 'mon-avance-unidades',
                        'i18n' => 'Seguimiento por área',
                    ];
                    $analisisSub[] = (object)[
                        'url'  => '/ranking-unidades',
                        'name' => 'Ranking',
                        'slug' => 'mon-ranking-unidades',
                        'i18n' => 'Clasificación mensual',
                    ];
                    $menu[] = (object)[
                        'name'    => 'Análisis',
                        'icon'    => 'menu-icon icon-base ti tabler-chart-bar',
                        'slug'    => ['mon-avance-unidades', 'mon-ranking-unidades'],
                        'i18n'    => 'Avance y ranking',
                        'submenu' => $analisisSub,
                    ];

                    $menu[] = (object)[
                        'url'  => '/reportes',
                        'name' => 'Reportes Generales',
                        'icon' => 'menu-icon icon-base ti tabler-table-export',
                        'slug' => 'rep-reportes',
                        'i18n' => 'Excel y PDF por actividad',
                    ];
                }

                if (Gate::check('recomendaciones.ver')) {
                    $menu[] = (object)[
                        'url'  => '/recomendaciones',
                        'name' => 'Recomendaciones',
                        'icon' => 'menu-icon icon-base ti tabler-message-report',
                        'slug' => 'recomendaciones',
                        'i18n' => 'Observaciones y mejoras',
                    ];
                }

                if (Gate::check('buenas-practicas.ver')) {
                    $menu[] = (object)[
                        'url'  => '/buenas-practicas',
                        'name' => 'Buenas Prácticas',
                        'icon' => 'menu-icon icon-base ti tabler-rosette-discount-check',
                        'slug' => 'buenas-practicas',
                        'i18n' => 'Prácticas positivas',
                    ];
                }

                if (Gate::check('reconocimientos.ver')) {
                    $menu[] = (object)[
                        'url'  => '/reconocimientos',
                        'name' => 'Reconocimientos',
                        'icon' => 'menu-icon icon-base ti tabler-trophy',
                        'slug' => 'rep-reconocimientos',
                        'i18n' => 'Trabajadores destacados',
                    ];
                }
            }


            // ════════════════════════════════════════
            // ENCUESTAS
            // ════════════════════════════════════════
            if (Gate::check('encuesta.ver') || Gate::check('encuesta.responder')) {
                $menu[] = (object)['menuHeader' => 'Encuestas Institucionales'];

                $encSub = [];
                $encSub[] = (object)[
                    'url'  => '/encuestas',
                    'name' => 'Todas las encuestas',
                    'slug' => 'encuestas-index',
                    'i18n' => 'Listado y gestión',
                ];
                if (Gate::check('encuesta.crear')) {
                    $encSub[] = (object)[
                        'url'  => '/encuestas/crear',
                        'name' => 'Nueva Encuesta',
                        'slug' => 'encuestas-crear',
                        'i18n' => 'Crear encuesta',
                    ];
                }

                $menu[] = (object)[
                    'name'    => 'Encuestas',
                    'icon'    => 'menu-icon icon-base ti tabler-forms',
                    'slug'    => ['encuestas-index', 'encuestas-crear'],
                    'i18n'    => 'Formularios y estadísticas',
                    'submenu' => $encSub,
                ];
            }

            // ════════════════════════════════════════
            // ADMINISTRACIÓN
            // ════════════════════════════════════════
            $hasAdm = Gate::check('usuarios.ver') || Gate::check('configuracion.ver');

            if ($hasAdm) {
                $menu[] = (object)['menuHeader' => 'Administración'];

                if (Gate::check('usuarios.ver')) {
                    $menu[] = (object)[
                        'url'  => '/usuarios',
                        'name' => 'Usuarios',
                        'icon' => 'menu-icon icon-base ti tabler-users',
                        'slug' => 'adm-usuarios',
                        'i18n' => 'Cuentas y accesos',
                    ];
                }

                if (Gate::check('configuracion.ver')) {
                    $accSub = [];
                    $accSub[] = (object)[
                        'url'  => '/roles',
                        'name' => 'Roles',
                        'slug' => 'adm-roles',
                        'i18n' => 'Perfiles de usuario',
                    ];
                    $accSub[] = (object)[
                        'url'  => '/permisos',
                        'name' => 'Permisos',
                        'slug' => 'adm-permisos',
                        'i18n' => 'Control de acceso',
                    ];
                    $menu[] = (object)[
                        'name'    => 'Roles y Permisos',
                        'icon'    => 'menu-icon icon-base ti tabler-lock',
                        'slug'    => ['adm-roles', 'adm-permisos'],
                        'i18n'    => 'Seguridad del sistema',
                        'submenu' => $accSub,
                    ];

                    $menu[] = (object)[
                        'url'  => '/unidades-organicas',
                        'name' => 'Unidades Orgánicas',
                        'icon' => 'menu-icon icon-base ti tabler-sitemap',
                        'slug' => 'adm-unidades',
                        'i18n' => 'Estructura institucional',
                    ];

                    $menu[] = (object)[
                        'url'  => '/administracion/sci-estructura',
                        'name' => 'Estructura SCI',
                        'icon' => 'menu-icon icon-base ti tabler-list-tree',
                        'slug' => 'adm-sci-estructura',
                        'i18n' => 'Ejes, componentes y preguntas',
                    ];

                    $menu[] = (object)[
                        'url'  => '/administracion/integridad-estructura',
                        'name' => 'Estructura Integridad',
                        'icon' => 'menu-icon icon-base ti tabler-list-tree',
                        'slug' => 'adm-integridad-estructura',
                        'i18n' => 'Etapas, componentes y preguntas',
                    ];

                    $menu[] = (object)[
                        'url'  => '/configuracion',
                        'name' => 'Configuración',
                        'icon' => 'menu-icon icon-base ti tabler-settings',
                        'slug' => 'adm-configuracion',
                        'i18n' => 'Parámetros del sistema',
                    ];
                }
            }

            // ════════════════════════════════════════
            // LANDING
            // ════════════════════════════════════════
            if (Gate::check('configuracion.ver')) {
                $menu[] = (object)['menuHeader' => 'Landing'];

                $menu[] = (object)[
                    'url'  => '/slider-landing',
                    'name' => 'Slider',
                    'icon' => 'menu-icon icon-base ti tabler-slideshow',
                    'slug' => 'slider-landing.index',
                    'i18n' => 'Slides de la página principal',
                ];

                $menu[] = (object)[
                    'url'  => '/instituciones-vinculadas',
                    'name' => 'Instituciones Vinculadas',
                    'icon' => 'menu-icon icon-base ti tabler-building-community',
                    'slug' => 'instituciones-vinculadas.index',
                    'i18n' => 'Logos e instituciones',
                ];
            }

            // ════════════════════════════════════════
            // SOPORTE
            // ════════════════════════════════════════
            $menu[] = (object)['menuHeader' => 'Soporte'];

            $menu[] = (object)[
                'url'  => '/ayuda',
                'name' => 'Ayuda',
                'icon' => 'menu-icon icon-base ti tabler-help-circle',
                'slug' => 'ayuda',
                'i18n' => 'Guías y documentación',
            ];

            $verticalMenuData = (object)['menu' => $menu];
            $view->with('menuData', [$verticalMenuData, $horizontalMenuData]);
        });
    }
}
