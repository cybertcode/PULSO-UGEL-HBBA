<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Componente;
use App\Models\UnidadOrganica;
use App\Models\Actividad;
use App\Models\ConfiguracionInstitucional;

class SemaforoController extends Controller
{
    public function index()
    {
        $config = ConfiguracionInstitucional::first();
        $umbral_verde    = $config->umbral_verde    ?? 75;
        $umbral_amarillo = $config->umbral_amarillo ?? 50;

        $componentes = Componente::withCount([
            'actividades',
            'actividades as completadas_count' => fn($q) => $q->where('estado', 'completada'),
        ])->where('activo', true)->orderBy('numero')->get()
          ->map(function ($c) use ($umbral_verde, $umbral_amarillo) {
              $c->porcentaje = $c->actividades_count > 0
                  ? round(($c->completadas_count / $c->actividades_count) * 100) : 0;
              $c->color    = $c->porcentaje >= $umbral_verde    ? 'success'
                           : ($c->porcentaje >= $umbral_amarillo ? 'warning' : 'danger');
              $c->semaforo = $c->porcentaje >= $umbral_verde    ? 'Verde'
                           : ($c->porcentaje >= $umbral_amarillo ? 'Amarillo' : 'Rojo');
              return $c;
          });

        $unidades = UnidadOrganica::withCount([
            'actividades',
            'actividades as completadas_count' => fn($q) => $q->where('estado', 'completada'),
        ])->where('activo', true)->get()
          ->map(function ($u) use ($umbral_verde, $umbral_amarillo) {
              $u->porcentaje = $u->actividades_count > 0
                  ? round(($u->completadas_count / $u->actividades_count) * 100) : 0;
              $u->color    = $u->porcentaje >= $umbral_verde    ? 'success'
                           : ($u->porcentaje >= $umbral_amarillo ? 'warning' : 'danger');
              $u->semaforo = $u->porcentaje >= $umbral_verde    ? 'Verde'
                           : ($u->porcentaje >= $umbral_amarillo ? 'Amarillo' : 'Rojo');
              return $u;
          })->sortByDesc('porcentaje')->values();

        $avance_global = $componentes->avg('porcentaje');

        return view('content.semaforo.index', compact(
            'componentes', 'unidades', 'avance_global', 'umbral_verde', 'umbral_amarillo'
        ));
    }
}
