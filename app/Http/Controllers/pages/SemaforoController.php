<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\ConfiguracionInstitucional;
use App\Models\SciEje;
use App\Models\SciComponente;
use App\Models\IntegridadEtapa;
use App\Models\IntegridadComponente;
use App\Models\UnidadOrganica;
use App\Support\SemaforoHelper;
use Illuminate\Http\Request;

class SemaforoController extends Controller
{
    public function index(Request $request)
    {
        $config = ConfiguracionInstitucional::cached();
        [$umbral_verde, $umbral_amarillo] = SemaforoHelper::umbrales($config);

        $anio = $request->input('anio', now()->year);

        // ── SCI ───────────────────────────────────────────────────────────────
        $sciEjes = SciEje::where('activo', true)
            ->when($anio, fn($q) => $q->where('anio', $anio))
            ->with(['componentes' => fn($q) => $q->where('activo', true)->with('preguntas')])
            ->orderBy('orden')
            ->get()
            ->map(function ($eje) use ($config) {
                $eje->componentes->each(function ($comp) use ($config) {
                    $pregIds = $comp->preguntas->pluck('id');
                    $total      = Actividad::where('modulo', 'sci')->whereIn('sci_pregunta_id', $pregIds)->count();
                    $completadas = Actividad::where('modulo', 'sci')->whereIn('sci_pregunta_id', $pregIds)->where('estado', 'completada')->count();
                    $comp->actividades_count  = $total;
                    $comp->completadas_count  = $completadas;
                    SemaforoHelper::decorar($comp, 'actividades_count', 'completadas_count', $config, 'Verde', 'Amarillo', 'Rojo');
                });
                $pct = (int) round($eje->componentes->avg('porcentaje') ?? 0);
                [$vrd, $aml] = SemaforoHelper::umbrales($config);
                $eje->porcentaje = $pct;
                $eje->color    = SemaforoHelper::color($pct, $vrd, $aml);
                $eje->semaforo = SemaforoHelper::label($pct, $vrd, $aml, 'Verde', 'Amarillo', 'Rojo');
                return $eje;
            });

        $sciTotales = [
            'total'       => Actividad::where('modulo', 'sci')->when($anio, fn($q) => $q->where('anio', $anio))->count(),
            'completadas' => Actividad::where('modulo', 'sci')->when($anio, fn($q) => $q->where('anio', $anio))->where('estado', 'completada')->count(),
        ];
        $sciAvance = $sciTotales['total'] > 0
            ? round($sciTotales['completadas'] / $sciTotales['total'] * 100)
            : 0;

        // ── INTEGRIDAD ────────────────────────────────────────────────────────
        $integridadEtapas = IntegridadEtapa::where('activo', true)
            ->when($anio, fn($q) => $q->where('anio', $anio))
            ->with(['componentes' => fn($q) => $q->where('activo', true)->with('preguntas')])
            ->orderBy('orden')
            ->get()
            ->map(function ($etapa) use ($config) {
                $etapa->componentes->each(function ($comp) use ($config) {
                    $pregIds = $comp->preguntas->pluck('id');
                    $total      = Actividad::where('modulo', 'integridad')->whereIn('integridad_pregunta_id', $pregIds)->count();
                    $completadas = Actividad::where('modulo', 'integridad')->whereIn('integridad_pregunta_id', $pregIds)->where('estado', 'completada')->count();
                    $comp->actividades_count = $total;
                    $comp->completadas_count = $completadas;
                    SemaforoHelper::decorar($comp, 'actividades_count', 'completadas_count', $config, 'Verde', 'Amarillo', 'Rojo');
                });
                $pct2 = (int) round($etapa->componentes->avg('porcentaje') ?? 0);
                [$vrd2, $aml2] = SemaforoHelper::umbrales($config);
                $etapa->porcentaje = $pct2;
                $etapa->color    = SemaforoHelper::color($pct2, $vrd2, $aml2);
                $etapa->semaforo = SemaforoHelper::label($pct2, $vrd2, $aml2, 'Verde', 'Amarillo', 'Rojo');
                return $etapa;
            });

        $intTotales = [
            'total'       => Actividad::where('modulo', 'integridad')->when($anio, fn($q) => $q->where('anio', $anio))->count(),
            'completadas' => Actividad::where('modulo', 'integridad')->when($anio, fn($q) => $q->where('anio', $anio))->where('estado', 'completada')->count(),
        ];
        $intAvance = $intTotales['total'] > 0
            ? round($intTotales['completadas'] / $intTotales['total'] * 100)
            : 0;

        // ── Unidades ──────────────────────────────────────────────────────────
        $unidades = UnidadOrganica::where('activo', true)->orderBy('nombre')->get()
            ->map(function ($u) use ($config, $anio) {
                $base = Actividad::where('unidad_organica_id', $u->id)->when($anio, fn($q) => $q->where('anio', $anio));
                $u->actividades_count = (clone $base)->count();
                $u->completadas_count = (clone $base)->where('estado', 'completada')->count();
                SemaforoHelper::decorar($u, 'actividades_count', 'completadas_count', $config, 'Verde', 'Amarillo', 'Rojo');
                return $u;
            })->sortByDesc('porcentaje')->values();

        $anios = collect(
            array_unique(array_merge(
                SciEje::pluck('anio')->toArray(),
                IntegridadEtapa::pluck('anio')->toArray(),
                Actividad::whereNotNull('anio')->pluck('anio')->toArray()
            ))
        )->sort()->reverse()->values();

        return view('content.semaforo.index', compact(
            'sciEjes', 'sciAvance', 'sciTotales',
            'integridadEtapas', 'intAvance', 'intTotales',
            'unidades', 'umbral_verde', 'umbral_amarillo',
            'anio', 'anios'
        ));
    }
}
