<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Alerta;
use App\Models\User;
use App\Services\AlertaService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class AlertasController extends Controller
{
    /**
     * Aplica el scope de visibilidad basado en permisos:
     *  - alertas.eliminar → gestión total, ve todas
     *  - alertas.crear + unidad_organica_id → ve su unidad + las suyas
     *  - solo alertas.ver → solo las suyas personales
     */
    private function scopeVisibilidad(Builder $query): Builder
    {
        $user = auth()->user();

        // Gestores (pueden eliminar) → visión total
        if ($user->can('alertas.eliminar')) {
            return $query;
        }

        // Puede crear y tiene unidad asignada → ve su unidad + las suyas
        if ($user->can('alertas.crear') && !empty($user->unidad_organica_id)) {
            $unidadId = $user->unidad_organica_id;
            return $query->where(function (Builder $q) use ($user, $unidadId) {
                $q->where('usuario_id', $user->id)
                  ->orWhere('unidad_organica_id', $unidadId);
            });
        }

        // Solo lectura → únicamente las propias
        return $query->where('usuario_id', $user->id);
    }

    public function index(Request $request)
    {
        $tab      = $request->input('tab', 'pendientes');
        $modulo   = $request->input('modulo');
        $prioridad = $request->input('prioridad');
        $tipo      = $request->input('tipo');

        // Stats con scope de visibilidad
        $base = fn() => $this->scopeVisibilidad(Alerta::query());

        $stats = [
            'total'      => $base()->count(),
            'pendientes' => $base()->where('leida', false)->count(),
            'resueltas'  => $base()->where('leida', true)->count(),
            'alta'       => $base()->where('leida', false)->where('prioridad', 'alta')->count(),
            'media'      => $base()->where('leida', false)->where('prioridad', 'media')->count(),
            'baja'       => $base()->where('leida', false)->where('prioridad', 'baja')->count(),
            'sci'        => $base()->where('leida', false)->where('modulo', 'sci')->count(),
            'integridad' => $base()->where('leida', false)->where('modulo', 'integridad')->count(),
        ];

        $query = $this->scopeVisibilidad(
            Alerta::with(['actividad.responsables', 'unidadOrganica'])
        )
        ->orderByDesc('created_at')
        ->orderByRaw("FIELD(prioridad,'alta','media','baja')");

        if ($tab === 'resueltas') {
            $query->where('leida', true);
        } else {
            $query->where('leida', false);
        }

        if ($modulo)    $query->where('modulo', $modulo);
        if ($prioridad) $query->where('prioridad', $prioridad);
        if ($tipo)      $query->where('tipo', $tipo);

        $alertas = $query->paginate(15)->withQueryString();

        $user = auth()->user();

        // Admins son quienes pueden eliminar alertas (permiso elevado)
        $esAdmin = $user->can('alertas.eliminar');

        // Puede ver alertas de su unidad (tiene unidad asignada, puede crear pero no eliminar)
        $esResponsableUnidad = !$esAdmin
            && $user->can('alertas.crear')
            && !empty($user->unidad_organica_id);

        // Gestores totales (eliminar): ven todos los usuarios
        // Responsable de Unidad: solo ve usuarios de su unidad
        if ($esAdmin) {
            $usuarios = User::where('estado', 'activo')->orderBy('name')->get(['id', 'name', 'email', 'unidad_organica_id']);
        } elseif ($esResponsableUnidad) {
            $usuarios = User::where('estado', 'activo')
                ->where('unidad_organica_id', $user->unidad_organica_id)
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'unidad_organica_id']);
        } else {
            $usuarios = collect();
        }

        // Unidades para select (solo gestores totales)
        $unidades = $esAdmin
            ? \App\Models\UnidadOrganica::where('activo', true)->orderBy('nombre')->get(['id', 'nombre', 'sigla'])
            : collect();

        return view('content.alertas.index', compact(
            'stats', 'alertas', 'tab', 'modulo', 'prioridad', 'tipo',
            'usuarios', 'unidades', 'esAdmin', 'esResponsableUnidad'
        ));
    }

    public function store(Request $request)
    {
        $esGestor = auth()->user()->can('alertas.eliminar');

        $validated = $request->validate([
            'titulo'             => 'required|string|max:255',
            'mensaje'            => 'required|string',
            'prioridad'          => 'required|in:alta,media,baja',
            'tipo'               => 'required|in:vencimiento,vencimiento_proximo,avance_bajo,evidencia_falta,sistema',
            'modulo'             => 'required|in:sci,integridad',
            'tipo_destino'       => 'required|in:individual,unidad,todos',
            'actividad_id'       => 'nullable|exists:actividades,id',
            'unidad_organica_id' => 'required_if:tipo_destino,unidad|nullable|exists:unidades_organicas,id',
            'usuario_id'         => 'nullable|exists:users,id',
            'enviar_email'       => 'nullable|boolean',
        ]);

        // No-gestores: solo pueden crear alertas individuales para sí mismos
        if (!$esGestor) {
            $validated['tipo_destino']  = 'individual';
            $validated['usuario_id']    = auth()->id();
            unset($validated['unidad_organica_id']);
        }

        // Individual sin usuario elegido → asignar al creador
        if ($validated['tipo_destino'] === 'individual' && empty($validated['usuario_id'])) {
            $validated['usuario_id'] = auth()->id();
        }

        // "Todos" → no tiene usuario_id específico
        if ($validated['tipo_destino'] === 'todos') {
            $validated['usuario_id'] = null;
        }

        try {
            $alerta = Alerta::create($validated);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            $msg = 'Ya existe una alerta pendiente de ese tipo para la actividad seleccionada. Resuélvela antes de crear una nueva.';
            if ($request->ajax()) {
                return response()->json(['ok' => false, 'message' => $msg], 422);
            }
            return back()->withInput()->with('error', $msg);
        }

        $mensaje = 'Alerta creada correctamente.';

        if ($request->boolean('enviar_email')) {
            try {
                $service = app(AlertaService::class);
                if ($alerta->tipo_destino === 'individual') {
                    $service->enviarEmailManual($alerta);
                    $alerta->refresh();
                    $dest = $alerta->destinatario_email ?? 'destinatario';
                    $mensaje = "Alerta creada y email enviado a: {$dest}";
                } else {
                    $result = $service->enviarEmailGrupo($alerta);
                    $mensaje = "Alerta creada. Emails enviados: {$result['enviados']}"
                        . ($result['fallidos'] > 0 ? " ({$result['fallidos']} fallidos)" : '');
                }
            } catch (\Throwable $e) {
                $mensaje = 'Alerta creada, pero no se pudo enviar el email: ' . $e->getMessage();
            }
        }

        if ($request->ajax()) {
            return response()->json(['ok' => true, 'message' => $mensaje]);
        }
        return back()->with('success', $mensaje);
    }

    public function update(Request $request, Alerta $alerta)
    {
        $this->autorizarAcceso($alerta);

        $validated = $request->validate([
            'titulo'             => 'required|string|max:255',
            'mensaje'            => 'required|string',
            'prioridad'          => 'required|in:alta,media,baja',
            'tipo'               => 'required|in:vencimiento,vencimiento_proximo,avance_bajo,evidencia_falta,sistema',
            'modulo'             => 'required|in:sci,integridad',
            'tipo_destino'       => 'nullable|in:individual,unidad,todos',
            'usuario_id'         => 'nullable|exists:users,id',
            'unidad_organica_id' => 'nullable|exists:unidades_organicas,id',
            'actividad_id'       => 'nullable|exists:actividades,id',
        ]);

        // No-gestores no pueden cambiar destinatario ni tipo_destino
        if (!auth()->user()->can('alertas.eliminar')) {
            $validated['usuario_id']   = $alerta->usuario_id;
            $validated['tipo_destino'] = $alerta->tipo_destino;
        }

        // "Todos" → limpiar usuario_id
        if (($validated['tipo_destino'] ?? $alerta->tipo_destino) === 'todos') {
            $validated['usuario_id'] = null;
        }

        $alerta->update($validated);

        if (request()->ajax()) {
            return response()->json(['ok' => true, 'message' => 'Alerta actualizada correctamente.']);
        }
        return back()->with('success', 'Alerta actualizada correctamente.');
    }

    public function marcarLeida(Alerta $alerta)
    {
        $this->autorizarAcceso($alerta);
        $alerta->update(['leida' => true, 'leida_at' => now()]);
        if (request()->ajax()) {
            return response()->json(['ok' => true, 'message' => 'Alerta marcada como leída.']);
        }
        return back()->with('success', 'Alerta marcada como leída.');
    }

    public function marcarTodasLeidas(Request $request)
    {
        $query = $this->scopeVisibilidad(Alerta::query())->where('leida', false);
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }
        $query->update(['leida' => true, 'leida_at' => now()]);
        if ($request->ajax()) {
            return response()->json(['ok' => true, 'message' => 'Alertas marcadas como leídas.']);
        }
        return back()->with('success', 'Alertas marcadas como leídas.');
    }

    public function enviarEmail(Alerta $alerta, AlertaService $service)
    {
        $this->autorizarAcceso($alerta);
        try {
            if ($alerta->tipo_destino !== 'individual') {
                $result = $service->enviarEmailGrupo($alerta);
                $msg = "Emails enviados: {$result['enviados']}"
                     . ($result['fallidos'] > 0 ? " ({$result['fallidos']} fallidos)" : '');
                if (request()->ajax()) {
                    return response()->json(['ok' => true, 'message' => $msg]);
                }
                return back()->with('success', $msg);
            }

            $service->enviarEmailManual($alerta);
            if (request()->ajax()) {
                return response()->json(['ok' => true, 'message' => 'Email enviado correctamente.']);
            }
            return back()->with('success', 'Email de alerta enviado correctamente.');
        } catch (\Throwable $e) {
            $raw = $e->getMessage();
            $mensaje = match(true) {
                str_contains($raw, '550') || str_contains($raw, 'No Such User')
                    => 'El correo del destinatario no existe en el servidor de correo ('
                       . ($alerta->load('usuario')->usuario?->email ?? 'sin email asignado')
                       . '). Verifica que el email del usuario sea válido.',
                str_contains($raw, '535') || str_contains($raw, 'Authentication')
                    => 'Error de autenticación SMTP. Ve a Configuración → Correo.',
                str_contains($raw, '421') || str_contains($raw, '450') || str_contains($raw, 'Connection')
                    => 'No se pudo conectar al servidor SMTP. Verifica host y puerto en Configuración.',
                str_contains($raw, 'No hay destinatario') || str_contains($raw, 'destinatarios')
                    => 'No se encontraron destinatarios con email válido para este grupo.',
                default
                    => 'Error al enviar el email: ' . $raw,
            };
            if (request()->ajax()) {
                return response()->json(['ok' => false, 'message' => $mensaje], 422);
            }
            return back()->with('error', $mensaje);
        }
    }

    public function destroy(Alerta $alerta)
    {
        $this->autorizarAcceso($alerta);
        $alerta->delete();
        if (request()->ajax()) {
            return response()->json(['ok' => true, 'message' => 'Alerta eliminada.']);
        }
        return back()->with('success', 'Alerta eliminada.');
    }

    /**
     * Endpoint JSON para polling en tiempo real.
     * Devuelve stats + filas HTML de la tabla según los filtros activos.
     */
    public function poll(Request $request)
    {
        $tab       = $request->input('tab', 'pendientes');
        $modulo    = $request->input('modulo');
        $prioridad = $request->input('prioridad');
        $tipo      = $request->input('tipo');

        $base = fn() => $this->scopeVisibilidad(Alerta::query());

        $stats = [
            'total'      => $base()->count(),
            'pendientes' => $base()->where('leida', false)->count(),
            'resueltas'  => $base()->where('leida', true)->count(),
            'alta'       => $base()->where('leida', false)->where('prioridad', 'alta')->count(),
            'media'      => $base()->where('leida', false)->where('prioridad', 'media')->count(),
            'baja'       => $base()->where('leida', false)->where('prioridad', 'baja')->count(),
        ];

        $query = $this->scopeVisibilidad(
            Alerta::with(['actividad.responsables', 'unidadOrganica'])
        )
        ->orderByDesc('created_at')
        ->orderByRaw("FIELD(prioridad,'alta','media','baja')");

        if ($tab === 'resueltas') {
            $query->where('leida', true);
        } else {
            $query->where('leida', false);
        }

        if ($modulo)    $query->where('modulo', $modulo);
        if ($prioridad) $query->where('prioridad', $prioridad);
        if ($tipo)      $query->where('tipo', $tipo);

        $alertas = $query->paginate(15)->withQueryString();
        $esAdmin = auth()->user()->can('alertas.eliminar');

        $html = view('content.alertas._filas', compact('alertas', 'tab', 'esAdmin'))->render();

        $paginacionHtml = $alertas->hasPages()
            ? $alertas->links()->toHtml()
            : '';

        $totalTexto = 'Mostrando <strong>'
            . $alertas->firstItem() . '–' . $alertas->lastItem()
            . '</strong> de <strong>' . $alertas->total() . '</strong> '
            . ($alertas->total() === 1 ? 'alerta' : 'alertas');

        return response()->json([
            'stats'          => $stats,
            'html'           => $html,
            'total_texto'    => $alertas->total() . ($alertas->total() === 1 ? ' alerta' : ' alertas'),
            'has_pages'      => $alertas->hasPages(),
            'paginacion_html'=> $paginacionHtml,
            'footer_html'    => $alertas->hasPages() ? $totalTexto : ($alertas->total() . ($alertas->total() === 1 ? ' alerta' : ' alertas')),
        ]);
    }

    /**
     * Retorna actividades filtradas por módulo + destinatario para el modal de alertas.
     * Acepta: modulo, usuario_id (individual), unidad_id (por unidad).
     */
    public function actividadesPorModulo(Request $request)
    {
        $modulo    = $request->input('modulo');
        $usuarioId = $request->input('usuario_id');
        $unidadId  = $request->input('unidad_id');

        if (!in_array($modulo, ['sci', 'integridad'])) {
            return response()->json([]);
        }

        $query = Actividad::where('modulo', $modulo)
            ->where('anio', now()->year)
            ->orderBy('codigo');

        if ($usuarioId) {
            // Individual: solo actividades asignadas a ese usuario
            $query->whereHas('responsables', fn($q) => $q->where('users.id', $usuarioId));
        } elseif ($unidadId) {
            // Por unidad: actividades de esa unidad
            $query->where('unidad_organica_id', $unidadId);
        }
        // Sin filtro de destino (tipo=todos): devuelve todas del módulo

        $actividades = $query->get(['id', 'codigo', 'nombre', 'estado', 'avance', 'fecha_limite']);

        return response()->json($actividades->map(fn($a) => [
            'id'          => $a->id,
            'label'       => "[{$a->codigo}] {$a->nombre}",
            'estado'      => $a->estado,
            'avance'      => $a->avance,
            'fecha_limite'=> $a->fecha_limite?->format('d/m/Y'),
            'nombre'      => $a->nombre,
            'codigo'      => $a->codigo,
        ]));
    }

    /**
     * Verifica acceso a una alerta específica basado en permisos.
     * Los gestores (alertas.eliminar) acceden a todo.
     * Otros solo a sus propias alertas o las de su unidad (si tienen unidad asignada).
     */
    private function autorizarAcceso(Alerta $alerta): void
    {
        $user = auth()->user();

        if ($user->can('alertas.eliminar')) {
            return;
        }

        if ($user->can('alertas.crear') && !empty($user->unidad_organica_id)) {
            $unidadId = $user->unidad_organica_id;
            if ($alerta->usuario_id === $user->id || $alerta->unidad_organica_id === $unidadId) {
                return;
            }
        }

        if ($alerta->usuario_id === $user->id) {
            return;
        }

        abort(403, 'No tienes permiso para operar sobre esta alerta.');
    }
}
