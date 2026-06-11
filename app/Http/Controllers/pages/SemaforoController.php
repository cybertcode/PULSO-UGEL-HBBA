<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\ConfiguracionInstitucional;
use App\Models\SciEje;
use App\Models\IntegridadEtapa;
use App\Models\UnidadOrganica;
use App\Support\SemaforoHelper;
use Illuminate\Http\Request;

class SemaforoController extends Controller
{
    public function demo()
    {
        $umbral_verde    = 75;
        $umbral_amarillo = 50;
        $anio            = now()->year;
        $anios           = collect([$anio]);

        // ── Datos simulados SCI ───────────────────────────────────────────────
        $sciData = [
            ['nombre' => 'Ambiente de Control', 'componentes' => [
                ['nombre' => 'Filosofía de la dirección',         'total' => 8,  'comp' => 7],
                ['nombre' => 'Integridad y valores éticos',       'total' => 6,  'comp' => 6],
                ['nombre' => 'Estructura organizacional',         'total' => 5,  'comp' => 3],
                ['nombre' => 'Administración estratégica',        'total' => 10, 'comp' => 9],
            ]],
            ['nombre' => 'Evaluación de Riesgos', 'componentes' => [
                ['nombre' => 'Planeamiento de la administración', 'total' => 12, 'comp' => 6],
                ['nombre' => 'Identificación de riesgos',        'total' => 8,  'comp' => 4],
                ['nombre' => 'Valoración de riesgos',            'total' => 7,  'comp' => 2],
                ['nombre' => 'Respuesta al riesgo',              'total' => 5,  'comp' => 5],
            ]],
            ['nombre' => 'Actividades de Control', 'componentes' => [
                ['nombre' => 'Procedimientos de autorización',    'total' => 9,  'comp' => 8],
                ['nombre' => 'Segregación de funciones',         'total' => 6,  'comp' => 5],
                ['nombre' => 'Evaluación costo-beneficio',       'total' => 4,  'comp' => 4],
                ['nombre' => 'Controles sobre TI',               'total' => 8,  'comp' => 3],
            ]],
            ['nombre' => 'Información y Comunicación', 'componentes' => [
                ['nombre' => 'Funciones y características',      'total' => 7,  'comp' => 7],
                ['nombre' => 'Información y responsabilidad',    'total' => 5,  'comp' => 4],
                ['nombre' => 'Sistemas de información',          'total' => 6,  'comp' => 6],
                ['nombre' => 'Canales de comunicación',          'total' => 4,  'comp' => 2],
            ]],
            ['nombre' => 'Supervisión', 'componentes' => [
                ['nombre' => 'Actividades de prevención',        'total' => 6,  'comp' => 5],
                ['nombre' => 'Seguimiento de resultados',        'total' => 8,  'comp' => 8],
                ['nombre' => 'Compromisos de mejoramiento',      'total' => 5,  'comp' => 3],
            ]],
        ];

        $sciEjes = collect($sciData)->values()->map(function ($ejeData, $i) use ($umbral_verde, $umbral_amarillo, $anio) {
            $componentes = collect($ejeData['componentes'])->map(function ($c) use ($umbral_verde, $umbral_amarillo) {
                $pct = $c['total'] > 0 ? (int) round($c['comp'] / $c['total'] * 100) : 0;
                return (object)[
                    'id'                => rand(100, 999),
                    'nombre'            => $c['nombre'],
                    'icono'             => 'tabler-circle-check',
                    'actividades_count' => $c['total'],
                    'completadas_count' => $c['comp'],
                    'porcentaje'        => $pct,
                    'color'             => \App\Support\SemaforoHelper::color($pct, $umbral_verde, $umbral_amarillo),
                    'semaforo'          => \App\Support\SemaforoHelper::label($pct, $umbral_verde, $umbral_amarillo, 'Verde', 'Amarillo', 'Rojo'),
                ];
            });
            $pctEje = (int) round($componentes->avg('porcentaje'));
            return (object)[
                'id'          => $i + 1,
                'nombre'      => $ejeData['nombre'],
                'anio'        => $anio,
                'componentes' => $componentes,
                'porcentaje'  => $pctEje,
                'color'       => \App\Support\SemaforoHelper::color($pctEje, $umbral_verde, $umbral_amarillo),
                'semaforo'    => \App\Support\SemaforoHelper::label($pctEje, $umbral_verde, $umbral_amarillo, 'Verde', 'Amarillo', 'Rojo'),
            ];
        });

        $sciTotales = ['total' => 137, 'completadas' => 100];
        $sciAvance  = 73;

        // ── Datos simulados Integridad ────────────────────────────────────────
        $intData = [
            ['nombre' => 'Etapa I: Diagnóstico', 'componentes' => [
                ['nombre' => 'Análisis de riesgos de corrupción', 'total' => 10, 'comp' => 10],
                ['nombre' => 'Mapeo de actores clave',            'total' => 8,  'comp' => 7],
                ['nombre' => 'Evaluación de integridad',          'total' => 6,  'comp' => 6],
            ]],
            ['nombre' => 'Etapa II: Planificación', 'componentes' => [
                ['nombre' => 'Plan de Integridad institucional',  'total' => 12, 'comp' => 9],
                ['nombre' => 'Código de Ética aprobado',         'total' => 5,  'comp' => 5],
                ['nombre' => 'Canal de denuncias implementado',   'total' => 7,  'comp' => 4],
            ]],
            ['nombre' => 'Etapa III: Implementación', 'componentes' => [
                ['nombre' => 'Capacitación en integridad',        'total' => 15, 'comp' => 7],
                ['nombre' => 'Declaraciones juradas',             'total' => 20, 'comp' => 8],
                ['nombre' => 'Conflictos de interés',             'total' => 9,  'comp' => 3],
            ]],
            ['nombre' => 'Etapa IV: Seguimiento', 'componentes' => [
                ['nombre' => 'Monitoreo del plan',                'total' => 8,  'comp' => 8],
                ['nombre' => 'Reporte a la Alta Dirección',       'total' => 6,  'comp' => 5],
                ['nombre' => 'Mejora continua',                   'total' => 5,  'comp' => 2],
            ]],
        ];

        $integridadEtapas = collect($intData)->values()->map(function ($etapaData, $i) use ($umbral_verde, $umbral_amarillo, $anio) {
            $componentes = collect($etapaData['componentes'])->map(function ($c) use ($umbral_verde, $umbral_amarillo) {
                $pct = $c['total'] > 0 ? (int) round($c['comp'] / $c['total'] * 100) : 0;
                return (object)[
                    'id'                => rand(100, 999),
                    'nombre'            => $c['nombre'],
                    'icono'             => 'tabler-shield',
                    'actividades_count' => $c['total'],
                    'completadas_count' => $c['comp'],
                    'porcentaje'        => $pct,
                    'color'             => \App\Support\SemaforoHelper::color($pct, $umbral_verde, $umbral_amarillo),
                    'semaforo'          => \App\Support\SemaforoHelper::label($pct, $umbral_verde, $umbral_amarillo, 'Verde', 'Amarillo', 'Rojo'),
                ];
            });
            $pctEtapa = (int) round($componentes->avg('porcentaje'));
            return (object)[
                'id'          => $i + 1,
                'nombre'      => $etapaData['nombre'],
                'anio'        => $anio,
                'componentes' => $componentes,
                'porcentaje'  => $pctEtapa,
                'color'       => \App\Support\SemaforoHelper::color($pctEtapa, $umbral_verde, $umbral_amarillo),
                'semaforo'    => \App\Support\SemaforoHelper::label($pctEtapa, $umbral_verde, $umbral_amarillo, 'Verde', 'Amarillo', 'Rojo'),
            ];
        });

        $intTotales = ['total' => 111, 'completadas' => 74];
        $intAvance  = 67;

        // ── Unidades orgánicas simuladas ──────────────────────────────────────
        $unidadesData = [
            ['nombre' => 'Dirección Regional',                        'sigla' => 'DR',   'total' => 30, 'comp' => 28],
            ['nombre' => 'Gestión Pedagógica',                        'sigla' => 'GP',   'total' => 25, 'comp' => 22],
            ['nombre' => 'Gestión Institucional',                     'sigla' => 'GI',   'total' => 20, 'comp' => 18],
            ['nombre' => 'Asesoría Jurídica',                         'sigla' => 'AJ',   'total' => 15, 'comp' => 13],
            ['nombre' => 'Administración',                            'sigla' => 'ADM',  'total' => 35, 'comp' => 25],
            ['nombre' => 'Recursos Humanos',                          'sigla' => 'RRHH', 'total' => 28, 'comp' => 16],
            ['nombre' => 'Infraestructura y Equipamiento',            'sigla' => 'IE',   'total' => 18, 'comp' => 9],
            ['nombre' => 'Tecnologías de la Información',             'sigla' => 'TI',   'total' => 12, 'comp' => 11],
            ['nombre' => 'Planificación y Presupuesto',               'sigla' => 'PP',   'total' => 22, 'comp' => 10],
            ['nombre' => 'Control Institucional',                     'sigla' => 'OCI',  'total' => 10, 'comp' => 10],
        ];

        $unidades = collect($unidadesData)->map(function ($u) use ($umbral_verde, $umbral_amarillo) {
            $pct = $u['total'] > 0 ? (int) round($u['comp'] / $u['total'] * 100) : 0;
            return (object)[
                'nombre'            => $u['nombre'],
                'sigla'             => $u['sigla'],
                'actividades_count' => $u['total'],
                'completadas_count' => $u['comp'],
                'porcentaje'        => $pct,
                'color'             => \App\Support\SemaforoHelper::color($pct, $umbral_verde, $umbral_amarillo),
                'semaforo'          => \App\Support\SemaforoHelper::label($pct, $umbral_verde, $umbral_amarillo),
            ];
        })->sortByDesc('porcentaje')->values();

        return view('content.semaforo.index', compact(
            'sciEjes', 'sciAvance', 'sciTotales',
            'integridadEtapas', 'intAvance', 'intTotales',
            'unidades', 'umbral_verde', 'umbral_amarillo',
            'anio', 'anios'
        ));
    }

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
                    $base = Actividad::where('modulo', 'sci')->whereIn('sci_pregunta_id', $pregIds);
                    $comp->actividades_count  = (clone $base)->count();
                    $comp->completadas_count  = (clone $base)->where('estado', 'completada')->count();
                    $comp->en_proceso_count   = (clone $base)->where('estado', 'en_proceso')->count();
                    $comp->pendiente_count    = (clone $base)->where('estado', 'pendiente')->count();
                    $comp->vencidas_count     = (clone $base)->where('estado', 'vencida')->count();
                    $comp->observado_count    = (clone $base)->where('estado', 'observado')->count();
                    $comp->proxima_fecha      = (clone $base)->whereNotIn('estado', ['completada'])->whereNotNull('fecha_limite')->orderBy('fecha_limite')->value('fecha_limite');
                    $comp->alta_prioridad     = (clone $base)->where('prioridad', 'alta')->whereNotIn('estado', ['completada'])->count();
                    SemaforoHelper::decorar($comp, 'actividades_count', 'completadas_count', $config, 'Cumplido', 'En proceso', 'En riesgo');
                });
                $pct = (int) round($eje->componentes->avg('porcentaje') ?? 0);
                [$vrd, $aml] = SemaforoHelper::umbrales($config);
                $eje->porcentaje = $pct;
                $eje->color    = SemaforoHelper::color($pct, $vrd, $aml);
                $eje->semaforo = SemaforoHelper::label($pct, $vrd, $aml, 'Cumplido', 'En proceso', 'En riesgo');
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
                    $base = Actividad::where('modulo', 'integridad')->whereIn('integridad_pregunta_id', $pregIds);
                    $comp->actividades_count  = (clone $base)->count();
                    $comp->completadas_count  = (clone $base)->where('estado', 'completada')->count();
                    $comp->en_proceso_count   = (clone $base)->where('estado', 'en_proceso')->count();
                    $comp->pendiente_count    = (clone $base)->where('estado', 'pendiente')->count();
                    $comp->vencidas_count     = (clone $base)->where('estado', 'vencida')->count();
                    $comp->observado_count    = (clone $base)->where('estado', 'observado')->count();
                    $comp->proxima_fecha      = (clone $base)->whereNotIn('estado', ['completada'])->whereNotNull('fecha_limite')->orderBy('fecha_limite')->value('fecha_limite');
                    $comp->alta_prioridad     = (clone $base)->where('prioridad', 'alta')->whereNotIn('estado', ['completada'])->count();
                    SemaforoHelper::decorar($comp, 'actividades_count', 'completadas_count', $config, 'Cumplido', 'En proceso', 'En riesgo');
                });
                $pct2 = (int) round($etapa->componentes->avg('porcentaje') ?? 0);
                [$vrd2, $aml2] = SemaforoHelper::umbrales($config);
                $etapa->porcentaje = $pct2;
                $etapa->color    = SemaforoHelper::color($pct2, $vrd2, $aml2);
                $etapa->semaforo = SemaforoHelper::label($pct2, $vrd2, $aml2, 'Cumplido', 'En proceso', 'En riesgo');
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
                $u->en_proceso_count  = (clone $base)->where('estado', 'en_proceso')->count();
                $u->pendiente_count   = (clone $base)->where('estado', 'pendiente')->count();
                $u->vencidas_count    = (clone $base)->where('estado', 'vencida')->count();
                SemaforoHelper::decorar($u, 'actividades_count', 'completadas_count', $config, 'Cumplido', 'En proceso', 'En riesgo');
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
