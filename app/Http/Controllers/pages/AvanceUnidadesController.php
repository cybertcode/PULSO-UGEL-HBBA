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

        // ── Unidades con conteos generales + por módulo ──────────────────────
        $unidades = UnidadOrganica::where('activo', true)
            ->withCount([
                // General
                'actividades',
                'actividades as completadas_count'  => fn($q) => $q->where('estado', 'completada'),
                'actividades as en_proceso_count'   => fn($q) => $q->where('estado', 'en_proceso'),
                'actividades as pendientes_count'   => fn($q) => $q->where('estado', 'pendiente'),
                'actividades as vencidas_count'     => fn($q) => $q->whereNotIn('estado', ['completada', 'observado'])
                                                                    ->whereDate('fecha_limite', '<', now()),
                // SCI
                'actividades as sci_total'          => fn($q) => $q->where('modulo', 'sci'),
                'actividades as sci_completadas'    => fn($q) => $q->where('modulo', 'sci')->where('estado', 'completada'),
                'actividades as sci_en_proceso'     => fn($q) => $q->where('modulo', 'sci')->where('estado', 'en_proceso'),
                'actividades as sci_pendientes'     => fn($q) => $q->where('modulo', 'sci')->where('estado', 'pendiente'),
                // Integridad
                'actividades as int_total'          => fn($q) => $q->where('modulo', 'integridad'),
                'actividades as int_completadas'    => fn($q) => $q->where('modulo', 'integridad')->where('estado', 'completada'),
                'actividades as int_en_proceso'     => fn($q) => $q->where('modulo', 'integridad')->where('estado', 'en_proceso'),
                'actividades as int_pendientes'     => fn($q) => $q->where('modulo', 'integridad')->where('estado', 'pendiente'),
            ])
            ->get()
            ->map(function ($u) use ($config) {
                // Semáforo general
                SemaforoHelper::decorar($u, 'actividades_count', 'completadas_count', $config, 'En avance', 'En proceso', 'En riesgo');

                // Porcentajes por módulo
                $u->sci_porcentaje = $u->sci_total > 0
                    ? (int) round(($u->sci_completadas / $u->sci_total) * 100) : 0;
                $u->int_porcentaje = $u->int_total > 0
                    ? (int) round(($u->int_completadas / $u->int_total) * 100) : 0;

                $u->sci_color = SemaforoHelper::color($u->sci_porcentaje);
                $u->int_color = SemaforoHelper::color($u->int_porcentaje);

                return $u;
            })
            ->sortByDesc('porcentaje')
            ->values();

        // ── Totales generales ────────────────────────────────────────────────
        $total_actividades  = $unidades->sum('actividades_count');
        $total_completadas  = $unidades->sum('completadas_count');
        $total_en_proceso   = $unidades->sum('en_proceso_count');
        $total_pendientes   = $unidades->sum('pendientes_count');
        $avance_global      = $total_actividades > 0
            ? round(($total_completadas / $total_actividades) * 100) : 0;

        // ── Totales SCI ──────────────────────────────────────────────────────
        $sci_total          = $unidades->sum('sci_total');
        $sci_completadas    = $unidades->sum('sci_completadas');
        $sci_en_proceso     = $unidades->sum('sci_en_proceso');
        $sci_pendientes     = $unidades->sum('sci_pendientes');
        $sci_avance         = $sci_total > 0 ? round(($sci_completadas / $sci_total) * 100) : 0;

        // ── Totales Integridad ───────────────────────────────────────────────
        $int_total          = $unidades->sum('int_total');
        $int_completadas    = $unidades->sum('int_completadas');
        $int_en_proceso     = $unidades->sum('int_en_proceso');
        $int_pendientes     = $unidades->sum('int_pendientes');
        $int_avance         = $int_total > 0 ? round(($int_completadas / $int_total) * 100) : 0;

        // ── Actividades prioritarias SCI pendientes (remediación) ────────────
        $medidas_remediacion = Actividad::with(['unidadOrganica', 'sciPregunta'])
            ->where('modulo', 'sci')
            ->where('prioridad', 'alta')
            ->whereNotIn('estado', ['completada', 'observado'])
            ->orderBy('fecha_limite')
            ->limit(6)->get();

        // ── Actividades Integridad completadas recientemente ─────────────────
        $medidas_control = Actividad::with(['unidadOrganica', 'integridadPregunta'])
            ->where('modulo', 'integridad')
            ->where('estado', 'completada')
            ->orderByDesc('updated_at')
            ->limit(6)->get();

        $ultima_actualizacion = Actividad::max('updated_at');

        return view('content.avance-unidades.index', compact(
            'unidades',
            'total_actividades', 'total_completadas', 'total_en_proceso', 'total_pendientes', 'avance_global',
            'sci_total', 'sci_completadas', 'sci_en_proceso', 'sci_pendientes', 'sci_avance',
            'int_total', 'int_completadas', 'int_en_proceso', 'int_pendientes', 'int_avance',
            'medidas_remediacion', 'medidas_control',
            'ultima_actualizacion'
        ));
    }
}
