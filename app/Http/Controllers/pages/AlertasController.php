<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Services\AlertaService;
use Illuminate\Http\Request;

class AlertasController extends Controller
{
    public function index(Request $request)
    {
        $tab      = $request->input('tab', 'pendientes');
        $modulo   = $request->input('modulo');
        $prioridad = $request->input('prioridad');
        $tipo      = $request->input('tipo');

        $stats = [
            'total'       => Alerta::count(),
            'pendientes'  => Alerta::where('leida', false)->count(),
            'resueltas'   => Alerta::where('leida', true)->count(),
            'alta'        => Alerta::where('leida', false)->where('prioridad', 'alta')->count(),
            'media'       => Alerta::where('leida', false)->where('prioridad', 'media')->count(),
            'baja'        => Alerta::where('leida', false)->where('prioridad', 'baja')->count(),
            'sci'         => Alerta::where('modulo', 'sci')->where('leida', false)->count(),
            'integridad'  => Alerta::where('modulo', 'integridad')->where('leida', false)->count(),
        ];

        $query = Alerta::with(['actividad.responsables', 'unidadOrganica'])
            ->orderByRaw("FIELD(prioridad,'alta','media','baja')")
            ->orderByDesc('created_at');

        if ($tab === 'resueltas') {
            $query->where('leida', true);
        } else {
            $query->where('leida', false);
        }

        if ($modulo)   $query->where('modulo', $modulo);
        if ($prioridad) $query->where('prioridad', $prioridad);
        if ($tipo)      $query->where('tipo', $tipo);

        $alertas = $query->paginate(20)->withQueryString();

        return view('content.alertas.index', compact(
            'stats', 'alertas', 'tab', 'modulo', 'prioridad', 'tipo'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'             => 'required|string|max:255',
            'mensaje'            => 'required|string',
            'prioridad'          => 'required|in:alta,media,baja',
            'tipo'               => 'required|in:vencimiento,vencimiento_proximo,avance_bajo,evidencia_falta,sistema',
            'modulo'             => 'required|in:sci,integridad',
            'actividad_id'       => 'nullable|exists:actividades,id',
            'unidad_organica_id' => 'nullable|exists:unidades_organicas,id',
            'usuario_id'         => 'nullable|exists:users,id',
            'enviar_email'       => 'nullable|boolean',
        ]);

        $alerta = Alerta::create($validated);

        // Si se marcó "enviar email" y notif_email está inactivo, forzar envío manual
        if ($request->boolean('enviar_email') && !$alerta->email_enviado) {
            try {
                app(AlertaService::class)->enviarEmailManual($alerta);
            } catch (\Throwable $e) {
                return back()->with('warning', 'Alerta creada, pero no se pudo enviar el email: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Alerta creada correctamente.');
    }

    public function marcarLeida(Alerta $alerta)
    {
        $alerta->update(['leida' => true, 'leida_at' => now()]);
        return back()->with('success', 'Alerta marcada como leída.');
    }

    public function marcarTodasLeidas(Request $request)
    {
        $query = Alerta::where('leida', false);
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }
        $query->update(['leida' => true, 'leida_at' => now()]);
        return back()->with('success', 'Alertas marcadas como leídas.');
    }

    public function enviarEmail(Alerta $alerta, AlertaService $service)
    {
        try {
            $service->enviarEmailManual($alerta);
            return back()->with('success', 'Email de alerta enviado correctamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo enviar el email: ' . $e->getMessage());
        }
    }

    public function destroy(Alerta $alerta)
    {
        $alerta->delete();
        return back()->with('success', 'Alerta eliminada.');
    }
}
