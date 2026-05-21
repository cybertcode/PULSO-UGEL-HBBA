<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\UnidadOrganica;
use App\Models\Actividad;
use App\Models\ConfiguracionInstitucional;
use Illuminate\Support\Facades\DB;

class AvanceUnidadesController extends Controller
{
    public function index()
    {
        $config          = ConfiguracionInstitucional::first();
        $umbral_verde    = $config->umbral_verde    ?? 75;
        $umbral_amarillo = $config->umbral_amarillo ?? 50;

        $unidades = UnidadOrganica::where('activo', true)
            ->withCount([
                'actividades',
                'actividades as completadas_count' => fn($q) => $q->where('estado', 'completada'),
                'actividades as en_proceso_count'  => fn($q) => $q->where('estado', 'en_proceso'),
                'actividades as pendientes_count'  => fn($q) => $q->where('estado', 'pendiente'),
                'actividades as vencidas_count'    => fn($q) => $q->where('estado', '!=', 'completada')
                    ->where('estado', '!=', 'cancelada')
                    ->whereDate('fecha_limite', '<', now()),
            ])
            ->get()
            ->map(function ($u) use ($umbral_verde, $umbral_amarillo) {
                $u->porcentaje = $u->actividades_count > 0
                    ? round(($u->completadas_count / $u->actividades_count) * 100) : 0;
                $u->color    = $u->porcentaje >= $umbral_verde ? 'success'
                    : ($u->porcentaje >= $umbral_amarillo ? 'warning' : 'danger');
                $u->semaforo = $u->porcentaje >= $umbral_verde ? 'En avance'
                    : ($u->porcentaje >= $umbral_amarillo ? 'En proceso' : 'En riesgo');
                return $u;
            })
            ->sortByDesc('porcentaje')
            ->values();

        // Totales globales
        $total_actividades  = $unidades->sum('actividades_count');
        $total_completadas  = $unidades->sum('completadas_count');
        $total_en_proceso   = $unidades->sum('en_proceso_count');
        $total_pendientes   = $unidades->sum('pendientes_count');
        $avance_global      = $total_actividades > 0
            ? round(($total_completadas / $total_actividades) * 100) : 0;

        // Medidas de remediación recientes (actividades con prioridad alta pendientes)
        $medidas_remediacion = Actividad::with(['unidadOrganica', 'componente'])
            ->where('prioridad', 'alta')
            ->whereNotIn('estado', ['completada', 'cancelada'])
            ->orderBy('fecha_limite')
            ->limit(5)->get();

        // Medidas de control recientes (actividades completadas recientemente)
        $medidas_control = Actividad::with(['unidadOrganica', 'componente'])
            ->where('estado', 'completada')
            ->orderByDesc('updated_at')
            ->limit(5)->get();

        // Última actualización
        $ultima_actualizacion = Actividad::max('updated_at');

        return view('content.avance-unidades.index', compact(
            'unidades', 'total_actividades', 'total_completadas', 'total_en_proceso',
            'total_pendientes', 'avance_global', 'medidas_remediacion', 'medidas_control',
            'ultima_actualizacion', 'umbral_verde', 'umbral_amarillo'
        ));
    }
}
