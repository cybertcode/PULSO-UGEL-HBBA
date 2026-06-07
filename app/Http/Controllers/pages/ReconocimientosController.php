<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Reconocimiento;
use App\Models\TrabajadorDestacado;
use App\Models\UnidadOrganica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReconocimientosController extends Controller
{
    public function index(Request $request)
    {
        $anio      = $request->input('anio', now()->year);
        $mes       = $request->input('mes');
        $categoria = $request->input('categoria');

        // Ranking de unidades por reconocimientos
        $queryUnidades = Reconocimiento::with('unidadOrganica')
            ->where('anio', $anio)
            ->orderBy('posicion');
        if ($mes) $queryUnidades->where('mes', $mes);
        else       $queryUnidades->whereNull('mes');
        $rankingUnidades = $queryUnidades->get();

        // Trabajadores destacados del período
        $queryTrabajadores = TrabajadorDestacado::with(['unidadOrganica', 'registradoPor'])
            ->where('anio', $anio)
            ->where('activo', true)
            ->orderByDesc('puntaje_total');
        if ($mes) $queryTrabajadores->where('mes', $mes);
        else       $queryTrabajadores->whereNull('mes');
        if ($categoria) $queryTrabajadores->where('categoria', $categoria);

        $trabajadores = $queryTrabajadores->get();
        $top3         = $trabajadores->take(3);
        $resto        = $trabajadores->skip(3);

        // Stats resumen
        $stats = [
            'total_reconocidos'  => TrabajadorDestacado::where('anio', $anio)->where('activo', true)->count(),
            'unidades_destacadas'=> Reconocimiento::where('anio', $anio)->distinct('unidad_organica_id')->count(),
            'promedio_puntaje'   => round(TrabajadorDestacado::where('anio', $anio)->avg('puntaje_total') ?? 0, 1),
            'proxima_ceremonia'  => now()->addDays(30)->format('d/m/Y'),
        ];

        $unidades = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
        $anios    = range(now()->year, now()->year - 3);
        $meses    = [
            1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',
            5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',
            9=>'Setiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre',
        ];
        $categorias = ['Control Interno', 'Modelo de Integridad', 'Buenas Prácticas', 'Apoyo Estratégico'];

        return view('content.reconocimientos.index', compact(
            'rankingUnidades', 'trabajadores', 'top3', 'resto',
            'stats', 'unidades', 'anios', 'meses', 'anio', 'mes',
            'categorias', 'categoria'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'                   => 'required|string|max:255',
            'cargo'                    => 'nullable|string|max:255',
            'unidad_organica_id'       => 'nullable|exists:unidades_organicas,id',
            'dni'                      => 'nullable|string|max:8',
            'correo'                   => 'nullable|email|max:100',
            'puntaje_cumplimiento'     => 'required|numeric|min:0|max:100',
            'puntaje_puntualidad'      => 'required|numeric|min:0|max:100',
            'puntaje_participacion'    => 'required|numeric|min:0|max:100',
            'puntaje_responsabilidad'  => 'required|numeric|min:0|max:100',
            'anio'                     => 'required|integer|min:2020|max:2099',
            'mes'                      => 'nullable|integer|min:1|max:12',
            'categoria'                => 'nullable|string|max:60',
            'motivo'                   => 'nullable|string',
            'numero_resolucion'        => 'nullable|string|max:60',
            'foto'                     => 'nullable|image|max:2048',
            'resolucion_archivo'       => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $validated['registrado_por'] = Auth::id();

        if ($request->hasFile('foto')) {
            $validated['foto_ruta'] = $request->file('foto')
                ->store('reconocimientos/fotos/' . $validated['anio'], 'public');
        }
        if ($request->hasFile('resolucion_archivo')) {
            $validated['resolucion_ruta'] = $request->file('resolucion_archivo')
                ->store('reconocimientos/resoluciones/' . $validated['anio'], 'public');
        }

        unset($validated['foto'], $validated['resolucion_archivo']);

        $trabajador = TrabajadorDestacado::create($validated);

        return back()->with('success', "Reconocimiento a «{$trabajador->nombre}» registrado correctamente.");
    }

    public function update(Request $request, TrabajadorDestacado $trabajador)
    {
        $validated = $request->validate([
            'nombre'                   => 'required|string|max:255',
            'cargo'                    => 'nullable|string|max:255',
            'unidad_organica_id'       => 'nullable|exists:unidades_organicas,id',
            'dni'                      => 'nullable|string|max:8',
            'correo'                   => 'nullable|email|max:100',
            'puntaje_cumplimiento'     => 'required|numeric|min:0|max:100',
            'puntaje_puntualidad'      => 'required|numeric|min:0|max:100',
            'puntaje_participacion'    => 'required|numeric|min:0|max:100',
            'puntaje_responsabilidad'  => 'required|numeric|min:0|max:100',
            'categoria'                => 'nullable|string|max:60',
            'motivo'                   => 'nullable|string',
            'numero_resolucion'        => 'nullable|string|max:60',
            'foto'                     => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($trabajador->foto_ruta) {
                Storage::disk('public')->delete($trabajador->foto_ruta);
            }
            $validated['foto_ruta'] = $request->file('foto')
                ->store('reconocimientos/fotos/' . $trabajador->anio, 'public');
        }
        unset($validated['foto']);

        $trabajador->update($validated);

        return back()->with('success', 'Reconocimiento actualizado correctamente.');
    }

    public function destroy(TrabajadorDestacado $trabajador)
    {
        if ($trabajador->foto_ruta) {
            Storage::disk('public')->delete($trabajador->foto_ruta);
        }
        $trabajador->delete();
        return back()->with('success', 'Reconocimiento eliminado.');
    }

    public function show(TrabajadorDestacado $trabajador)
    {
        $trabajador->load('unidadOrganica');
        return view('content.reconocimientos.show', compact('trabajador'));
    }
}
