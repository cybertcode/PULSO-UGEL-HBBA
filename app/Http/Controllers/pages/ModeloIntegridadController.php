<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Alerta;
use App\Models\Componente;
use App\Models\ConfiguracionInstitucional;
use App\Models\Evidencia;
use App\Models\IntegridadCompromiso;
use App\Models\UnidadOrganica;
use App\Models\User;
use App\Support\SemaforoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

        // Compromisos del Modelo de Integridad agrupados por pilar
        $compromisos_por_pilar = IntegridadCompromiso::with('responsable')
            ->orderBy('pilar')
            ->orderByDesc('avance')
            ->get()
            ->groupBy('pilar');

        $pilares = ['compromiso', 'cultura', 'regulacion', 'control'];

        $avance_integridad = IntegridadCompromiso::count() > 0
            ? (int) round(IntegridadCompromiso::avg('avance'))
            : 0;

        $usuarios  = User::orderBy('name')->get(['id', 'name']);
        $anio      = now()->year;

        return view('content.modelo-integridad.index', compact(
            'componentes', 'avance_global',
            'umbral_verde', 'umbral_amarillo',
            'en_avance', 'en_riesgo', 'criticos',
            'alertas_activas', 'proximas_acciones', 'evidencias_recientes',
            'compromisos_por_pilar', 'pilares', 'avance_integridad', 'usuarios', 'anio'
        ));
    }

    public function storeCompromiso(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pilar'          => 'required|in:compromiso,cultura,regulacion,control',
            'titulo'         => 'required|string|max:255',
            'descripcion'    => 'nullable|string',
            'avance'         => 'required|integer|min:0|max:100',
            'estado'         => 'required|in:pendiente,en_proceso,completado',
            'fecha_inicio'   => 'nullable|date',
            'fecha_fin'      => 'nullable|date|after_or_equal:fecha_inicio',
            'responsable_id' => 'nullable|exists:users,id',
            'evidencia'      => 'nullable|string',
            'observaciones'  => 'nullable|string',
            'anio'           => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Corrija los errores.');
        }

        IntegridadCompromiso::create($request->only([
            'pilar', 'titulo', 'descripcion', 'avance', 'estado',
            'fecha_inicio', 'fecha_fin', 'responsable_id', 'evidencia', 'observaciones', 'anio',
        ]));

        return back()->with('success', 'Compromiso registrado correctamente.');
    }

    public function updateCompromiso(Request $request, IntegridadCompromiso $compromiso)
    {
        $validator = Validator::make($request->all(), [
            'pilar'          => 'required|in:compromiso,cultura,regulacion,control',
            'titulo'         => 'required|string|max:255',
            'descripcion'    => 'nullable|string',
            'avance'         => 'required|integer|min:0|max:100',
            'estado'         => 'required|in:pendiente,en_proceso,completado',
            'fecha_inicio'   => 'nullable|date',
            'fecha_fin'      => 'nullable|date',
            'responsable_id' => 'nullable|exists:users,id',
            'evidencia'      => 'nullable|string',
            'observaciones'  => 'nullable|string',
            'anio'           => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Corrija los errores.');
        }

        $compromiso->update($request->only([
            'pilar', 'titulo', 'descripcion', 'avance', 'estado',
            'fecha_inicio', 'fecha_fin', 'responsable_id', 'evidencia', 'observaciones', 'anio',
        ]));

        return back()->with('success', 'Compromiso actualizado.');
    }

    public function destroyCompromiso(IntegridadCompromiso $compromiso)
    {
        $compromiso->delete();
        return back()->with('success', 'Compromiso eliminado.');
    }
}
