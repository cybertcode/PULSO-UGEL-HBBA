<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Evidencia;
use App\Models\Actividad;
use App\Models\SciEje;
use App\Models\IntegridadEtapa;
use Illuminate\Http\Request;
use App\Notifications\EvidenciaRevisada;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EvidenciasController extends Controller
{
    public function index(Request $request)
    {
        $user   = Auth::user();
        $modulo = $request->input('modulo', 'sci');  // tab activo por defecto

        // ── Stats globales (por módulo) ───────────────────────────────────────
        $stats = [
            'sci' => [
                'total'      => $this->countByModulo('sci'),
                'validadas'  => $this->countByModulo('sci', 'validado'),
                'pendientes' => $this->countByModulo('sci', 'pendiente'),
                'rechazadas' => $this->countByModulo('sci', 'rechazado'),
            ],
            'integridad' => [
                'total'      => $this->countByModulo('integridad'),
                'validadas'  => $this->countByModulo('integridad', 'validado'),
                'pendientes' => $this->countByModulo('integridad', 'pendiente'),
                'rechazadas' => $this->countByModulo('integridad', 'rechazado'),
            ],
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return $this->jsonResponse($request);
        }

        $evidencias     = $this->buildQuery($request, $modulo)->paginate(15)->withQueryString();
        $actividades    = $this->actividadesDelUsuario($user, $modulo);
        $actividadPresel = $request->boolean('nueva') ? $request->input('actividad_id') : null;

        // Ejes/Etapas para filtro de estructura
        $sciEjes         = SciEje::where('activo', true)->orderBy('anio','desc')->orderBy('orden')->get();
        $integridadEtapas = IntegridadEtapa::where('activo', true)->orderBy('anio','desc')->orderBy('orden')->get();

        return view('content.evidencias.index', compact(
            'stats', 'evidencias', 'actividades',
            'sciEjes', 'integridadEtapas',
            'actividadPresel', 'modulo'
        ));
    }

    // ── JSON para AJAX ────────────────────────────────────────────────────────
    private function jsonResponse(Request $request)
    {
        $modulo = $request->input('modulo', 'sci');
        $evidencias = $this->buildQuery($request, $modulo)->get();

        return response()->json([
            'total' => $evidencias->count(),
            'items' => $evidencias->map(fn($ev) => [
                'id'             => $ev->id,
                'titulo'         => $ev->titulo,
                'numero_sgd'     => $ev->numero_sgd,
                'descripcion'    => $ev->descripcion ? Str::limit($ev->descripcion, 50) : null,
                'actividad'      => $ev->actividad?->nombre ? Str::limit($ev->actividad->nombre, 45) : '—',
                'codigo'         => $ev->actividad?->codigo,
                'componente'     => $ev->actividad?->modulo === 'integridad'
                    ? $ev->actividad?->integridadPregunta?->componente?->nombre
                    : $ev->actividad?->sciPregunta?->componente?->nombre,
                'modulo'         => $ev->actividad?->modulo ?? $modulo,
                'subido_por'     => $ev->subidoPor?->name ?? '—',
                'validado_por'   => $ev->validadoPor?->name,
                'estado'         => $ev->estado,
                'url_documento'  => $ev->url_documento,
                'url_host'       => $ev->url_documento ? (parse_url($ev->url_documento, PHP_URL_HOST) ?: $ev->url_documento) : null,
                'fecha'          => $ev->created_at->format('d/m/Y'),
                'motivo_rechazo' => $ev->motivo_rechazo ? Str::limit($ev->motivo_rechazo, 30) : null,
                'es_propio'      => $ev->subido_por === Auth::id(),
                'url_editar'     => route('sci-evidencias.update', $ev),
                'url_validar'    => route('sci-evidencias.validar', $ev),
                'url_eliminar'   => route('sci-evidencias.destroy', $ev),
            ]),
        ]);
    }

    // ── Query builder compartido ──────────────────────────────────────────────
    private function buildQuery(Request $request, string $modulo)
    {
        $user = Auth::user();

        $query = Evidencia::with([
                'actividad.sciPregunta.componente',
                'actividad.integridadPregunta.componente',
                'subidoPor',
                'validadoPor',
            ])
            ->whereHas('actividad', fn($q) => $q->where('modulo', $modulo)->visiblesParaUsuario($user))
            ->orderByDesc('created_at');

        if ($request->filled('actividad_id')) {
            $query->where('actividad_id', $request->actividad_id);
        }
        if ($request->filled('eje_id') && $modulo === 'sci') {
            $query->whereHas('actividad.sciPregunta.componente', fn($q) => $q->where('sci_eje_id', $request->eje_id));
        }
        if ($request->filled('etapa_id') && $modulo === 'integridad') {
            $query->whereHas('actividad.integridadPregunta.componente', fn($q) => $q->where('integridad_etapa_id', $request->etapa_id));
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(fn($q) => $q
                ->where('numero_sgd', 'like', "%$b%")
                ->orWhere('titulo', 'like', "%$b%")
            );
        }

        return $query;
    }

    private function countByModulo(string $modulo, ?string $estado = null)
    {
        $user = Auth::user();
        $q = Evidencia::whereHas('actividad', fn($q) => $q->where('modulo', $modulo)->visiblesParaUsuario($user));
        if ($estado) $q->where('estado', $estado);
        return $q->count();
    }

    private function actividadesDelUsuario($user, string $modulo)
    {
        return Actividad::whereHas('responsables', fn($q) => $q->where('users.id', $user->id))
            ->where('modulo', $modulo)
            ->orderBy('codigo')
            ->get(['id', 'codigo', 'nombre', 'estado', 'modulo']);
    }

    // ── CRUD ──────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'actividad_id'  => [
                'required', 'exists:actividades,id',
                fn($_attr, $val, $fail) => Actividad::whereHas('responsables', fn($q) => $q->where('users.id', $user->id))
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
        abort_unless(
            $evidencia->subido_por === $user->id && $evidencia->estado === 'pendiente',
            403, 'Solo puedes editar evidencias pendientes que registraste tú.'
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
        $data = $request->validate([
            'accion'         => 'required|in:validado,rechazado',
            'motivo_rechazo' => 'required_if:accion,rechazado|nullable|string',
        ]);

        $accion = $data['accion'];
        $motivo = $data['motivo_rechazo'] ?? null;

        $evidencia->update([
            'estado'         => $accion,
            'validado_por'   => Auth::id(),
            'validado_at'    => now(),
            'motivo_rechazo' => $accion === 'rechazado' ? $motivo : null,
        ]);

        // Notificar al usuario que subió la evidencia (si es distinto al validador)
        if ($evidencia->subido_por && $evidencia->subido_por !== Auth::id()) {
            $evidencia->subidoPor->notify(new EvidenciaRevisada($evidencia, $accion, $motivo));
        }

        $msg = $accion === 'validado' ? 'Evidencia validada correctamente.' : 'Evidencia rechazada. El usuario ha sido notificado.';

        return back()->with('success', $msg);
    }

    public function destroy(Evidencia $evidencia)
    {
        $evidencia->delete();
        return back()->with('success', 'Evidencia eliminada.');
    }
}
