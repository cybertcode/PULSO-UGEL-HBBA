<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use Illuminate\Http\Request;

class AlertasController extends Controller
{
    public function index(Request $request)
    {
        $tab      = $request->input('tab', 'pendientes');
        $prioridad = $request->input('prioridad');

        $stats = [
            'total'    => Alerta::count(),
            'pendientes'=> Alerta::where('leida', false)->count(),
            'resueltas' => Alerta::where('leida', true)->count(),
            'alta'     => Alerta::where('leida', false)->where('prioridad', 'alta')->count(),
            'media'    => Alerta::where('leida', false)->where('prioridad', 'media')->count(),
            'baja'     => Alerta::where('leida', false)->where('prioridad', 'baja')->count(),
        ];

        $query = Alerta::with(['actividad.componente', 'actividad.responsable', 'unidadOrganica'])
            ->orderByRaw("FIELD(prioridad,'alta','media','baja')")
            ->orderByDesc('created_at');

        if ($tab === 'resueltas') {
            $query->where('leida', true);
        } else {
            $query->where('leida', false);
        }

        if ($prioridad) {
            $query->where('prioridad', $prioridad);
        }

        $alertas = $query->paginate(15)->withQueryString();

        return view('content.alertas.index', compact('stats', 'alertas', 'tab', 'prioridad'));
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
}
