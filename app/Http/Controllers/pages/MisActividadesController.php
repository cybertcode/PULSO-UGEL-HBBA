<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\ActividadHistorial;
use App\Models\Componente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MisActividadesController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Actividad::with(['componente', 'unidadOrganica', 'responsables', 'evidencias'])
            ->whereHas('responsables', fn($q) => $q->where('users.id', $user->id))
            ->orderByRaw("FIELD(estado,'vencida','observado','en_proceso','pendiente','completada')")
            ->orderBy('fecha_limite');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('componente_id')) {
            $query->where('componente_id', $request->componente_id);
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

        $actividades = $query->paginate(15)->withQueryString();

        // KPIs solo de mis actividades
        $base = Actividad::whereHas('responsables', fn($q) => $q->where('users.id', $user->id));
        $stats = [
            'total'       => (clone $base)->count(),
            'completadas' => (clone $base)->where('estado', 'completada')->count(),
            'en_proceso'  => (clone $base)->whereIn('estado', ['en_proceso', 'pendiente'])->count(),
            'vencidas'    => (clone $base)->where('estado', 'vencida')->count(),
            'sin_ev'      => (clone $base)->whereNotIn('estado', ['pendiente'])
                                ->whereDoesntHave('evidencias')->count(),
        ];
        $stats['porcentaje'] = $stats['total'] > 0
            ? round(($stats['completadas'] / $stats['total']) * 100) : 0;

        // Mis próximas a vencer (próximos 15 días)
        $proximas = Actividad::with('componente')
            ->whereHas('responsables', fn($q) => $q->where('users.id', $user->id))
            ->whereNotIn('estado', ['completada', 'vencida', 'observado'])
            ->whereDate('fecha_limite', '>=', now())
            ->whereDate('fecha_limite', '<=', now()->addDays(15))
            ->orderBy('fecha_limite')
            ->get();

        $componentes = Componente::where('activo', true)->orderBy('numero')->get();

        return view('content.mis-actividades.index', compact(
            'actividades', 'stats', 'proximas', 'componentes', 'user'
        ));
    }

    public function updateAvance(Request $request, Actividad $actividad)
    {
        $user = Auth::user();

        // Solo puede actualizar si es responsable
        abort_unless(
            $actividad->responsables()->where('users.id', $user->id)->exists(),
            403,
            'No tienes permiso para actualizar esta actividad.'
        );

        $request->validate([
            'avance'       => 'required|integer|min:0|max:100',
            'observaciones'=> 'nullable|string|max:500',
        ]);

        $data = ['avance' => $request->avance];

        if ($request->avance == 100 && $actividad->estado !== 'completada') {
            $data['estado']             = 'completada';
            $data['fecha_cumplimiento'] = now()->toDateString();
        } elseif ($request->avance > 0 && $actividad->estado === 'pendiente') {
            $data['estado'] = 'en_proceso';
        }

        if ($request->filled('observaciones')) {
            $data['observaciones'] = $request->observaciones;
        }

        $actividad->update($data);

        return response()->json([
            'success'    => true,
            'avance'     => $actividad->avance,
            'estado'     => $actividad->estado,
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
