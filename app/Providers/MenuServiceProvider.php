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
        // Menú horizontal (sin cambios)
        $horizontalMenuJson = file_get_contents(base_path('resources/menu/horizontalMenu.json'));
        $horizontalMenuData = json_decode($horizontalMenuJson);

        // Menú vertical dinámico según permisos
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

            // Panel principal — siempre visible
            $menu[] = (object)[
                'url'  => '/',
                'name' => 'Panel Principal',
                'icon' => 'menu-icon icon-base ti tabler-smart-home',
                'slug' => 'dashboard',
            ];

            // Control y Seguimiento
            $sciSubmenu = [];
            $sciSlugs   = [];
            if (Gate::check('control-interno.ver')) {
                $sciSubmenu[] = (object)['url'=>'/control-interno','name'=>'Control Interno','icon'=>'menu-icon icon-base ti tabler-clipboard-check','slug'=>'sci-control-interno'];
                $sciSlugs[]   = 'sci-control-interno';
            }
            if (Gate::check('integridad.ver')) {
                $sciSubmenu[] = (object)['url'=>'/modelo-integridad','name'=>'Modelo de Integridad','icon'=>'menu-icon icon-base ti tabler-shield-check','slug'=>'sci-modelo-integridad'];
                $sciSlugs[]   = 'sci-modelo-integridad';
            }
            if (Gate::check('evidencias.ver')) {
                $sciSubmenu[] = (object)['url'=>'/evidencias','name'=>'Evidencias','icon'=>'menu-icon icon-base ti tabler-file-upload','slug'=>'sci-evidencias'];
                $sciSlugs[]   = 'sci-evidencias';
            }
            if (!empty($sciSubmenu)) {
                $menu[] = (object)['name'=>'Control y Seguimiento','icon'=>'menu-icon icon-base ti tabler-clipboard-list','slug'=>$sciSlugs,'submenu'=>$sciSubmenu];
            }

            // Monitoreo
            $monSubmenu = [];
            $monSlugs   = ['mon-semaforo', 'mon-avance-unidades', 'mon-ranking-unidades'];
            $monSubmenu[] = (object)['url'=>'/semaforo','name'=>'Semáforo Institucional','icon'=>'menu-icon icon-base ti tabler-traffic-lights','slug'=>'mon-semaforo'];
            if (Gate::check('alertas.ver')) {
                $monSubmenu[] = (object)['url'=>'/alertas','name'=>'Alertas','icon'=>'menu-icon icon-base ti tabler-bell','slug'=>'mon-alertas'];
                $monSlugs[]   = 'mon-alertas';
            }
            $monSubmenu[] = (object)['url'=>'/avance-unidades','name'=>'Avance por Unidades','icon'=>'menu-icon icon-base ti tabler-building-community','slug'=>'mon-avance-unidades'];
            $monSubmenu[] = (object)['url'=>'/ranking-unidades','name'=>'Ranking de Unidades','icon'=>'menu-icon icon-base ti tabler-award','slug'=>'mon-ranking-unidades'];
            $menu[] = (object)['name'=>'Monitoreo','icon'=>'menu-icon icon-base ti tabler-chart-line','slug'=>$monSlugs,'submenu'=>$monSubmenu];

            // Reportes y Reconocimientos
            $repSubmenu = [];
            $repSlugs   = [];
            if (Gate::check('reportes.ver')) {
                $repSubmenu[] = (object)['url'=>'/reportes','name'=>'Reportes','icon'=>'menu-icon icon-base ti tabler-chart-bar','slug'=>'rep-reportes'];
                $repSlugs[]   = 'rep-reportes';
            }
            if (Gate::check('reconocimientos.ver')) {
                $repSubmenu[] = (object)['url'=>'/reconocimientos','name'=>'Reconocimientos','icon'=>'menu-icon icon-base ti tabler-trophy','slug'=>'rep-reconocimientos'];
                $repSlugs[]   = 'rep-reconocimientos';
            }
            if (!empty($repSubmenu)) {
                $menu[] = (object)['name'=>'Reportes','icon'=>'menu-icon icon-base ti tabler-chart-bar','slug'=>$repSlugs,'submenu'=>$repSubmenu];
            }

            // Gestión de Usuarios
            $usuSubmenu = [];
            $usuSlugs   = [];
            if (Gate::check('usuarios.ver')) {
                $usuSubmenu[] = (object)['url'=>'/usuarios','name'=>'Usuarios','icon'=>'menu-icon icon-base ti tabler-users','slug'=>'adm-usuarios'];
                $usuSlugs[]   = 'adm-usuarios';
            }
            if (Gate::check('configuracion.ver')) {
                $usuSubmenu[] = (object)['url'=>'/roles','name'=>'Roles','icon'=>'menu-icon icon-base ti tabler-user-check','slug'=>'adm-roles'];
                $usuSubmenu[] = (object)['url'=>'/permisos','name'=>'Permisos','icon'=>'menu-icon icon-base ti tabler-shield-lock','slug'=>'adm-permisos'];
                $usuSlugs[]   = 'adm-roles';
                $usuSlugs[]   = 'adm-permisos';
            }
            if (!empty($usuSubmenu)) {
                $menu[] = (object)['name'=>'Gestión de Usuarios','icon'=>'menu-icon icon-base ti tabler-users-group','slug'=>$usuSlugs,'submenu'=>$usuSubmenu];
            }

            // Configuración del Sistema
            $cfgSubmenu = [];
            $cfgSlugs   = [];
            if (Gate::check('componentes.ver')) {
                $cfgSubmenu[] = (object)['url'=>'/administracion/componentes','name'=>'Componentes SCI','icon'=>'menu-icon icon-base ti tabler-layout-grid','slug'=>'adm-componentes'];
                $cfgSlugs[]   = 'adm-componentes';
            }
            if (Gate::check('configuracion.ver')) {
                $cfgSubmenu[] = (object)['url'=>'/unidades-organicas','name'=>'Unidades Orgánicas','icon'=>'menu-icon icon-base ti tabler-sitemap','slug'=>'adm-unidades'];
                $cfgSubmenu[] = (object)['url'=>'/configuracion','name'=>'Configuración Institucional','icon'=>'menu-icon icon-base ti tabler-building-cog','slug'=>'adm-configuracion'];
                $cfgSlugs[]   = 'adm-unidades';
                $cfgSlugs[]   = 'adm-configuracion';
            }
            if (!empty($cfgSubmenu)) {
                $menu[] = (object)['name'=>'Configuración del Sistema','icon'=>'menu-icon icon-base ti tabler-settings-2','slug'=>$cfgSlugs,'submenu'=>$cfgSubmenu];
            }

            $verticalMenuData = (object)['menu' => $menu];
            $view->with('menuData', [$verticalMenuData, $horizontalMenuData]);
        });
    }
}
