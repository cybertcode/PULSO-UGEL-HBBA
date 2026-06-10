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

            // Mis Actividades — visible para todos los usuarios autenticados
            $menu[] = (object)[
                'url'  => '/mis-actividades',
                'name' => 'Mis Actividades',
                'icon' => 'menu-icon icon-base ti tabler-checklist',
                'slug' => 'mis-actividades',
                'i18n' => 'Mis compromisos asignados',
            ];

            // ════════════════════════════════════════
            // CONTROL INTERNO
            // ════════════════════════════════════════
            $hasSci = Gate::check('control-interno.ver')
                   || Gate::check('integridad.ver')
                   || Gate::check('evidencias.ver');

            if ($hasSci) {
                $menu[] = (object)['menuHeader' => 'Control Interno'];

                if (Gate::check('control-interno.ver')) {
                    $menu[] = (object)[
                        'url'  => '/control-interno',
                        'name' => 'Actividades SCI',
                        'icon' => 'menu-icon icon-base ti tabler-clipboard-check',
                        'slug' => 'sci-control-interno',
                        'i18n' => 'Registro y seguimiento',
                    ];
                }

                if (Gate::check('evidencias.ver')) {
                    $menu[] = (object)[
                        'url'  => '/evidencias',
                        'name' => 'Evidencias',
                        'icon' => 'menu-icon icon-base ti tabler-files',
                        'slug' => 'sci-evidencias',
                        'i18n' => 'Documentos de respaldo',
                    ];
                }

                // Modelo de Integridad + Semáforo fusionados en un submenú
                if (Gate::check('integridad.ver')) {
                    $intSub = [];
                    $intSub[] = (object)[
                        'url'  => '/modelo-integridad',
                        'name' => 'Vista General',
                        'slug' => 'sci-modelo-integridad',
                        'i18n' => '9 componentes PCM',
                    ];
                    $intSub[] = (object)[
                        'url'  => '/semaforo',
                        'name' => 'Semáforo',
                        'slug' => 'mon-semaforo',
                        'i18n' => 'Estado por unidad',
                    ];
                    $menu[] = (object)[
                        'name'    => 'Modelo de Integridad',
                        'icon'    => 'menu-icon icon-base ti tabler-shield-check',
                        'slug'    => ['sci-modelo-integridad', 'mon-semaforo'],
                        'i18n'    => 'PCM — 9 componentes',
                        'submenu' => $intSub,
                    ];
                }
            }

            // ════════════════════════════════════════
            // CUMPLIMIENTO (núcleo del objetivo SCI)
            // ════════════════════════════════════════
            if (Gate::check('control-interno.ver')) {
                $menu[] = (object)['menuHeader' => 'Cumplimiento'];

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

            // Avance y Ranking — requiere permiso de reportes
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
            }

            if (Gate::check('alertas.ver')) {
                $menu[] = (object)[
                    'url'  => '/alertas',
                    'name' => 'Alertas',
                    'icon' => 'menu-icon icon-base ti tabler-bell',
                    'slug' => 'mon-alertas',
                    'i18n' => 'Notificaciones activas',
                ];
            }

            // ════════════════════════════════════════
            // PLANIFICACIÓN SCI
            // ════════════════════════════════════════
            $hasPlan = Gate::check('paci.ver') || Gate::check('riesgos.ver');

            if ($hasPlan) {
                $menu[] = (object)['menuHeader' => 'Planificación SCI'];

                if (Gate::check('paci.ver')) {
                    $menu[] = (object)[
                        'url'  => '/paci',
                        'name' => 'PACI',
                        'icon' => 'menu-icon icon-base ti tabler-file-description',
                        'slug' => 'paci.index',
                        'i18n' => 'Plan Anual de Control Interno',
                    ];
                }

                if (Gate::check('riesgos.ver')) {
                    $menu[] = (object)[
                        'url'  => '/matriz-riesgos',
                        'name' => 'Matriz de Riesgos',
                        'icon' => 'menu-icon icon-base ti tabler-shield-exclamation',
                        'slug' => 'matriz-riesgos.index',
                        'i18n' => 'Identificación y tratamiento',
                    ];
                }

                if (Gate::check('actas.ver')) {
                    $menu[] = (object)[
                        'url'  => '/actas-comite',
                        'name' => 'Actas del Comité',
                        'icon' => 'menu-icon icon-base ti tabler-notebook',
                        'slug' => 'actas-comite.index',
                        'i18n' => 'Sesiones del comité SCI',
                    ];
                }

                if (Gate::check('autoevaluacion.ver')) {
                    $menu[] = (object)[
                        'url'  => '/autoevaluacion',
                        'name' => 'Autoevaluación',
                        'icon' => 'menu-icon icon-base ti tabler-clipboard-list',
                        'slug' => 'autoevaluacion.index',
                        'i18n' => 'Cuestionarios COSO',
                    ];
                }
            }

            // ════════════════════════════════════════
            // GESTIÓN
            // ════════════════════════════════════════
            $hasGestion = Gate::check('recomendaciones.ver')
                       || Gate::check('buenas-practicas.ver')
                       || Gate::check('reconocimientos.ver');

            if ($hasGestion) {
                $menu[] = (object)['menuHeader' => 'Gestión'];
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

            // ════════════════════════════════════════
            // REPORTES
            // ════════════════════════════════════════
            if (Gate::check('reportes.ver')) {
                $menu[] = (object)['menuHeader' => 'Reportes'];

                $menu[] = (object)[
                    'url'  => '/reportes',
                    'name' => 'Reportes Generales',
                    'icon' => 'menu-icon icon-base ti tabler-table-export',
                    'slug' => 'rep-reportes',
                    'i18n' => 'Excel y PDF por actividad',
                ];

                $menu[] = (object)[
                    'url'  => '/cumplimiento/responsables',
                    'name' => 'Reporte Cumplimiento',
                    'icon' => 'menu-icon icon-base ti tabler-user-check',
                    'slug' => 'cumplimiento.responsables',
                    'i18n' => 'Exportar por responsable',
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
                        'url'  => '/administracion/componentes',
                        'name' => 'Componentes SCI',
                        'icon' => 'menu-icon icon-base ti tabler-layout-grid',
                        'slug' => 'adm-componentes',
                        'i18n' => 'Catálogo del modelo',
                    ];

                    $menu[] = (object)[
                        'url'  => '/configuracion',
                        'name' => 'Configuración',
                        'icon' => 'menu-icon icon-base ti tabler-settings',
                        'slug' => 'adm-configuracion',
                        'i18n' => 'Parámetros del sistema',
                    ];

                    $menu[] = (object)[
                        'url'  => '/slider-landing',
                        'name' => 'Slider del Landing',
                        'icon' => 'menu-icon icon-base ti tabler-slideshow',
                        'slug' => 'slider-landing.index',
                        'i18n' => 'Slides de la página principal',
                    ];

                    $menu[] = (object)[
                        'url'  => '/instituciones-vinculadas',
                        'name' => 'Instituciones Vinculadas',
                        'icon' => 'menu-icon icon-base ti tabler-building-community',
                        'slug' => 'instituciones-vinculadas.index',
                        'i18n' => 'Logos e instituciones del landing',
                    ];
                }
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
