<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertasController extends Controller
{
    public function index()
    {
        $stats = [
            'total'  => Alerta::where('leida', false)->count(),
            'alta'   => Alerta::where('leida', false)->where('prioridad', 'alta')->count(),
            'media'  => Alerta::where('leida', false)->where('prioridad', 'media')->count(),
            'baja'   => Alerta::where('leida', false)->where('prioridad', 'baja')->count(),
        ];

        $alertas = Alerta::with(['actividad.componente', 'unidadOrganica'])
            ->orderByRaw("FIELD(prioridad,'alta','media','baja')")
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('content.alertas.index', compact('stats', 'alertas'));
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
