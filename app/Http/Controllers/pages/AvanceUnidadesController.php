<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\ConfiguracionInstitucional;
use App\Models\UnidadOrganica;
use App\Support\SemaforoHelper;

class AvanceUnidadesController extends Controller
{
    public function index()
    {
        $config = ConfiguracionInstitucional::cached();
        [$umbral_verde, $umbral_amarillo] = SemaforoHelper::umbrales($config);

        $unidades = UnidadOrganica::where('activo', true)
            ->withCount([
                'actividades',
                'actividades as completadas_count' => fn($q) => $q->where('estado', 'completada'),
                'actividades as en_proceso_count'  => fn($q) => $q->where('estado', 'en_proceso'),
                'actividades as pendientes_count'  => fn($q) => $q->where('estado', 'pendiente'),
                'actividades as vencidas_count'    => fn($q) => $q->whereNotIn('estado', ['completada', 'observado'])
                    ->whereDate('fecha_limite', '<', now()),
            ])
            ->get()
            ->map(function ($u) use ($config) {
                SemaforoHelper::decorar($u, 'actividades_count', 'completadas_count', $config, 'En avance', 'En proceso', 'En riesgo');
                return $u;
            })
            ->sortByDesc('porcentaje')
            ->values();

        $total_actividades = $unidades->sum('actividades_count');
        $total_completadas = $unidades->sum('completadas_count');
        $total_en_proceso  = $unidades->sum('en_proceso_count');
        $total_pendientes  = $unidades->sum('pendientes_count');
        $avance_global     = $total_actividades > 0
            ? round(($total_completadas / $total_actividades) * 100) : 0;

        $medidas_remediacion = Actividad::with(['unidadOrganica', 'componente'])
            ->where('prioridad', 'alta')
            ->whereNotIn('estado', ['completada', 'observado'])
            ->orderBy('fecha_limite')
            ->limit(5)->get();

        $medidas_control = Actividad::with(['unidadOrganica', 'componente'])
            ->where('estado', 'completada')
            ->orderByDesc('updated_at')
            ->limit(5)->get();

        $ultima_actualizacion = Actividad::max('updated_at');

        return view('content.avance-unidades.index', compact(
            'unidades', 'total_actividades', 'total_completadas', 'total_en_proceso',
            'total_pendientes', 'avance_global', 'medidas_remediacion', 'medidas_control',
            'ultima_actualizacion', 'umbral_verde', 'umbral_amarillo'
        ));
    }
}
