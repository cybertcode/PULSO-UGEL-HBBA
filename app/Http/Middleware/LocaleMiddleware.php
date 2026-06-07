<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    // Sistema configurado para Español (Perú) — permite cambio solo entre es/en
    if (session()->has('locale') && in_array(session()->get('locale'), ['es', 'en'])) {
      app()->setLocale(session()->get('locale'));
    } else {
      // Siempre Spanish por defecto — sistema institucional peruano
      app()->setLocale(config('app.locale', 'es'));
      session()->put('locale', config('app.locale', 'es'));
    }

    return $next($request);
  }
}
