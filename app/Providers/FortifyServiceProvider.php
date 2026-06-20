<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\ConfiguracionInstitucional;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
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
        Fortify::loginView(function () {
            return view('content.authentications.auth-login-cover', [
                'pageConfigs'         => ['myLayout' => 'blank'],
                'configInstitucional' => ConfiguracionInstitucional::first(),
                'unidades'            => \App\Models\UnidadOrganica::where('activo', true)
                                            ->orderBy('nombre')
                                            ->get(['id', 'nombre', 'sigla', 'foto_ruta']),
            ]);
        });

        Fortify::registerView(function () {
            return view('content.authentications.auth-register-cover', [
                'pageConfigs'         => ['myLayout' => 'blank'],
                'configInstitucional' => ConfiguracionInstitucional::first(),
            ]);
        });

        Fortify::requestPasswordResetLinkView(function () {
            return view('content.authentications.auth-forgot-password-cover', [
                'pageConfigs'         => ['myLayout' => 'blank'],
                'configInstitucional' => ConfiguracionInstitucional::first(),
            ]);
        });

        Fortify::resetPasswordView(function (Request $request) {
            return view('content.authentications.auth-reset-password-cover', [
                'pageConfigs'         => ['myLayout' => 'blank'],
                'configInstitucional' => ConfiguracionInstitucional::first(),
                'request'             => $request,
            ]);
        });

        Fortify::verifyEmailView(function () {
            return view('content.authentications.auth-verify-email-cover', [
                'pageConfigs'         => ['myLayout' => 'blank'],
                'configInstitucional' => ConfiguracionInstitucional::first(),
            ]);
        });

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('passkeys', function (Request $request) {
            $credentialId = $request->input('credential.id');

            return Limit::perMinute(10)->by(
                ($credentialId ?: $request->session()->getId()).'|'.$request->ip()
            );
        });
    }
}
