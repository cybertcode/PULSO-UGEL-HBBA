<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Evidencia;
use App\Models\Actividad;
use App\Models\ActividadHistorial;
use App\Models\SciEje;
use App\Models\IntegridadEtapa;
use Illuminate\Http\Request;
use App\Notifications\EvidenciaRevisada;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
        // Cargar actividades de ambos módulos para el modal y el mapa JS
        $actividades    = $this->actividadesDelUsuario($user, null);
        $actividadPresel = $request->boolean('nueva') ? $request->input('actividad_id') : null;

        // Actividades en observado (ambos módulos) sin evidencia pendiente/rechazada activa
        // → el usuario debe subir evidencia nueva pero no hay fila visible en la tabla
        $todasObservadas = $actividades
            ->where('estado', 'observado')
            ->filter(fn($a) =>
                $a->evidencias->where('estado', 'pendiente')->count() === 0 &&
                $a->evidencias->where('estado', 'rechazado')->count() === 0
            );
        $actividadesObservadas = [
            'sci'        => $todasObservadas->where('modulo', 'sci')->values(),
            'integridad' => $todasObservadas->where('modulo', 'integridad')->values(),
        ];

        // Ejes/Etapas para filtro de estructura
        $sciEjes         = SciEje::where('activo', true)->orderBy('anio','desc')->orderBy('orden')->get();
        $integridadEtapas = IntegridadEtapa::where('activo', true)->orderBy('anio','desc')->orderBy('orden')->get();

        return view('content.evidencias.index', compact(
            'stats', 'evidencias', 'actividades',
            'sciEjes', 'integridadEtapas',
            'actividadPresel', 'modulo', 'actividadesObservadas'
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
                'motivo_rechazo' => $ev->motivo_rechazo ?? null,
                'motivo_corto'   => $ev->motivo_rechazo ? Str::limit($ev->motivo_rechazo, 40) : null,
                'es_propio'      => $ev->subido_por === Auth::id(),
                'es_responsable' => $ev->actividad?->responsables?->contains('id', Auth::id()) ?? false,
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
                'actividad.responsables',
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

    private function actividadesDelUsuario($user, ?string $modulo = null)
    {
        $q = Actividad::with(['evidencias' => fn($q) => $q->select('id', 'actividad_id', 'estado')])
            ->whereHas('responsables', fn($q) => $q->where('users.id', $user->id))
            ->orderBy('modulo')
            ->orderBy('codigo');
        if ($modulo) {
            $q->where('modulo', $modulo);
        }
        return $q->get(['id', 'codigo', 'nombre', 'estado', 'modulo']);
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

        $actividad = Actividad::find($request->actividad_id);

        // Bloquear si la actividad ya está completada
        if ($actividad && $actividad->estado === 'completada') {
            return back()->withErrors(['actividad_id' => 'Esta actividad ya está completada. No se pueden agregar más evidencias.']);
        }

        // Bloquear si ya hay una evidencia pendiente (en espera de revisión)
        $evPendiente = $actividad?->evidencias()->where('estado', 'pendiente')->first();
        if ($evPendiente) {
            return back()->withErrors(['actividad_id' => 'Ya existe una evidencia en revisión para esta actividad. Espera la validación antes de enviar otra.']);
        }

        // Si hay evidencia rechazada: actualizar la misma en vez de crear un duplicado
        $evRechazada = $actividad?->evidencias()->where('estado', 'rechazado')->first();
        if ($evRechazada) {
            $evRechazada->update([
                'titulo'         => $request->titulo,
                'numero_sgd'     => $request->numero_sgd,
                'descripcion'    => $request->descripcion,
                'url_documento'  => $request->url_documento ?: null,
                'estado'         => 'pendiente',
                'motivo_rechazo' => null,
                'validado_por'   => null,
                'validado_at'    => null,
            ]);
            if ($actividad->estado === 'observado') {
                $actividad->update(['estado' => 'en_proceso']);
            }

            ActividadHistorial::create([
                'actividad_id'   => $actividad->id,
                'usuario_id'     => $user->id,
                'campo'          => 'evidencia',
                'valor_anterior' => 'rechazado',
                'valor_nuevo'    => 'pendiente',
                'descripcion'    => 'Evidencia corregida y reenviada: "' . $evRechazada->titulo . '"' . ($evRechazada->numero_sgd ? ' (SGD: ' . $evRechazada->numero_sgd . ')' : ''),
            ]);

            return redirect()->route('sci-evidencias', [
                'modulo'       => $actividad->modulo ?? 'sci',
                'actividad_id' => $request->actividad_id,
            ])->with('success', 'Evidencia corregida y reenviada para revisión.');
        }

        $nuevaEv = Evidencia::create([
            'actividad_id'  => $request->actividad_id,
            'subido_por'    => $user->id,
            'titulo'        => $request->titulo,
            'numero_sgd'    => $request->numero_sgd,
            'descripcion'   => $request->descripcion,
            'url_documento' => $request->url_documento ?: null,
            'estado'        => 'pendiente',
        ]);

        $actividad = Actividad::find($request->actividad_id);

        ActividadHistorial::create([
            'actividad_id'   => $actividad->id,
            'usuario_id'     => $user->id,
            'campo'          => 'evidencia',
            'valor_anterior' => null,
            'valor_nuevo'    => 'pendiente',
            'descripcion'    => 'Evidencia enviada: "' . $nuevaEv->titulo . '"' . ($nuevaEv->numero_sgd ? ' (SGD: ' . $nuevaEv->numero_sgd . ')' : ''),
        ]);
        return redirect()->route('sci-evidencias', [
            'modulo'      => $actividad->modulo ?? 'sci',
            'actividad_id'=> $request->actividad_id,
        ])->with('success', 'Evidencia registrada correctamente. Pendiente de validación.');
    }

    public function update(Request $request, Evidencia $evidencia)
    {
        $user = Auth::user();
        // Pendiente: solo quien la subió puede editar
        // Rechazado: cualquier responsable de la actividad puede corregir y reenviar
        $esAutor        = $evidencia->subido_por === $user->id;
        $esResponsable  = $evidencia->actividad?->responsables()->where('users.id', $user->id)->exists();
        $puedeEditar    = match($evidencia->estado) {
            'pendiente' => $esAutor,
            'rechazado' => $esAutor || $esResponsable,
            default     => false,
        };
        abort_unless($puedeEditar, 403, 'No tienes permiso para editar esta evidencia.');

        $request->validate([
            'titulo'        => 'required|string|max:255',
            'numero_sgd'    => 'nullable|string|max:50',
            'descripcion'   => 'nullable|string',
            'url_documento' => 'nullable|url|max:500',
        ]);

        $eraRechazada = $evidencia->estado === 'rechazado';

        $evidencia->update([
            'titulo'         => $request->titulo,
            'numero_sgd'     => $request->numero_sgd,
            'descripcion'    => $request->descripcion,
            'url_documento'  => $request->url_documento ?: null,
            // Si estaba rechazada, vuelve a pendiente y limpia el rechazo
            'estado'         => $eraRechazada ? 'pendiente' : $evidencia->estado,
            'motivo_rechazo' => $eraRechazada ? null : $evidencia->motivo_rechazo,
            'validado_por'   => $eraRechazada ? null : $evidencia->validado_por,
            'validado_at'    => $eraRechazada ? null : $evidencia->validado_at,
        ]);

        // Restaurar la actividad a en_proceso si estaba en observado
        if ($eraRechazada) {
            $actividad = $evidencia->actividad;
            if ($actividad && $actividad->estado === 'observado') {
                $actividad->update(['estado' => 'en_proceso']);
            }
        }

        $msg = $eraRechazada
            ? 'Evidencia corregida y reenviada para revisión.'
            : 'Evidencia actualizada correctamente.';

        return back()->with('success', $msg);
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

        $actividad    = $evidencia->actividad;
        $validadorId  = Auth::id();

        // ── Efecto sobre la actividad ─────────────────────────────────────────
        if ($accion === 'validado') {
            if ($actividad && $actividad->estado !== 'completada') {
                $actividad->update([
                    'estado'             => 'completada',
                    'avance'             => 100,
                    'fecha_cumplimiento' => now()->toDateString(),
                ]);
            }
            if ($actividad) {
                ActividadHistorial::create([
                    'actividad_id'   => $actividad->id,
                    'usuario_id'     => Auth::id(),
                    'campo'          => 'evidencia',
                    'valor_anterior' => 'pendiente',
                    'valor_nuevo'    => 'validado',
                    'descripcion'    => 'Evidencia validada: "' . $evidencia->titulo . '"' . ($evidencia->numero_sgd ? ' (SGD: ' . $evidencia->numero_sgd . ')' : ''),
                ]);
            }
        } elseif ($accion === 'rechazado') {
            if ($actividad && !in_array($actividad->estado, ['vencida'])) {
                $actividad->update(['estado' => 'observado']);
            }
            if ($actividad) {
                ActividadHistorial::create([
                    'actividad_id'   => $actividad->id,
                    'usuario_id'     => Auth::id(),
                    'campo'          => 'evidencia',
                    'valor_anterior' => 'pendiente',
                    'valor_nuevo'    => 'rechazado',
                    'descripcion'    => 'Evidencia rechazada: "' . $evidencia->titulo . '"' . ($evidencia->numero_sgd ? ' (SGD: ' . $evidencia->numero_sgd . ')' : '') . ($motivo ? ' — Motivo: ' . $motivo : ''),
                ]);
            }
        }

        // ── Notificar a todos los responsables (excepto al validador) ─────────
        if ($actividad) {
            $responsables = $actividad->responsables;
            // Incluir también al que subió la evidencia por si no es responsable
            $notificar = $responsables->pluck('id')
                ->push($evidencia->subido_por)
                ->unique()
                ->filter(fn($id) => $id && $id !== $validadorId);

            $usuarios = \App\Models\User::whereIn('id', $notificar)->get();
            foreach ($usuarios as $usuario) {
                $usuario->notify(new EvidenciaRevisada($evidencia, $accion, $motivo));
            }
        }

        $msg = $accion === 'validado'
            ? 'Evidencia validada. La actividad ha sido marcada como completada.'
            : 'Evidencia rechazada. La actividad volvió a estado Observado y los responsables fueron notificados.';

        return back()->with('success', $msg);
    }

    public function destroy(Evidencia $evidencia)
    {
        Gate::authorize('evidencias.eliminar');

        $actividad      = $evidencia->actividad;
        $estadoEvidencia = $evidencia->estado;
        $subidoPor      = $evidencia->subido_por;

        $evidencia->delete();

        // Si la evidencia eliminada era pendiente o rechazada, restaurar actividad a en_proceso
        // y notificar al responsable para que sepa que debe volver a subir evidencia
        if ($actividad && in_array($estadoEvidencia, ['pendiente', 'rechazado'])) {
            // Solo cambiar estado si no tiene otras evidencias válidas
            $tieneValidada = $actividad->evidencias()->where('estado', 'validado')->exists();
            if (!$tieneValidada && in_array($actividad->estado, ['en_proceso', 'observado', 'pendiente'])) {
                $actividad->update(['estado' => 'en_proceso']);
            }

            // Notificar a los responsables + quien subió la evidencia
            $notificar = $actividad->responsables->pluck('id')
                ->push($subidoPor)
                ->unique()
                ->filter(fn($id) => $id && $id !== Auth::id());

            $usuarios = \App\Models\User::whereIn('id', $notificar)->get();
            foreach ($usuarios as $usuario) {
                $usuario->notify(new EvidenciaRevisada(
                    $evidencia,
                    'eliminado',
                    'El validador eliminó la evidencia enviada. Debes volver a subir una nueva evidencia para esta actividad.'
                ));
            }
        }

        return back()->with('success', 'Evidencia eliminada. Los responsables han sido notificados.');
    }
}
