<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Alerta;
use App\Models\Componente;
use App\Models\ConfiguracionInstitucional;
use App\Models\Evidencia;
use App\Support\SemaforoHelper;

class ModeloIntegridadController extends Controller
{
    public function index()
    {
        $config = ConfiguracionInstitucional::cached();
        [$umbral_verde, $umbral_amarillo] = SemaforoHelper::umbrales($config);

        $componentes = Componente::withCount([
            'actividades',
            'actividades as completadas_count' => fn($q) => $q->where('estado', 'completada'),
            'actividades as en_proceso_count'  => fn($q) => $q->where('estado', 'en_proceso'),
            'actividades as vencidas_count'    => fn($q) => $q->whereNotIn('estado', ['completada', 'observado'])
                                                               ->whereDate('fecha_limite', '<', now()),
        ])->where('activo', true)->orderBy('numero')->get()
          ->map(function ($c) use ($config) {
              SemaforoHelper::decorar($c, 'actividades_count', 'completadas_count', $config, 'Cumplido', 'En proceso', 'En riesgo');
              $c->nivel            = $c->porcentaje >= $config->umbral_verde ? 'Bueno' : ($c->porcentaje >= $config->umbral_amarillo ? 'Regular' : 'En riesgo');
              $c->evidencias_count = Evidencia::whereHas('actividad', fn($q) => $q->where('componente_id', $c->id))->count();
              return $c;
          });

        $avance_global = round($componentes->avg('porcentaje') ?? 0);

        $en_avance = $componentes->where('porcentaje', '>=', $umbral_amarillo)->count();
        $en_riesgo = $componentes->where('porcentaje', '<', $umbral_amarillo)->where('porcentaje', '>', 0)->count();
        $criticos  = $componentes->where('porcentaje', 0)->count();

        $alertas_activas = Alerta::with(['actividad.componente', 'unidadOrganica'])
            ->where('leida', false)
            ->orderByRaw("FIELD(prioridad,'alta','media','baja')")
            ->limit(5)->get();

        $proximas_acciones = Actividad::with('componente')
            ->whereNotIn('estado', ['completada', 'observado'])
            ->whereDate('fecha_limite', '>=', now())
            ->orderBy('fecha_limite')
            ->limit(5)->get();

        $evidencias_recientes = Evidencia::with(['actividad.componente', 'subidoPor'])
            ->latest()
            ->limit(8)->get();

        return view('content.modelo-integridad.index', compact(
            'componentes', 'avance_global',
            'umbral_verde', 'umbral_amarillo',
            'en_avance', 'en_riesgo', 'criticos',
            'alertas_activas', 'proximas_acciones', 'evidencias_recientes'
        ));
    }
}
