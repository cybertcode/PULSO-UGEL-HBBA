<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\Actividad;
use App\Models\UnidadOrganica;
use App\Jobs\EnviarAlertaEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertasController extends Controller
{
    public function index(Request $request)
    {
        $tab      = $request->input('tab', 'pendientes');
        $prioridad = $request->input('prioridad');
        $tipo      = $request->input('tipo');

        $stats = [
            'total'     => Alerta::count(),
            'pendientes'=> Alerta::where('leida', false)->count(),
            'resueltas' => Alerta::where('leida', true)->count(),
            'alta'      => Alerta::where('leida', false)->where('prioridad', 'alta')->count(),
            'media'     => Alerta::where('leida', false)->where('prioridad', 'media')->count(),
            'baja'      => Alerta::where('leida', false)->where('prioridad', 'baja')->count(),
        ];

        $query = Alerta::with(['actividad.componente', 'actividad.responsables', 'unidadOrganica'])
            ->orderByRaw("FIELD(prioridad,'alta','media','baja')")
            ->orderByDesc('created_at');

        if ($tab === 'resueltas') {
            $query->where('leida', true);
        } else {
            $query->where('leida', false);
        }

        if ($prioridad) $query->where('prioridad', $prioridad);
        if ($tipo)      $query->where('tipo', $tipo);

        $alertas = $query->paginate(15)->withQueryString();

        return view('content.alertas.index', compact('stats', 'alertas', 'tab', 'prioridad', 'tipo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'            => 'required|string|max:255',
            'mensaje'           => 'required|string',
            'prioridad'         => 'required|in:alta,media,baja',
            'tipo'              => 'required|in:vencimiento,avance_bajo,evidencia_falta,sistema',
            'actividad_id'      => 'nullable|exists:actividades,id',
            'unidad_organica_id'=> 'nullable|exists:unidades_organicas,id',
            'usuario_id'        => 'nullable|exists:users,id',
            'enviar_email'      => 'nullable|boolean',
        ]);

        $alerta = Alerta::create($validated);

        // Enviar correo si se solicitó
        if ($request->boolean('enviar_email')) {
            dispatch(new EnviarAlertaEmail($alerta));
        }

        return back()->with('success', 'Alerta creada correctamente.');
    }

    public function marcarLeida(Alerta $alerta)
    {
        $alerta->update(['leida' => true, 'leida_at' => now()]);
        return back()->with('success', 'Alerta marcada como leída.');
    }

    public function marcarTodasLeidas()
    {
        Alerta::where('leida', false)->update(['leida' => true, 'leida_at' => now()]);
        return back()->with('success', 'Todas las alertas marcadas como leídas.');
    }

    public function destroy(Alerta $alerta)
    {
        $alerta->delete();
        return back()->with('success', 'Alerta eliminada.');
    }

    /** Generar alertas automáticas según el estado actual de actividades */
    public static function generarAlertasAutomaticas(): int
    {
        $generadas = 0;

        // 1. Actividades vencidas sin alerta existente
        Actividad::whereNotIn('estado', ['completada', 'observado'])
            ->whereDate('fecha_limite', '<', now())
            ->whereDoesntHave('alertas', fn($q) => $q->where('tipo', 'vencimiento')->where('leida', false))
            ->each(function ($actividad) use (&$generadas) {
                $alerta = Alerta::create([
                    'actividad_id'      => $actividad->id,
                    'usuario_id'        => $actividad->responsablePrincipal()->first()?->id ?? $actividad->responsables()->first()?->id,
                    'unidad_organica_id'=> $actividad->unidad_organica_id,
                    'titulo'            => "Actividad vencida: {$actividad->nombre}",
                    'mensaje'           => "La actividad «{$actividad->nombre}» (código: {$actividad->codigo}) venció el {$actividad->fecha_limite->format('d/m/Y')} y no ha sido completada.",
                    'tipo'              => 'vencimiento',
                    'prioridad'         => 'alta',
                ]);
                dispatch(new EnviarAlertaEmail($alerta));
                $generadas++;
            });

        // 2. Actividades sin avance después de 7 días de inicio
        Actividad::where('estado', 'pendiente')
            ->where('avance', 0)
            ->whereNotNull('fecha_inicio')
            ->whereDate('fecha_inicio', '<', now()->subDays(7))
            ->whereDoesntHave('alertas', fn($q) => $q->where('tipo', 'avance_bajo')->where('leida', false))
            ->each(function ($actividad) use (&$generadas) {
                Alerta::create([
                    'actividad_id'      => $actividad->id,
                    'usuario_id'        => $actividad->responsablePrincipal()->first()?->id ?? $actividad->responsables()->first()?->id,
                    'unidad_organica_id'=> $actividad->unidad_organica_id,
                    'titulo'            => "Sin avance: {$actividad->nombre}",
                    'mensaje'           => "La actividad «{$actividad->nombre}» lleva más de 7 días sin registrar avance.",
                    'tipo'              => 'avance_bajo',
                    'prioridad'         => 'media',
                ]);
                $generadas++;
            });

        // 3. Actividades sin evidencias
        Actividad::where('estado', 'en_proceso')
            ->whereDoesntHave('evidencias')
            ->whereDoesntHave('alertas', fn($q) => $q->where('tipo', 'evidencia_falta')->where('leida', false))
            ->each(function ($actividad) use (&$generadas) {
                Alerta::create([
                    'actividad_id'      => $actividad->id,
                    'usuario_id'        => $actividad->responsablePrincipal()->first()?->id ?? $actividad->responsables()->first()?->id,
                    'unidad_organica_id'=> $actividad->unidad_organica_id,
                    'titulo'            => "Sin evidencias: {$actividad->nombre}",
                    'mensaje'           => "La actividad «{$actividad->nombre}» está en proceso pero no tiene evidencias adjuntas.",
                    'tipo'              => 'evidencia_falta',
                    'prioridad'         => 'media',
                ]);
                $generadas++;
            });

        return $generadas;
    }
}
