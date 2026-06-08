<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Jobs\EnviarAlertaEmail;
use App\Models\Alerta;
use Illuminate\Http\Request;

class AlertasController extends Controller
{
    public function index(Request $request)
    {
        $tab      = $request->input('tab', 'pendientes');
        $prioridad = $request->input('prioridad');
        $tipo      = $request->input('tipo');

        $stats = [
            'total'      => Alerta::count(),
            'pendientes' => Alerta::where('leida', false)->count(),
            'resueltas'  => Alerta::where('leida', true)->count(),
            'alta'       => Alerta::where('leida', false)->where('prioridad', 'alta')->count(),
            'media'      => Alerta::where('leida', false)->where('prioridad', 'media')->count(),
            'baja'       => Alerta::where('leida', false)->where('prioridad', 'baja')->count(),
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
            'titulo'             => 'required|string|max:255',
            'mensaje'            => 'required|string',
            'prioridad'          => 'required|in:alta,media,baja',
            'tipo'               => 'required|in:vencimiento,avance_bajo,evidencia_falta,sistema',
            'actividad_id'       => 'nullable|exists:actividades,id',
            'unidad_organica_id' => 'nullable|exists:unidades_organicas,id',
            'usuario_id'         => 'nullable|exists:users,id',
            'enviar_email'       => 'nullable|boolean',
        ]);

        $alerta = Alerta::create($validated);

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
}
