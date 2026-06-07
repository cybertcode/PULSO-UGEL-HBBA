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
            if (Gate::check('control-interno.ver')) {
                $sciSubmenu[] = (object)['url'=>'/control-interno','name'=>'Control Interno','icon'=>'menu-icon icon-base ti tabler-clipboard-check','slug'=>'sci-control-interno'];
            }
            if (Gate::check('integridad.ver')) {
                $sciSubmenu[] = (object)['url'=>'/modelo-integridad','name'=>'Modelo de Integridad','icon'=>'menu-icon icon-base ti tabler-shield-check','slug'=>'sci-modelo-integridad'];
            }
            if (Gate::check('evidencias.ver')) {
                $sciSubmenu[] = (object)['url'=>'/evidencias','name'=>'Evidencias','icon'=>'menu-icon icon-base ti tabler-file-upload','slug'=>'sci-evidencias'];
            }
            if (!empty($sciSubmenu)) {
                $menu[] = (object)['name'=>'Control y Seguimiento','icon'=>'menu-icon icon-base ti tabler-clipboard-list','slug'=>'sci','submenu'=>$sciSubmenu];
            }

            // Monitoreo
            $monSubmenu = [];
            $monSubmenu[] = (object)['url'=>'/semaforo','name'=>'Semáforo Institucional','icon'=>'menu-icon icon-base ti tabler-traffic-lights','slug'=>'mon-semaforo'];
            if (Gate::check('alertas.ver')) {
                $monSubmenu[] = (object)['url'=>'/alertas','name'=>'Alertas','icon'=>'menu-icon icon-base ti tabler-bell','slug'=>'mon-alertas'];
            }
            $monSubmenu[] = (object)['url'=>'/avance-unidades','name'=>'Avance por Unidades','icon'=>'menu-icon icon-base ti tabler-building-community','slug'=>'mon-avance-unidades'];
            $monSubmenu[] = (object)['url'=>'/ranking-unidades','name'=>'Ranking de Unidades','icon'=>'menu-icon icon-base ti tabler-award','slug'=>'mon-ranking-unidades'];
            $menu[] = (object)['name'=>'Monitoreo','icon'=>'menu-icon icon-base ti tabler-chart-line','slug'=>'mon','submenu'=>$monSubmenu];

            // Reportes y Reconocimientos
            $repSubmenu = [];
            if (Gate::check('reportes.ver')) {
                $repSubmenu[] = (object)['url'=>'/reportes','name'=>'Reportes','icon'=>'menu-icon icon-base ti tabler-chart-bar','slug'=>'rep-reportes'];
            }
            if (Gate::check('reconocimientos.ver')) {
                $repSubmenu[] = (object)['url'=>'/reconocimientos','name'=>'Reconocimientos','icon'=>'menu-icon icon-base ti tabler-trophy','slug'=>'rep-reconocimientos'];
            }
            if (!empty($repSubmenu)) {
                $menu[] = (object)['name'=>'Reportes','icon'=>'menu-icon icon-base ti tabler-chart-bar','slug'=>'rep','submenu'=>$repSubmenu];
            }

            // Administración
            $admSubmenu = [];
            if (Gate::check('usuarios.ver')) {
                $admSubmenu[] = (object)['url'=>'/usuarios','name'=>'Usuarios','icon'=>'menu-icon icon-base ti tabler-users','slug'=>'adm-usuarios'];
            }
            if (Gate::check('componentes.ver')) {
                $admSubmenu[] = (object)['url'=>'/administracion/componentes','name'=>'Componentes SCI','icon'=>'menu-icon icon-base ti tabler-layout-grid','slug'=>'adm-componentes'];
            }
            if (Gate::check('configuracion.ver')) {
                $admSubmenu[] = (object)['url'=>'/roles','name'=>'Roles','icon'=>'menu-icon icon-base ti tabler-user-check','slug'=>'adm-roles'];
                $admSubmenu[] = (object)['url'=>'/permisos','name'=>'Permisos','icon'=>'menu-icon icon-base ti tabler-lock','slug'=>'adm-permisos'];
                $admSubmenu[] = (object)['url'=>'/configuracion','name'=>'Configuración','icon'=>'menu-icon icon-base ti tabler-settings','slug'=>'adm-configuracion'];
            }
            if (!empty($admSubmenu)) {
                $menu[] = (object)['name'=>'Administración','icon'=>'menu-icon icon-base ti tabler-settings-2','slug'=>'adm','submenu'=>$admSubmenu];
            }

            $verticalMenuData = (object)['menu' => $menu];
            $view->with('menuData', [$verticalMenuData, $horizontalMenuData]);
        });
    }
}
