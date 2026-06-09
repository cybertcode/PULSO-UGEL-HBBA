<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Evidencia;
use App\Models\Actividad;
use App\Models\Componente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvidenciasController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $stats = [
            'total'      => Evidencia::count(),
            'validadas'  => Evidencia::where('estado', 'validado')->count(),
            'pendientes' => Evidencia::where('estado', 'pendiente')->count(),
            'rechazadas' => Evidencia::where('estado', 'rechazado')->count(),
        ];

        $query = Evidencia::with(['actividad.componente', 'subidoPor', 'validadoPor'])
            ->orderByDesc('created_at');

        if ($request->filled('actividad_id')) {
            $query->where('actividad_id', $request->actividad_id);
        }
        if ($request->filled('componente_id')) {
            $query->whereHas('actividad', fn($q) => $q->where('componente_id', $request->componente_id));
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(fn($q) => $q
                ->where('numero_sgd', 'like', "%$buscar%")
                ->orWhere('titulo', 'like', "%$buscar%")
            );
        }

        $evidencias = $query->paginate(15)->withQueryString();
        $componentes = Componente::where('activo', true)->orderBy('numero')->get();

        // Solo actividades asignadas al usuario (sin importar estado)
        $actividades = Actividad::whereHas('responsables', fn($q) => $q->where('users.id', $user->id))
            ->orderBy('codigo')
            ->get(['id', 'codigo', 'nombre', 'estado']);

        // Solo preseleccionar y abrir modal cuando se llega con ?nueva=1 (desde otro módulo)
        $actividadPresel = $request->boolean('nueva') ? $request->input('actividad_id') : null;

        return view('content.evidencias.index', compact(
            'stats', 'evidencias', 'actividades', 'componentes', 'actividadPresel'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'actividad_id'  => [
                'required',
                'exists:actividades,id',
                // Solo puede registrar evidencia de actividades que le fueron asignadas
                fn($attr, $val, $fail) => Actividad::whereHas('responsables', fn($q) => $q->where('users.id', $user->id))
                    ->where('id', $val)->exists() ?: $fail('No tienes permiso para registrar evidencias en esta actividad.'),
            ],
            'titulo'        => 'required|string|max:255',
            'numero_sgd'    => 'nullable|string|max:50',
            'descripcion'   => 'nullable|string',
            'url_documento' => 'nullable|url|max:500',
        ]);

        Evidencia::create([
            'actividad_id'  => $request->actividad_id,
            'subido_por'    => $user->id,
            'titulo'        => $request->titulo,
            'numero_sgd'    => $request->numero_sgd,
            'descripcion'   => $request->descripcion,
            'url_documento' => $request->url_documento ?: null,
            'estado'        => 'pendiente',
        ]);

        return back()->with('success', 'Evidencia registrada correctamente. Pendiente de validación.');
    }

    public function update(Request $request, Evidencia $evidencia)
    {
        $user = Auth::user();

        // Solo puede editar si la subió él mismo y está pendiente
        abort_unless(
            $evidencia->subido_por === $user->id && $evidencia->estado === 'pendiente',
            403,
            'Solo puedes editar evidencias pendientes que registraste tú.'
        );

        $request->validate([
            'titulo'        => 'required|string|max:255',
            'numero_sgd'    => 'nullable|string|max:50',
            'descripcion'   => 'nullable|string',
            'url_documento' => 'nullable|url|max:500',
        ]);

        $evidencia->update([
            'titulo'        => $request->titulo,
            'numero_sgd'    => $request->numero_sgd,
            'descripcion'   => $request->descripcion,
            'url_documento' => $request->url_documento ?: null,
        ]);

        return back()->with('success', 'Evidencia actualizada correctamente.');
    }

    public function validar(Request $request, Evidencia $evidencia)
    {
        $request->validate([
            'accion'         => 'required|in:validado,rechazado',
            'motivo_rechazo' => 'required_if:accion,rechazado|nullable|string',
        ]);

        $evidencia->update([
            'estado'         => $request->accion,
            'validado_por'   => Auth::id(),
            'validado_at'    => now(),
            'motivo_rechazo' => $request->accion === 'rechazado' ? $request->motivo_rechazo : null,
        ]);

        $msg = $request->accion === 'validado' ? 'Evidencia validada.' : 'Evidencia rechazada.';
        return back()->with('success', $msg);
    }

    public function destroy(Evidencia $evidencia)
    {
        $evidencia->delete();
        return back()->with('success', 'Evidencia eliminada.');
    }
}
