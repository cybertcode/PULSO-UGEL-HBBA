<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Paci;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PaciController extends Controller
{
    public function index(Request $request)
    {
        $anio = $request->get('anio', now()->year);

        $pacis = Paci::with('creadoPor')
            ->withCount('actividades')
            ->when($request->filled('estado'), fn($q) => $q->where('estado', $request->estado))
            ->orderByDesc('anio')
            ->orderByDesc('created_at')
            ->paginate(10)->withQueryString();

        $stats = [
            'total'       => Paci::count(),
            'en_ejecucion'=> Paci::where('estado', 'en_ejecucion')->count(),
            'aprobados'   => Paci::where('estado', 'aprobado')->count(),
            'promedio'    => (int) round(Paci::avg('avance') ?? 0),
        ];

        $actividades = Actividad::orderBy('nombre')->get(['id', 'nombre', 'estado']);
        $anios = range(now()->year - 2, now()->year + 1);

        return view('content.paci.index', compact('pacis', 'stats', 'actividades', 'anios'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo'             => 'required|string|max:255',
            'anio'               => 'required|integer|min:2020|max:2050',
            'descripcion'        => 'nullable|string',
            'numero_resolucion'  => 'nullable|string|max:100',
            'fecha_aprobacion'   => 'nullable|date',
            'fecha_inicio'       => 'nullable|date',
            'fecha_fin'          => 'nullable|date|after_or_equal:fecha_inicio',
            'estado'             => 'required|in:borrador,aprobado,en_ejecucion,cerrado',
            'avance'             => 'required|integer|min:0|max:100',
            'observaciones'      => 'nullable|string',
            'actividades'        => 'nullable|array',
            'actividades.*'      => 'exists:actividades,id',
            'archivo'            => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Corrija los errores del formulario.');
        }

        $data = $request->only([
            'titulo', 'anio', 'descripcion', 'numero_resolucion',
            'fecha_aprobacion', 'fecha_inicio', 'fecha_fin',
            'estado', 'avance', 'observaciones',
        ]);
        $data['creado_por'] = Auth::id();

        if ($request->hasFile('archivo')) {
            $data['archivo'] = $request->file('archivo')->store('paci', 'public');
        }

        $paci = Paci::create($data);

        if ($request->filled('actividades')) {
            $paci->actividades()->sync($request->actividades);
        }

        return back()->with('success', 'PACI registrado correctamente.');
    }

    public function update(Request $request, Paci $paci)
    {
        $validator = Validator::make($request->all(), [
            'titulo'             => 'required|string|max:255',
            'anio'               => 'required|integer|min:2020|max:2050',
            'descripcion'        => 'nullable|string',
            'numero_resolucion'  => 'nullable|string|max:100',
            'fecha_aprobacion'   => 'nullable|date',
            'fecha_inicio'       => 'nullable|date',
            'fecha_fin'          => 'nullable|date',
            'estado'             => 'required|in:borrador,aprobado,en_ejecucion,cerrado',
            'avance'             => 'required|integer|min:0|max:100',
            'observaciones'      => 'nullable|string',
            'actividades'        => 'nullable|array',
            'actividades.*'      => 'exists:actividades,id',
            'archivo'            => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Corrija los errores del formulario.');
        }

        $data = $request->only([
            'titulo', 'anio', 'descripcion', 'numero_resolucion',
            'fecha_aprobacion', 'fecha_inicio', 'fecha_fin',
            'estado', 'avance', 'observaciones',
        ]);

        if ($request->hasFile('archivo')) {
            if ($paci->archivo) Storage::disk('public')->delete($paci->archivo);
            $data['archivo'] = $request->file('archivo')->store('paci', 'public');
        }

        $paci->update($data);
        $paci->actividades()->sync($request->actividades ?? []);

        return back()->with('success', 'PACI actualizado correctamente.');
    }

    public function destroy(Paci $paci)
    {
        if ($paci->archivo) Storage::disk('public')->delete($paci->archivo);
        $paci->delete();
        return back()->with('success', 'PACI eliminado.');
    }
}
