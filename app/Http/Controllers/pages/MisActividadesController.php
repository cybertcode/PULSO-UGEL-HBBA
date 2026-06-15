<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\ActividadHistorial;
use App\Models\SciEje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MisActividadesController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Actividad::with([
                'sciPregunta.componente.eje',
                'integridadPregunta.componente.etapa',
                'unidadOrganica',
                'responsables',
                'evidencias',
            ])
            ->whereHas('responsables', fn($q) => $q->where('users.id', $user->id))
            ->orderByRaw("FIELD(estado,'vencida','observado','en_proceso','pendiente','completada')")
            ->orderBy('fecha_limite')
            ->orderByDesc('created_at');

        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }
        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(fn($q) => $q
                ->where('nombre', 'like', "%$b%")
                ->orWhere('codigo', 'like', "%$b%")
            );
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_limite', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_limite', '<=', $request->fecha_hasta);
        }
        if ($request->filled('avance_min')) {
            $query->where('avance', '>=', (int) $request->avance_min);
        }
        if ($request->filled('avance_max')) {
            $query->where('avance', '<=', (int) $request->avance_max);
        }
        if ($request->filled('evidencia')) {
            if ($request->evidencia === 'con') {
                $query->whereHas('evidencias');
            } elseif ($request->evidencia === 'sin') {
                $query->whereDoesntHave('evidencias');
            }
        }
        if ($request->filled('mi_rol')) {
            $query->whereHas('responsables', fn($q) => $q
                ->where('users.id', $user->id)
                ->where('actividad_responsables.tipo', $request->mi_rol)
            );
        }

        $actividades = $query->paginate(15)->withQueryString();

        // KPIs solo de mis actividades
        $base = Actividad::whereHas('responsables', fn($q) => $q->where('users.id', $user->id));
        $stats = [
            'total'       => (clone $base)->count(),
            'completadas' => (clone $base)->where('estado', 'completada')->count(),
            'en_proceso'  => (clone $base)->whereIn('estado', ['en_proceso', 'pendiente'])->count(),
            'vencidas'    => (clone $base)->where('estado', 'vencida')->count(),
            'observadas'  => (clone $base)->where(fn($q) => $q
                                ->where('estado', 'observado')
                                ->orWhere(fn($q2) => $q2->where('estado', 'completada')
                                    ->whereHas('evidencias', fn($e) => $e->where('estado', 'rechazado'))
                                )
                            )->count(),
            'sin_ev'      => (clone $base)->whereNotIn('estado', ['pendiente'])
                                ->whereDoesntHave('evidencias')->count(),
            'ev_rechazadas' => (clone $base)->whereHas('evidencias', fn($q) => $q->where('estado', 'rechazado'))->count(),
            'sci'         => (clone $base)->where('modulo', 'sci')->count(),
            'integridad'  => (clone $base)->where('modulo', 'integridad')->count(),
        ];
        $stats['porcentaje'] = $stats['total'] > 0
            ? round(($stats['completadas'] / $stats['total']) * 100) : 0;

        // Mis próximas a vencer (próximos 15 días)
        $proximas = Actividad::with([
                'sciPregunta.componente',
                'integridadPregunta.componente',
            ])
            ->whereHas('responsables', fn($q) => $q->where('users.id', $user->id))
            ->whereNotIn('estado', ['completada', 'vencida', 'observado'])
            ->whereDate('fecha_limite', '>=', now())
            ->whereDate('fecha_limite', '<=', now()->addDays(15))
            ->orderBy('fecha_limite')
            ->get();

        // Ejes SCI del año actual para filtro
        $sciEjes = SciEje::where('activo', true)->where('anio', now()->year)->orderBy('orden')->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html'  => view('content.mis-actividades._cards', compact('actividades', 'user'))->render(),
                'stats' => $stats,
                'total' => $actividades->total(),
                'from'  => $actividades->firstItem() ?? 0,
                'to'    => $actividades->lastItem() ?? 0,
                'pages' => $actividades->hasPages()
                    ? $actividades->links()->toHtml()
                    : '',
            ]);
        }

        return view('content.mis-actividades.index', compact(
            'actividades', 'stats', 'proximas', 'sciEjes', 'user'
        ));
    }

    public function updateAvance(Request $request, Actividad $actividad)
    {
        $user = Auth::user();

        abort_unless(
            $actividad->responsables()->where('users.id', $user->id)->exists(),
            403,
            'No tienes permiso para actualizar esta actividad.'
        );

        $request->validate([
            'avance'        => 'required|integer|min:0|max:100',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $avance = (int) $request->avance;
        $data   = ['avance' => $avance];

        if ($avance == 100 && $actividad->estado !== 'completada') {
            // Solo puede completarse si tiene al menos una evidencia validada
            $tieneEvidenciaValidada = $actividad->evidencias()
                ->where('estado', 'validado')->exists();

            if ($tieneEvidenciaValidada) {
                $data['estado']             = 'completada';
                $data['fecha_cumplimiento'] = now()->toDateString();
            } else {
                // Llega a 100% pero sin evidencia validada → queda en_proceso
                $data['estado'] = 'en_proceso';
                $actividad->update($data);
                return response()->json([
                    'success'      => false,
                    'avance'       => $avance,
                    'estado'       => 'en_proceso',
                    'estado_label' => 'En Proceso',
                    'actividad_id' => $actividad->id,
                    'advertencia'  => 'El avance se guardó en 100%, pero la actividad no puede marcarse como completada hasta que tengas al menos una evidencia validada.',
                ]);
            }
        } elseif ($avance > 0 && in_array($actividad->estado, ['pendiente', 'observado'])) {
            $data['estado'] = 'en_proceso';
        }

        if ($request->filled('observaciones')) {
            $data['observaciones'] = $request->observaciones;
        }

        $actividad->update($data);

        return response()->json([
            'success'      => true,
            'avance'       => $actividad->avance,
            'estado'       => $actividad->estado,
            'estado_label' => $actividad->estado_label,
        ]);
    }

    public function historial(Actividad $actividad)
    {
        $user = Auth::user();
        abort_unless(
            $actividad->responsables()->where('users.id', $user->id)->exists(),
            403
        );

        $historial = ActividadHistorial::with('usuario')
            ->where('actividad_id', $actividad->id)
            ->latest()
            ->get();

        return response()->json($historial->map(fn($h) => [
            'campo'          => $h->campo,
            'valor_anterior' => $h->valor_anterior,
            'valor_nuevo'    => $h->valor_nuevo,
            'descripcion'    => $h->descripcion,
            'usuario'        => $h->usuario?->name ?? 'Sistema',
            'fecha'          => $h->created_at->format('d/m/Y H:i'),
        ]));
    }
}
