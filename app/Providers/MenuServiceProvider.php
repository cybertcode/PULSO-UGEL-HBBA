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

            // Inicio — siempre visible
            $menu[] = (object)['url'=>'/','name'=>'Inicio','icon'=>'menu-icon icon-base ti tabler-smart-home','slug'=>'dashboard','i18n'=>'Panel principal'];

            // Control Interno
            if (Gate::check('control-interno.ver')) {
                $menu[] = (object)['url'=>'/control-interno','name'=>'Control Interno','icon'=>'menu-icon icon-base ti tabler-clipboard-check','slug'=>'sci-control-interno','i18n'=>'Seguimiento de actividades'];
            }

            // Modelo de Integridad
            if (Gate::check('integridad.ver')) {
                $menu[] = (object)['url'=>'/modelo-integridad','name'=>'Modelo de Integridad','icon'=>'menu-icon icon-base ti tabler-shield-check','slug'=>'sci-modelo-integridad','i18n'=>'Monitoreo de acciones'];
            }

            // Buenas Prácticas — siempre visible
            $menu[] = (object)['url'=>'/buenas-practicas','name'=>'Buenas Prácticas','icon'=>'menu-icon icon-base ti tabler-rosette-discount-check','slug'=>'buenas-practicas','i18n'=>'Registro y seguimiento'];

            // Avance por Unidades — siempre visible
            $menu[] = (object)['url'=>'/avance-unidades','name'=>'Avance por Unidades','icon'=>'menu-icon icon-base ti tabler-building-community','slug'=>'mon-avance-unidades','i18n'=>'Seguimiento detallado'];

            // Reportes
            if (Gate::check('reportes.ver')) {
                $menu[] = (object)['url'=>'/reportes','name'=>'Reportes','icon'=>'menu-icon icon-base ti tabler-chart-bar','slug'=>'rep-reportes','i18n'=>'Reportes y analíticas'];
            }

            // Alertas
            if (Gate::check('alertas.ver')) {
                $menu[] = (object)['url'=>'/alertas','name'=>'Alertas','icon'=>'menu-icon icon-base ti tabler-bell','slug'=>'mon-alertas','i18n'=>'Notificaciones y pendientes'];
            }

            // Reconocimientos
            if (Gate::check('reconocimientos.ver')) {
                $menu[] = (object)['url'=>'/reconocimientos','name'=>'Reconocimientos','icon'=>'menu-icon icon-base ti tabler-trophy','slug'=>'rep-reconocimientos','i18n'=>'Premiación y destacados'];
            }

            // Configuración
            if (Gate::check('configuracion.ver')) {
                $menu[] = (object)['url'=>'/configuracion','name'=>'Configuración','icon'=>'menu-icon icon-base ti tabler-settings','slug'=>'adm-configuracion','i18n'=>'Administración del sistema'];
            }

            // Ayuda — siempre visible
            $menu[] = (object)['url'=>'/ayuda','name'=>'Ayuda','icon'=>'menu-icon icon-base ti tabler-help-circle','slug'=>'ayuda','i18n'=>'Guías y soporte'];

            $verticalMenuData = (object)['menu' => $menu];
            $view->with('menuData', [$verticalMenuData, $horizontalMenuData]);
        });
    }
}
