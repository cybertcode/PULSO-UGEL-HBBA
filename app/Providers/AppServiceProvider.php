<?php

namespace App\Providers;

use App\Models\Alerta;
use App\Models\ConfiguracionInstitucional;
use Illuminate\Support\Facades\Auth;
use App\Observers\AlertaObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        // Observer: envío síncrono de email al crear una alerta
        Alerta::observe(AlertaObserver::class);

        // Forzar español como locale de la aplicación
        App::setLocale('es');

        // Compartir configuración institucional con todas las vistas
        View::composer('*', function ($view) {
            try {
                $configInstitucional = ConfiguracionInstitucional::cached();
            } catch (\Exception $e) {
                $configInstitucional = null;
            }
            $view->with('configInstitucional', $configInstitucional);

            // Pre-cargar relaciones del usuario autenticado para navbar/sidebar
            $user = Auth::user();
            if ($user && !$user->relationLoaded('cargos')) {
                $user->load('cargos', 'roles', 'unidadOrganica');
            }
        });

        // Paginación con Bootstrap 5
        Paginator::useBootstrapFive();

        // Reglas de contraseña por defecto (usadas en registro, reset y cambio de clave)
        Password::defaults(fn () => Password::min(8)->mixedCase()->numbers());

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
