<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Reconocimiento;
use App\Models\TrabajadorDestacado;
use App\Models\UnidadOrganica;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ReconocimientosController extends Controller
{
    private array $mesesNombres = [
        1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',
        5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',
        9=>'Setiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre',
    ];

    private array $categoriasList = [
        'Control Interno', 'Modelo de Integridad',
    ];

    private function buildQuery(int $anio, ?int $mes, ?string $categoria, ?string $modulo)
    {
        $q = TrabajadorDestacado::with(['unidadOrganica', 'registradoPor'])
            ->where('anio', $anio)
            ->where('activo', true)
            ->orderByDesc('created_at');
        if ($mes) $q->where('mes', $mes);
        if ($categoria) $q->where('categoria', $categoria);
        if ($modulo === 'sci')       $q->where('categoria', 'Control Interno');
        if ($modulo === 'integridad') $q->where('categoria', 'Modelo de Integridad');
        return $q;
    }

    public function index(Request $request)
    {
        $anio      = (int) $request->input('anio', now()->year);
        $mes       = $request->input('mes') ? (int) $request->input('mes') : null;
        $categoria = $request->input('categoria');
        $modulo    = $request->input('modulo'); // 'sci' | 'integridad' | null

        // Ranking de unidades
        $queryUnidades = Reconocimiento::with('unidadOrganica')
            ->where('anio', $anio)->orderBy('posicion');
        $mes ? $queryUnidades->where('mes', $mes) : $queryUnidades->whereNull('mes');
        $rankingUnidades = $queryUnidades->get();

        $trabajadores = $this->buildQuery($anio, $mes, $categoria, $modulo)->get();
        $top3         = $trabajadores->take(4);

        $stats = [
            'total_reconocidos'   => TrabajadorDestacado::where('anio', $anio)->where('activo', true)->count(),
            'total_sci'           => TrabajadorDestacado::where('anio', $anio)->where('activo', true)->where('categoria', 'Control Interno')->count(),
            'total_integridad'    => TrabajadorDestacado::where('anio', $anio)->where('activo', true)->where('categoria', 'Modelo de Integridad')->count(),
            'unidades_destacadas' => Reconocimiento::where('anio', $anio)->distinct('unidad_organica_id')->count(),
            'promedio_puntaje'    => round(TrabajadorDestacado::where('anio', $anio)->avg('puntaje_total') ?? 0, 1),
            'proxima_ceremonia'   => now()->locale('es')->addDays(30)->translatedFormat('d \d\e F \d\e Y'),
        ];

        $unidades   = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
        $usuarios   = User::with(['cargo', 'unidadOrganica'])->orderBy('name')->get();
        $anios      = range(now()->year, now()->year - 3);
        $meses      = $this->mesesNombres;
        $categorias = $this->categoriasList;

        return view('content.reconocimientos.index', compact(
            'rankingUnidades', 'trabajadores', 'top3',
            'stats', 'unidades', 'usuarios', 'anios',
            'meses', 'anio', 'mes', 'categorias', 'categoria', 'modulo'
        ));
    }

    public function ajax(Request $request)
    {
        $anio      = (int) $request->input('anio', now()->year);
        $mes       = $request->input('mes') ? (int) $request->input('mes') : null;
        $categoria = $request->input('categoria');
        $modulo    = $request->input('modulo');
        $buscar    = $request->input('buscar');

        $q = $this->buildQuery($anio, $mes, $categoria, $modulo);
        if ($buscar) {
            $q->where(function ($sq) use ($buscar) {
                $sq->where('nombre', 'like', "%{$buscar}%")
                   ->orWhere('cargo', 'like', "%{$buscar}%");
            });
        }

        $trabajadores = $q->get()->map(fn($t) => [
            'id'                      => $t->id,
            'nombre'                  => $t->nombre,
            'cargo'                   => $t->cargo,
            'foto_url'                => $t->foto_url,
            'categoria'               => $t->categoria,
            'unidad_sigla'            => $t->unidadOrganica?->sigla ?? '—',
            'unidad_organica_id'      => $t->unidad_organica_id,
            'puntaje_total'           => number_format($t->puntaje_total, 1),
            'nivel'                   => $t->nivel,
            'nivel_color'             => $t->nivel_color,
            'cumplimiento'            => $t->puntaje_cumplimiento,
            'puntualidad'             => $t->puntaje_puntualidad,
            'participacion'           => $t->puntaje_participacion,
            'responsabilidad'         => $t->puntaje_responsabilidad,
            'motivo'                  => $t->motivo,
            'dni'                     => $t->dni,
            'correo'                  => $t->correo,
            'numero_resolucion'       => $t->numero_resolucion,
            'user_id'                 => $t->user_id,
            'show_url'                => route('rep-reconocimientos.show', $t),
            'edit_url'                => route('rep-reconocimientos.update', $t),
            'delete_url'              => route('rep-reconocimientos.destroy', $t),
        ]);

        $stats = [
            'total'    => $trabajadores->count(),
            'promedio' => $trabajadores->count() ? number_format($trabajadores->avg('puntaje_total'), 1) : '0.0',
        ];

        return response()->json(['trabajadores' => $trabajadores, 'stats' => $stats]);
    }

    public function datosUsuario(User $usuario)
    {
        $usuario->load(['cargo', 'unidadOrganica']);
        return response()->json([
            'nombre'             => $usuario->name,
            'email'              => $usuario->email,
            'dni'                => $usuario->dni ?? '',
            'cargo'              => $usuario->cargo?->nombre ?? '',
            'unidad_organica_id' => $usuario->unidad_organica_id,
            'unidad_nombre'      => $usuario->unidadOrganica?->nombre ?? '',
            'foto_url'           => $usuario->profile_photo_url,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'                  => 'nullable|exists:users,id',
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
            'foto'                     => 'nullable|image|mimes:' . ImageService::ALLOWED_MIMES . '|max:' . ImageService::MAX_SIZE_KB,
            'resolucion_archivo'       => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $validated['registrado_por'] = Auth::id();

        $images = app(ImageService::class);

        if ($request->hasFile('foto')) {
            $validated['foto_ruta'] = $images->store(
                $request->file('foto'),
                'reconocimientos/fotos/' . $validated['anio']
            );
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
        Gate::authorize('reconocimientos.editar');

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
            'foto'               => 'nullable|image|mimes:' . ImageService::ALLOWED_MIMES . '|max:' . ImageService::MAX_SIZE_KB,
            'resolucion_archivo' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $images = app(ImageService::class);

        if ($request->hasFile('foto')) {
            $images->delete($trabajador->foto_ruta);
            $validated['foto_ruta'] = $images->store(
                $request->file('foto'),
                'reconocimientos/fotos/' . $trabajador->anio
            );
        }
        if ($request->hasFile('resolucion_archivo')) {
            $validated['resolucion_ruta'] = $request->file('resolucion_archivo')
                ->store('reconocimientos/resoluciones/' . $trabajador->anio, 'public');
        }
        unset($validated['foto'], $validated['resolucion_archivo']);

        $trabajador->update($validated);

        return back()->with('success', 'Reconocimiento actualizado correctamente.');
    }

    public function destroy(TrabajadorDestacado $trabajador)
    {
        Gate::authorize('reconocimientos.eliminar');
        app(ImageService::class)->delete($trabajador->foto_ruta);
        $trabajador->delete();
        return back()->with('success', 'Reconocimiento eliminado.');
    }

    public function show(TrabajadorDestacado $trabajador)
    {
        $trabajador->load(['unidadOrganica', 'registradoPor']);
        return view('content.reconocimientos.show', compact('trabajador'));
    }
}
