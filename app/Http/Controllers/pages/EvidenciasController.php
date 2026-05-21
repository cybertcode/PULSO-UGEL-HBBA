<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Evidencia;
use App\Models\Actividad;
use App\Models\Componente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EvidenciasController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total'      => Evidencia::count(),
            'validadas'  => Evidencia::where('estado', 'validado')->count(),
            'pendientes' => Evidencia::where('estado', 'pendiente')->count(),
            'rechazadas' => Evidencia::where('estado', 'rechazado')->count(),
        ];

        $query = Evidencia::with(['actividad.componente', 'subidoPor', 'validadoPor'])
            ->orderByDesc('created_at');

        if ($request->filled('componente_id')) {
            $query->whereHas('actividad', fn($q) => $q->where('componente_id', $request->componente_id));
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(fn($q) => $q->where('numero_sgd', 'like', "%$buscar%")
                ->orWhere('titulo', 'like', "%$buscar%"));
        }

        $evidencias  = $query->paginate(15)->withQueryString();
        $actividades = Actividad::whereNotIn('estado', ['cancelada'])->orderBy('nombre')->get();
        $componentes = Componente::where('activo', true)->orderBy('numero')->get();

        return view('content.evidencias.index', compact(
            'stats', 'evidencias', 'actividades', 'componentes'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'actividad_id' => 'required|exists:actividades,id',
            'titulo'       => 'required|string|max:255',
            'numero_sgd'   => 'nullable|string|max:50',
            'descripcion'  => 'nullable|string',
            'archivo'      => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ]);

        $file = $request->file('archivo');
        $ruta = $file->store('evidencias/' . now()->format('Y/m'), 'public');

        Evidencia::create([
            'actividad_id'   => $request->actividad_id,
            'subido_por'     => Auth::id(),
            'titulo'         => $request->titulo,
            'numero_sgd'     => $request->numero_sgd,
            'descripcion'    => $request->descripcion,
            'archivo_ruta'   => $ruta,
            'archivo_nombre' => $file->getClientOriginalName(),
            'archivo_tipo'   => $file->getMimeType(),
            'archivo_tamanio'=> $file->getSize(),
            'estado'         => 'pendiente',
        ]);

        return back()->with('success', 'Evidencia subida correctamente. Pendiente de validación.');
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
        Storage::disk('public')->delete($evidencia->archivo_ruta);
        $evidencia->delete();
        return back()->with('success', 'Evidencia eliminada.');
    }
}
