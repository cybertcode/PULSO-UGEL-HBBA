<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Componente;
use App\Models\Actividad;
use App\Models\Alerta;
use App\Models\Evidencia;
use App\Models\ConfiguracionInstitucional;

class ModeloIntegridadController extends Controller
{
    public function index()
    {
        $config          = ConfiguracionInstitucional::first();
        $umbral_verde    = $config->umbral_verde    ?? 75;
        $umbral_amarillo = $config->umbral_amarillo ?? 50;

        $componentes = Componente::withCount([
            'actividades',
            'actividades as completadas_count' => fn($q) => $q->where('estado', 'completada'),
            'actividades as en_proceso_count'  => fn($q) => $q->where('estado', 'en_proceso'),
            'actividades as vencidas_count'    => fn($q) => $q->where('estado', '!=', 'completada')
                                                               ->where('estado', '!=', 'cancelada')
                                                               ->whereDate('fecha_limite', '<', now()),
        ])->where('activo', true)->orderBy('numero')->get()
          ->map(function ($c) use ($umbral_verde, $umbral_amarillo) {
              $c->porcentaje = $c->actividades_count > 0
                  ? round(($c->completadas_count / $c->actividades_count) * 100) : 0;
              $c->color    = $c->porcentaje >= $umbral_verde    ? 'success'   : ($c->porcentaje >= $umbral_amarillo ? 'warning' : 'danger');
              $c->semaforo = $c->porcentaje >= $umbral_verde    ? 'Cumplido'  : ($c->porcentaje >= $umbral_amarillo ? 'En proceso' : 'En riesgo');
              $c->nivel    = $c->porcentaje >= $umbral_verde    ? 'Bueno'     : ($c->porcentaje >= $umbral_amarillo ? 'Regular' : 'En riesgo');
              // evidencias count per component (via actividades)
              $c->evidencias_count = \App\Models\Evidencia::whereHas('actividad', fn($q) => $q->where('componente_id', $c->id))->count();
              return $c;
          });

        $avance_global = $componentes->avg('porcentaje') ?? 0;

        $en_avance  = $componentes->where('porcentaje', '>=', $umbral_amarillo)->count();
        $en_riesgo  = $componentes->where('porcentaje', '<',  $umbral_amarillo)->where('porcentaje', '>', 0)->count();
        $criticos   = $componentes->where('porcentaje', 0)->count();

        // Alertas activas (no leídas) — lateral panel
        $alertas_activas = Alerta::with(['actividad.componente', 'unidadOrganica'])
            ->where('leida', false)
            ->orderByRaw("FIELD(prioridad,'alta','media','baja')")
            ->limit(5)
            ->get();

        // Próximas acciones (actividades próximas a vencer)
        $proximas_acciones = Actividad::with('componente')
            ->whereNotIn('estado', ['completada', 'cancelada'])
            ->whereDate('fecha_limite', '>=', now())
            ->orderBy('fecha_limite')
            ->limit(5)
            ->get();

        // Evidencias recientes
        $evidencias_recientes = Evidencia::with(['actividad.componente', 'subidoPor'])
            ->latest()
            ->limit(8)
            ->get();

        return view('content.modelo-integridad.index', compact(
            'componentes', 'avance_global',
            'umbral_verde', 'umbral_amarillo',
            'en_avance', 'en_riesgo', 'criticos',
            'alertas_activas', 'proximas_acciones', 'evidencias_recientes'
        ));
    }
}
