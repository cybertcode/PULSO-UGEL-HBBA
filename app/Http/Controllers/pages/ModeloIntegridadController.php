<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\ActividadHistorial;
use App\Models\Evidencia;
use App\Models\Alerta;
use App\Models\IntegridadEtapa;
use App\Models\IntegridadComponente;
use App\Models\IntegridadPregunta;
use App\Models\UnidadOrganica;
use App\Models\User;
use App\Models\ConfiguracionInstitucional;
use App\Notifications\ActividadAsignada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ModeloIntegridadController extends Controller
{
    public function index(Request $request)
    {
        $anio = $request->input('anio', now()->year);
        $user = Auth::user();

        $config          = ConfiguracionInstitucional::cached();
        $umbral_verde    = (int) ($config->umbral_verde    ?? 70);
        $umbral_amarillo = (int) ($config->umbral_amarillo ?? 40);

        // ── Actividades ───────────────────────────────────────────────────────
        $actQuery = Actividad::with([
                'integridadPregunta.componente.etapa',
                'evidencias',
                'responsables',
                'unidadOrganica',
            ])
            ->where('modulo', 'integridad')
            ->where('anio', $anio)
            ->visiblesParaUsuario($user);

        // Filtros adicionales (para listado AJAX)
        if ($request->filled('etapa_id')) {
            $actQuery->whereHas('integridadPregunta.componente', fn($q) => $q->where('etapa_id', $request->etapa_id));
        }
        if ($request->filled('componente_id')) {
            $actQuery->whereHas('integridadPregunta', fn($q) => $q->where('componente_id', $request->componente_id));
        }
        if ($request->filled('pregunta_id')) {
            $actQuery->where('integridad_pregunta_id', $request->pregunta_id);
        }
        if ($request->filled('unidad_id')) {
            $actQuery->where('unidad_organica_id', $request->unidad_id);
        }
        if ($request->filled('estado')) {
            $actQuery->where('estado', $request->estado);
        }
        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $actQuery->where(fn($q) => $q->where('nombre', 'like', "%$b%")->orWhere('codigo', 'like', "%$b%"));
        }

        // ── Si es petición AJAX de listado ────────────────────────────────────
        if ($request->ajax() || $request->wantsJson()) {
            $all = $actQuery->orderByDesc('fecha_limite')->get();
            return response()->json([
                'actividades' => $all->map(fn($a) => $this->actividadToArray($a)),
                'total'       => $all->count(),
            ]);
        }

        // Para métricas necesitamos todos los registros sin paginar
        $todasActividades = (clone $actQuery)->orderByDesc('fecha_limite')->get();

        // ── Métricas globales ─────────────────────────────────────────────────
        $total         = $todasActividades->count();
        $completadas_n = $todasActividades->where('estado', 'completada')->count();
        $avance_global = $total > 0 ? round($todasActividades->avg('avance')) : 0;

        // ── Componentes con métricas ──────────────────────────────────────────
        $componentesBase = IntegridadComponente::with(['etapa', 'preguntas'])
            ->where('activo', true)
            ->whereHas('etapa', fn($q) => $q->where('anio', $anio))
            ->orderBy('orden')
            ->get();

        $componentes = $componentesBase->map(function ($comp) use ($todasActividades, $umbral_verde, $umbral_amarillo) {
            $acts        = $todasActividades->filter(fn($a) => $a->integridadPregunta?->componente_id === $comp->id);
            $total       = $acts->count();
            $porcentaje  = $total > 0 ? (int) round($acts->avg('avance')) : 0;
            $completadas = $acts->where('estado', 'completada')->count();
            $vencidas    = $acts->where('estado', 'vencida')->count();
            $en_proceso  = $acts->whereIn('estado', ['en_proceso', 'pendiente'])->count();
            $evidencias  = $acts->sum(fn($a) => $a->evidencias->count());
            $color       = $porcentaje >= $umbral_verde ? 'success'
                         : ($porcentaje >= $umbral_amarillo ? 'warning' : 'danger');

            return (object) [
                'id'               => $comp->id,
                'numero'           => $comp->orden,
                'nombre'           => $comp->nombre,
                'icono'            => $comp->icono ?? 'tabler-circle',
                'etapa'            => $comp->etapa?->nombre ?? '—',
                'porcentaje'       => $porcentaje,
                'color'            => $color,
                'total'            => $total,
                'completadas'      => $completadas,
                'completadas_count'=> $completadas,
                'en_proceso_count' => $en_proceso,
                'vencidas'         => $vencidas,
                'evidencias_count' => $evidencias,
                'con_ev'           => $acts->filter(fn($a) => $a->evidencias->count() > 0)->count(),
            ];
        });

        $en_avance         = $componentes->where('color', 'success')->count();
        $en_riesgo         = $componentes->where('color', 'warning')->count();
        $criticos          = $componentes->where('color', 'danger')->count();
        $observadas_count  = $todasActividades->where('estado', 'observado')->count();
        $ev_rechazadas_count = $todasActividades->filter(fn($a) => $a->evidencias->where('estado', 'rechazado')->count() > 0)->count();

        // ── Evidencias recientes ──────────────────────────────────────────────
        $idsIntegridad = $todasActividades->pluck('id');
        $evidencias_recientes = Evidencia::with(['actividad.integridadPregunta.componente', 'subidoPor'])
            ->whereIn('actividad_id', $idsIntegridad)
            ->latest()->limit(50)->get()
            ->map(function ($ev) {
                if ($ev->actividad?->integridadPregunta) {
                    $comp = $ev->actividad->integridadPregunta->componente;
                    $ev->actividad->componente = $comp ? (object)['numero'=>$comp->orden,'nombre'=>$comp->nombre] : null;
                } else {
                    if ($ev->actividad) $ev->actividad->componente = null;
                }
                return $ev;
            });

        // ── Alertas activas ───────────────────────────────────────────────────
        $alertas_activas = Alerta::with(['actividad.integridadPregunta.componente'])
            ->whereIn('actividad_id', $idsIntegridad)
            ->orderByRaw("FIELD(prioridad,'alta','media','baja')")
            ->limit(5)->get()
            ->map(function ($al) {
                if ($al->actividad?->integridadPregunta) {
                    $comp = $al->actividad->integridadPregunta->componente;
                    $al->actividad->componente = $comp ? (object)['nombre'=>$comp->nombre] : null;
                } else {
                    if ($al->actividad) $al->actividad->componente = null;
                }
                return $al;
            });

        // ── Próximas acciones ─────────────────────────────────────────────────
        $proximas_acciones = $todasActividades
            ->whereIn('estado', ['pendiente', 'en_proceso', 'observado'])
            ->filter(fn($a) => $a->fecha_limite !== null)
            ->sortBy('fecha_limite')->take(8)
            ->map(function ($act) {
                $comp = $act->integridadPregunta?->componente;
                $act->componente = $comp ? (object)['nombre' => $comp->nombre] : null;
                return $act;
            });

        // ── Para formulario nueva actividad ───────────────────────────────────
        $etapas    = IntegridadEtapa::where('activo', true)->orderBy('anio','desc')->orderBy('orden')->get();
        $anios_opt = Actividad::where('modulo','integridad')->selectRaw('DISTINCT anio')->whereNotNull('anio')->orderByDesc('anio')->pluck('anio');

        // Unidades y usuarios filtrados por visibilidad del usuario
        if ($user->can('actividades.ver-todas')) {
            $unidades = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
            $usuarios = User::where('estado', 'activo')->orderBy('name')->get();
        } elseif ($user->can('actividades.ver-unidad')) {
            $unidades = UnidadOrganica::where('activo', true)->where('id', $user->unidad_organica_id)->get();
            $usuarios = User::where('estado', 'activo')->where('unidad_organica_id', $user->unidad_organica_id)->orderBy('name')->get();
        } else {
            $unidades = collect();
            $usuarios = User::where('id', $user->id)->get();
        }

        // Paginado para la tabla (re-ejecuta la query con paginación)
        $actividades = $actQuery->orderByDesc('fecha_limite')->paginate(15)->withQueryString();

        return view('content.modelo-integridad.index', compact(
            'avance_global', 'umbral_verde', 'umbral_amarillo',
            'componentes', 'en_avance', 'en_riesgo', 'criticos',
            'observadas_count', 'ev_rechazadas_count',
            'evidencias_recientes', 'alertas_activas', 'proximas_acciones',
            'actividades', 'etapas', 'unidades', 'usuarios', 'anio', 'anios_opt'
        ));
    }

    // ── AJAX: componentes por etapa ───────────────────────────────────────────
    public function componentesPorEtapa(Request $request)
    {
        $componentes = IntegridadComponente::where('etapa_id', $request->etapa_id)
            ->where('activo', true)
            ->orderBy('orden')
            ->get(['id','nombre','orden','icono']);

        return response()->json($componentes);
    }

    // ── AJAX: preguntas por componente ────────────────────────────────────────
    public function preguntasPorComponente(Request $request)
    {
        $preguntas = IntegridadPregunta::where('componente_id', $request->componente_id)
            ->where('activo', true)
            ->orderBy('orden')
            ->get(['id','nombre','link_ficha','orden']);

        return response()->json($preguntas);
    }

    // ── Crear actividad ───────────────────────────────────────────────────────
    public function store(Request $request)
    {
        Gate::authorize('integridad.crear');

        $validated = $request->validate([
            'nombre'                 => 'required|string|max:255',
            'anio'                   => 'required|integer|min:2020|max:2099',
            'integridad_pregunta_id' => 'required|exists:integridad_preguntas,id',
            'unidad_organica_id'     => 'nullable|exists:unidades_organicas,id',
            'fecha_limite'           => 'required|date',
            'fecha_inicio'           => 'nullable|date|before_or_equal:fecha_limite',
            'prioridad'              => 'required|in:alta,media,baja',
            'numero_sgd'             => 'nullable|string|max:50',
            'descripcion'            => 'nullable|string',
            'observaciones'          => 'nullable|string',
            'responsables'           => 'nullable|array',
            'responsables.*'         => 'exists:users,id',
            'tipos'                  => 'nullable|array',
            'tipos.*'                => 'in:principal,colaborador,supervisor',
        ]);

        $validated['modulo']     = 'integridad';
        $validated['creado_por'] = Auth::id();
        $validated['estado']     = 'pendiente';
        $validated['avance']     = 0;

        $anio  = $validated['anio'];
        $count = Actividad::where('modulo','integridad')->whereYear('created_at', $anio)->withTrashed()->count() + 1;
        $validated['codigo'] = 'INTEGRIDAD-' . $anio . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($validated, $request) {
            $actividad = Actividad::create(\Arr::except($validated, ['responsables','tipos']));

            ActividadHistorial::create([
                'actividad_id'   => $actividad->id,
                'usuario_id'     => Auth::id(),
                'campo'          => 'estado',
                'valor_anterior' => null,
                'valor_nuevo'    => 'pendiente',
                'descripcion'    => 'Actividad creada',
            ]);

            if (!empty($validated['responsables'])) {
                $tipos = $validated['tipos'] ?? [];
                $sync  = collect($validated['responsables'])
                    ->mapWithKeys(fn($id) => [$id => ['tipo' => $tipos[$id] ?? 'principal']])
                    ->toArray();
                $actividad->responsables()->sync($sync);

                $creadorId = Auth::id();
                $actividad->load('responsables');
                foreach ($actividad->responsables as $resp) {
                    if ($resp->id === $creadorId) continue;
                    $resp->notify(new ActividadAsignada($actividad, 'nueva', $resp->pivot->tipo));
                }
            }
        });

        return back()->with('success', "Actividad «{$validated['nombre']}» creada correctamente.");
    }

    // ── Actualizar actividad ──────────────────────────────────────────────────
    public function update(Request $request, Actividad $actividad)
    {
        Gate::authorize('integridad.editar');
        abort_unless($actividad->puedeEditarUsuario(), 403, 'No tienes permiso para editar esta actividad.');

        $validated = $request->validate([
            'nombre'                 => 'required|string|max:255',
            'integridad_pregunta_id' => 'required|exists:integridad_preguntas,id',
            'unidad_organica_id'     => 'nullable|exists:unidades_organicas,id',
            'fecha_limite'           => 'required|date',
            'fecha_inicio'           => 'nullable|date|before_or_equal:fecha_limite',
            'avance'                 => 'nullable|integer|min:0|max:100',
            'estado'                 => 'required|in:pendiente,en_proceso,completada,observado,vencida',
            'prioridad'              => 'required|in:alta,media,baja',
            'numero_sgd'             => 'nullable|string|max:50',
            'descripcion'            => 'nullable|string',
            'observaciones'          => 'nullable|string',
            'responsables'           => 'nullable|array',
            'responsables.*'         => 'exists:users,id',
            'tipos'                  => 'nullable|array',
            'tipos.*'                => 'in:principal,colaborador,supervisor',
        ]);

        if ($validated['estado'] === 'completada') {
            if (!$actividad->evidencias()->where('estado', 'validado')->exists()) {
                return back()->withErrors(['estado' => 'No se puede marcar como completada sin al menos una evidencia validada.'])->withInput();
            }
            if (!$actividad->fecha_cumplimiento) {
                $validated['fecha_cumplimiento'] = now();
            }
            $validated['avance'] = 100;
        }

        $fechaAntes        = $actividad->fecha_limite?->toDateString();
        $responsablesAntes = $actividad->responsables->pluck('id')->toArray();
        $creadorId         = Auth::id();

        DB::transaction(function () use ($validated, $actividad) {
            $actividad->update(\Arr::except($validated, ['responsables','tipos']));

            if (!empty($validated['responsables'])) {
                $tipos = $validated['tipos'] ?? [];
                $sync  = collect($validated['responsables'])
                    ->mapWithKeys(fn($id) => [$id => ['tipo' => $tipos[$id] ?? 'principal']])
                    ->toArray();
                $actividad->responsables()->sync($sync);
            }
        });

        $actividad->load('responsables');
        $responsablesDespues = $actividad->responsables->pluck('id')->toArray();
        $nuevosIds = array_diff($responsablesDespues, $responsablesAntes);

        foreach ($actividad->responsables->whereIn('id', $nuevosIds) as $resp) {
            if ($resp->id === $creadorId) continue;
            $resp->notify(new ActividadAsignada($actividad, 'nueva', $resp->pivot->tipo));
        }

        $fechaDespues = $actividad->fecha_limite?->toDateString();
        if ($fechaAntes !== $fechaDespues && $fechaDespues) {
            foreach ($actividad->responsables->whereNotIn('id', $nuevosIds) as $resp) {
                if ($resp->id === $creadorId) continue;
                $resp->notify(new ActividadAsignada($actividad, 'fecha_limite', $resp->pivot->tipo));
            }
        }

        return back()->with('success', 'Actividad actualizada correctamente.');
    }

    public function destroy(Actividad $actividad)
    {
        Gate::authorize('integridad.eliminar');
        $actividad->delete();
        return back()->with('success', 'Actividad eliminada.');
    }

    public function updateAvance(Request $request, Actividad $actividad)
    {
        abort_unless($actividad->puedeEditarUsuario(), 403, 'No tienes permiso para actualizar esta actividad.');

        $request->validate(['avance' => 'required|integer|min:0|max:100']);

        $avance = (int) $request->avance;

        if ($avance >= 100) {
            $tieneEvidenciaValidada = $actividad->evidencias()->where('estado', 'validado')->exists();
            $estado = $tieneEvidenciaValidada ? 'completada' : 'en_proceso';
        } elseif ($avance > 0 && in_array($actividad->estado, ['pendiente', 'observado'])) {
            $estado = 'en_proceso';
        } else {
            $estado = $actividad->estado;
        }

        $avanceAntes = $actividad->avance;
        $actividad->update([
            'avance'             => $avance,
            'estado'             => $estado,
            'fecha_cumplimiento' => $estado === 'completada' ? now() : $actividad->fecha_cumplimiento,
        ]);

        if ($avance !== $avanceAntes) {
            $editor = Auth::user();
            $supervisores = $actividad->responsables()
                ->where('users.id', '!=', $editor->id)
                ->wherePivot('tipo', 'supervisor')
                ->get();
            foreach ($supervisores as $supervisor) {
                $supervisor->notify(new \App\Notifications\AvanceActualizado($actividad, $avance, $avanceAntes, $editor->name));
            }
        }

        return response()->json([
            'ok'          => true,
            'avance'      => $avance,
            'estado'      => $estado,
            'advertencia' => ($avance >= 100 && $estado !== 'completada')
                ? 'Avance guardado al 100%, pero se requiere al menos una evidencia validada para completar la actividad.'
                : null,
        ]);
    }

    public function historial(Actividad $actividad)
    {
        abort_unless($actividad->puedeEditarUsuario(), 403);

        $historial = ActividadHistorial::with('usuario')
            ->where('actividad_id', $actividad->id)
            ->latest()->get()
            ->map(fn($h) => [
                'campo'          => $h->campo,
                'campo_label'    => $h->campo_label,
                'valor_anterior' => $h->valor_anterior,
                'valor_nuevo'    => $h->valor_nuevo,
                'descripcion'    => $h->descripcion,
                'usuario'        => ['name' => $h->usuario?->name ?? 'Sistema'],
                'created_at'     => $h->created_at,
            ]);

        return response()->json($historial);
    }

    // ── Helper: mapear actividad a array para JSON ────────────────────────────
    private function actividadToArray(Actividad $a): array
    {
        $comp = $a->integridadPregunta?->componente;
        $hoy  = now();
        return [
            'id'          => $a->id,
            'codigo'      => $a->codigo,
            'nombre'      => $a->nombre,
            'componente'  => $comp?->nombre ?? '—',
            'pregunta'    => $a->integridadPregunta?->nombre ?? '—',
            'link_ficha'  => $a->integridadPregunta?->link_ficha,
            'unidad'      => $a->unidadOrganica?->sigla ?? '—',
            'responsable' => $a->responsables->where('pivot.tipo','principal')->first()?->name
                          ?? $a->responsables->first()?->name ?? '—',
            'avance'      => $a->avance,
            'estado'      => $a->estado,
            'prioridad'   => $a->prioridad,
            'fecha_limite'=> $a->fecha_limite?->format('d/m/Y'),
            'vencida'     => $a->fecha_limite && $a->fecha_limite->lt($hoy) && $a->estado !== 'completada',
            'dias_retraso'=> $a->fecha_limite && $a->fecha_limite->lt($hoy)
                ? (int) $hoy->diffInDays($a->fecha_limite) : 0,
        ];
    }
}
