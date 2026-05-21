<?php

namespace App\Providers;

use App\Models\ConfiguracionInstitucional;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forzar español como locale de la aplicación
        App::setLocale('es');

        // Compartir configuración institucional con todas las vistas
        View::composer('*', function ($view) {
            try {
                $configInstitucional = \Illuminate\Support\Facades\Cache::remember(
                    'config_institucional',
                    now()->addMinutes(60),
                    fn () => ConfiguracionInstitucional::first()
                );
            } catch (\Exception $e) {
                $configInstitucional = null;
            }
            $view->with('configInstitucional', $configInstitucional);
        });

        // Paginación con Bootstrap 5
        Paginator::useBootstrapFive();

        // Super Admin bypasses all permission checks via Gate
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        Vite::useStyleTagAttributes(function (?string $src, string $url, ?array $chunk, ?array $manifest) {
            if ($src !== null) {
                return [
                    'class' => preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?core)-?.*/i", $src) ? 'template-customizer-core-css' : (preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?theme)-?.*/i", $src) ? 'template-customizer-theme-css' : '')
                ];
            }
            return [];
        });
    }
}
