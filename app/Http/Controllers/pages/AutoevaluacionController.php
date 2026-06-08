<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Autoevaluacion;
use App\Models\AutoevaluacionRespuesta;
use App\Models\Componente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AutoevaluacionController extends Controller
{
    public function index(Request $request)
    {
        $autoevaluaciones = Autoevaluacion::with('elaboradoPor')
            ->withCount('respuestas')
            ->when($request->filled('estado'), fn($q) => $q->where('estado', $request->estado))
            ->when($request->filled('anio'),   fn($q) => $q->where('anio', $request->anio))
            ->orderByDesc('anio')
            ->orderByDesc('created_at')
            ->paginate(10)->withQueryString();

        $stats = [
            'total'      => Autoevaluacion::count(),
            'abiertas'   => Autoevaluacion::where('estado', 'abierta')->count(),
            'cerradas'   => Autoevaluacion::where('estado', 'cerrada')->count(),
            'promedio'   => (int) round(Autoevaluacion::whereNotNull('puntaje_total')->avg('puntaje_total') ?? 0),
        ];

        $componentes = Componente::where('activo', true)->orderBy('numero')->get();
        $anios       = range(now()->year - 2, now()->year + 1);

        return view('content.autoevaluacion.index', compact(
            'autoevaluaciones', 'stats', 'componentes', 'anios'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo'       => 'required|string|max:255',
            'anio'         => 'required|integer|min:2020|max:2050',
            'periodo'      => 'required|in:I_trimestre,II_trimestre,III_trimestre,IV_trimestre,semestral,anual',
            'fecha_inicio' => 'nullable|date',
            'fecha_cierre' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado'       => 'required|in:abierta,en_proceso,cerrada',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Corrija los errores.');
        }

        Autoevaluacion::create(array_merge(
            $request->only(['titulo', 'anio', 'periodo', 'fecha_inicio', 'fecha_cierre', 'estado']),
            ['elaborado_por' => Auth::id()]
        ));

        return back()->with('success', 'Autoevaluación creada. Ahora puede registrar las respuestas.');
    }

    public function show(Autoevaluacion $autoevaluacion)
    {
        $autoevaluacion->load(['respuestas.componente', 'elaboradoPor']);
        $componentes = Componente::where('activo', true)->orderBy('numero')->get();

        $respuestas_por_componente = $autoevaluacion->respuestas->groupBy('componente_id');

        return view('content.autoevaluacion.show', compact(
            'autoevaluacion', 'componentes', 'respuestas_por_componente'
        ));
    }

    public function guardarRespuestas(Request $request, Autoevaluacion $autoevaluacion)
    {
        $request->validate([
            'respuestas'                   => 'required|array',
            'respuestas.*.componente_id'   => 'required|exists:componentes,id',
            'respuestas.*.pregunta'        => 'required|string',
            'respuestas.*.respuesta'       => 'nullable|in:si,no,parcial,no_aplica',
            'respuestas.*.puntaje'         => 'nullable|integer|min:0|max:3',
            'respuestas.*.evidencia'       => 'nullable|string',
            'respuestas.*.observacion'     => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $autoevaluacion) {
            foreach ($request->respuestas as $r) {
                AutoevaluacionRespuesta::updateOrCreate(
                    ['autoevaluacion_id' => $autoevaluacion->id, 'pregunta' => $r['pregunta']],
                    [
                        'componente_id' => $r['componente_id'],
                        'respuesta'     => $r['respuesta'] ?? null,
                        'puntaje'       => $r['puntaje'] ?? 0,
                        'evidencia'     => $r['evidencia'] ?? null,
                        'observacion'   => $r['observacion'] ?? null,
                    ]
                );
            }

            $total = $autoevaluacion->respuestas()->sum('puntaje');
            $autoevaluacion->update(['puntaje_total' => $total]);
        });

        return back()->with('success', 'Respuestas guardadas correctamente.');
    }

    public function cerrar(Autoevaluacion $autoevaluacion)
    {
        $total = $autoevaluacion->respuestas()->sum('puntaje');
        $autoevaluacion->update([
            'estado'        => 'cerrada',
            'puntaje_total' => $total,
        ]);
        return back()->with('success', 'Autoevaluación cerrada correctamente.');
    }

    public function destroy(Autoevaluacion $autoevaluacion)
    {
        $autoevaluacion->delete();
        return back()->with('success', 'Autoevaluación eliminada.');
    }
}
