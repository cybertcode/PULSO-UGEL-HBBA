<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Componente;
use App\Models\ConfiguracionInstitucional;
use App\Models\UnidadOrganica;
use App\Support\SemaforoHelper;

class SemaforoController extends Controller
{
    public function index()
    {
        $config = ConfiguracionInstitucional::cached();
        [$umbral_verde, $umbral_amarillo] = SemaforoHelper::umbrales($config);

        $componentes = Componente::withCount([
            'actividades',
            'actividades as completadas_count' => fn($q) => $q->where('estado', 'completada'),
        ])->where('activo', true)->orderBy('numero')->get()
          ->map(function ($c) use ($config) {
              SemaforoHelper::decorar($c, 'actividades_count', 'completadas_count', $config, 'Verde', 'Amarillo', 'Rojo');
              return $c;
          });

        $unidades = UnidadOrganica::withCount([
            'actividades',
            'actividades as completadas_count' => fn($q) => $q->where('estado', 'completada'),
        ])->where('activo', true)->get()
          ->map(function ($u) use ($config) {
              SemaforoHelper::decorar($u, 'actividades_count', 'completadas_count', $config, 'Verde', 'Amarillo', 'Rojo');
              return $u;
          })->sortByDesc('porcentaje')->values();

        $avance_global = $componentes->avg('porcentaje');

        return view('content.semaforo.index', compact(
            'componentes', 'unidades', 'avance_global', 'umbral_verde', 'umbral_amarillo'
        ));
    }
}
