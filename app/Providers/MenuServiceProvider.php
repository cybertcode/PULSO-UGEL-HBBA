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
                'url'  => '/',
                'name' => 'Inicio',
                'icon' => 'menu-icon icon-base ti tabler-smart-home',
                'slug' => 'dashboard',
                'i18n' => 'Resumen y estadísticas',
            ];

            // ════════════════════════════════════════
            // CONTROL Y SEGUIMIENTO SCI
            // ════════════════════════════════════════
            $hasSci = Gate::check('control-interno.ver')
                   || Gate::check('integridad.ver')
                   || Gate::check('evidencias.ver');

            if ($hasSci) {
                $menu[] = (object)['menuHeader' => 'Control y Seguimiento'];

                if (Gate::check('control-interno.ver')) {
                    $menu[] = (object)[
                        'url'  => '/control-interno',
                        'name' => 'Control Interno',
                        'icon' => 'menu-icon icon-base ti tabler-clipboard-check',
                        'slug' => 'sci-control-interno',
                        'i18n' => 'Actividades y compromisos',
                    ];
                }

                if (Gate::check('integridad.ver')) {
                    $intSub = [];
                    $intSub[] = (object)[
                        'url'  => '/modelo-integridad',
                        'name' => 'Vista General',
                        'slug' => 'sci-modelo-integridad',
                        'i18n' => 'Componentes y cumplimiento',
                    ];
                    $intSub[] = (object)[
                        'url'  => '/semaforo',
                        'name' => 'Semáforo Institucional',
                        'slug' => 'mon-semaforo',
                        'i18n' => 'Estado en tiempo real',
                    ];
                    $menu[] = (object)[
                        'name'    => 'Modelo de Integridad',
                        'icon'    => 'menu-icon icon-base ti tabler-shield-check',
                        'slug'    => ['sci-modelo-integridad', 'mon-semaforo'],
                        'i18n'    => 'PCM — 9 componentes',
                        'submenu' => $intSub,
                    ];
                }

                if (Gate::check('evidencias.ver')) {
                    $menu[] = (object)[
                        'url'  => '/evidencias',
                        'name' => 'Evidencias',
                        'icon' => 'menu-icon icon-base ti tabler-files',
                        'slug' => 'sci-evidencias',
                        'i18n' => 'Documentos y registros',
                    ];
                }
            }

            // ════════════════════════════════════════
            // MONITOREO
            // ════════════════════════════════════════
            $menu[] = (object)['menuHeader' => 'Monitoreo'];

            $menu[] = (object)[
                'url'  => '/avance-unidades',
                'name' => 'Avance por Unidades',
                'icon' => 'menu-icon icon-base ti tabler-building-community',
                'slug' => 'mon-avance-unidades',
                'i18n' => 'Seguimiento por área',
            ];

            $menu[] = (object)[
                'url'  => '/ranking-unidades',
                'name' => 'Ranking de Unidades',
                'icon' => 'menu-icon icon-base ti tabler-podium',
                'slug' => 'mon-ranking-unidades',
                'i18n' => 'Clasificación general',
            ];

            if (Gate::check('alertas.ver')) {
                $menu[] = (object)[
                    'url'  => '/alertas',
                    'name' => 'Alertas',
                    'icon' => 'menu-icon icon-base ti tabler-bell',
                    'slug' => 'mon-alertas',
                    'i18n' => 'Notificaciones y pendientes',
                ];
            }

            // ════════════════════════════════════════
            // GESTIÓN
            // ════════════════════════════════════════
            $menu[] = (object)['menuHeader' => 'Gestión'];

            $menu[] = (object)[
                'url'  => '/buenas-practicas',
                'name' => 'Buenas Prácticas',
                'icon' => 'menu-icon icon-base ti tabler-rosette-discount-check',
                'slug' => 'buenas-practicas',
                'i18n' => 'Registro y seguimiento',
            ];

            if (Gate::check('reconocimientos.ver')) {
                $menu[] = (object)[
                    'url'  => '/reconocimientos',
                    'name' => 'Reconocimientos',
                    'icon' => 'menu-icon icon-base ti tabler-trophy',
                    'slug' => 'rep-reconocimientos',
                    'i18n' => 'Premiación y destacados',
                ];
            }

            // ════════════════════════════════════════
            // REPORTES
            // ════════════════════════════════════════
            if (Gate::check('reportes.ver')) {
                $menu[] = (object)['menuHeader' => 'Reportes'];

                $menu[] = (object)[
                    'url'  => '/reportes',
                    'name' => 'Reportes',
                    'icon' => 'menu-icon icon-base ti tabler-chart-bar',
                    'slug' => 'rep-reportes',
                    'i18n' => 'Análisis y exportación',
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
