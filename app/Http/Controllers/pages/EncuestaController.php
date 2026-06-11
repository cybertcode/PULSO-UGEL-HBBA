<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\Encuesta;
use App\Models\EncuestaDestinatario;
use App\Models\EncuestaOpcion;
use App\Models\EncuestaPregunta;
use App\Models\EncuestaRespuesta;
use App\Models\Role;
use App\Models\UnidadOrganica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EncuestaController extends Controller
{
    public function index()
    {
        $stats = [
            'total'      => Encuesta::count(),
            'borrador'   => Encuesta::where('estado', 'borrador')->count(),
            'publicadas' => Encuesta::where('estado', 'publicada')->count(),
            'cerradas'   => Encuesta::where('estado', 'cerrada')->count(),
            'mis_pendientes' => EncuestaRespuesta::where('usuario_id', Auth::id())
                ->where('completada', false)
                ->whereHas('encuesta', fn($q) => $q->where('estado', 'publicada'))
                ->count(),
        ];

        return view('content.encuestas.index', compact('stats'));
    }

    public function data(Request $request)
    {
        $query = Encuesta::with('creador')
            ->withCount('respuestas')
            ->withCount(['respuestas as completadas_count' => fn($q) => $q->where('completada', true)]);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }
        if ($request->filled('buscar')) {
            $query->where('titulo', 'like', '%' . $request->buscar . '%');
        }

        $encuestas = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return response()->json($encuestas);
    }

    public function create()
    {
        $unidades = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
        $roles    = \Spatie\Permission\Models\Role::orderBy('name')->get();
        $usuarios = User::where('estado', 'activo')->orderBy('name')->get();

        return view('content.encuestas.create', compact('unidades', 'roles', 'usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'       => 'required|string|max:255',
            'modulo'       => 'required|in:sci,integridad,ambos',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'preguntas'    => 'required|array|min:1',
            'preguntas.*.texto' => 'required|string',
            'preguntas.*.tipo'  => 'required|in:opcion_multiple,seleccion_multiple,escala,texto_libre',
        ]);

        DB::transaction(function () use ($request) {
            $encuesta = Encuesta::create([
                'titulo'      => $request->titulo,
                'descripcion' => $request->descripcion,
                'modulo'      => $request->modulo,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin'   => $request->fecha_fin,
                'creado_por'  => Auth::id(),
                'estado'      => 'borrador',
            ]);

            foreach ($request->preguntas as $i => $preguntaData) {
                $pregunta = $encuesta->preguntas()->create([
                    'orden'    => $i + 1,
                    'texto'    => $preguntaData['texto'],
                    'tipo'     => $preguntaData['tipo'],
                    'requerida' => ($preguntaData['requerida'] ?? '1') === '1',
                ]);

                if (in_array($pregunta->tipo, ['opcion_multiple', 'seleccion_multiple'])) {
                    foreach (($preguntaData['opciones'] ?? []) as $j => $opcionTexto) {
                        if (trim($opcionTexto)) {
                            $pregunta->opciones()->create(['orden' => $j + 1, 'texto' => $opcionTexto]);
                        }
                    }
                }
            }

            $this->guardarDestinatarios($encuesta, $request);

            session()->flash('success', 'Encuesta creada correctamente.');
            session()->flash('encuesta_id', $encuesta->id);
        });

        return redirect()->route('encuestas.index')
            ->with('success', 'Encuesta creada como borrador. Puedes publicarla cuando esté lista.');
    }

    public function edit(Encuesta $encuesta)
    {
        abort_if($encuesta->estado !== 'borrador', 403, 'Solo se pueden editar encuestas en borrador.');

        $encuesta->load(['preguntas.opciones', 'destinatarios']);
        $unidades = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
        $roles    = \Spatie\Permission\Models\Role::orderBy('name')->get();
        $usuarios = User::where('estado', 'activo')->orderBy('name')->get();

        return view('content.encuestas.edit', compact('encuesta', 'unidades', 'roles', 'usuarios'));
    }

    public function update(Request $request, Encuesta $encuesta)
    {
        abort_if($encuesta->estado !== 'borrador', 403, 'Solo se pueden editar encuestas en borrador.');

        $request->validate([
            'titulo'       => 'required|string|max:255',
            'modulo'       => 'required|in:sci,integridad,ambos',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'preguntas'    => 'required|array|min:1',
            'preguntas.*.texto' => 'required|string',
            'preguntas.*.tipo'  => 'required|in:opcion_multiple,seleccion_multiple,escala,texto_libre',
        ]);

        DB::transaction(function () use ($request, $encuesta) {
            $encuesta->update([
                'titulo'      => $request->titulo,
                'descripcion' => $request->descripcion,
                'modulo'      => $request->modulo,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin'   => $request->fecha_fin,
            ]);

            // Reconstruir preguntas
            $encuesta->preguntas()->each(fn($p) => $p->opciones()->delete());
            $encuesta->preguntas()->delete();

            foreach ($request->preguntas as $i => $preguntaData) {
                $pregunta = $encuesta->preguntas()->create([
                    'orden'    => $i + 1,
                    'texto'    => $preguntaData['texto'],
                    'tipo'     => $preguntaData['tipo'],
                    'requerida' => ($preguntaData['requerida'] ?? '1') === '1',
                ]);

                if (in_array($pregunta->tipo, ['opcion_multiple', 'seleccion_multiple'])) {
                    foreach (($preguntaData['opciones'] ?? []) as $j => $opcionTexto) {
                        if (trim($opcionTexto)) {
                            $pregunta->opciones()->create(['orden' => $j + 1, 'texto' => $opcionTexto]);
                        }
                    }
                }
            }

            $encuesta->destinatarios()->delete();
            $this->guardarDestinatarios($encuesta, $request);
        });

        return redirect()->route('encuestas.index')
            ->with('success', 'Encuesta actualizada correctamente.');
    }

    public function destroy(Encuesta $encuesta)
    {
        $encuesta->delete();
        return redirect()->route('encuestas.index')
            ->with('success', 'Encuesta eliminada.');
    }

    public function publicar(Request $request, Encuesta $encuesta)
    {
        abort_if($encuesta->estado !== 'borrador', 403, 'Solo se pueden publicar encuestas en borrador.');

        $encuesta->load('destinatarios');
        $userIds = $encuesta->resolverDestinatarios();

        if ($userIds->isEmpty()) {
            return back()->with('error', 'No hay destinatarios definidos para esta encuesta.');
        }

        DB::transaction(function () use ($encuesta, $userIds) {
            $encuesta->update([
                'estado'       => 'publicada',
                'published_at' => now(),
            ]);

            // Crear encuesta_respuestas para cada destinatario
            foreach ($userIds as $userId) {
                EncuestaRespuesta::firstOrCreate([
                    'encuesta_id' => $encuesta->id,
                    'usuario_id'  => $userId,
                ], [
                    'completada' => false,
                ]);
            }

            // Crear alertas en el sistema existente
            $usuarios = User::whereIn('id', $userIds)->get();
            foreach ($usuarios as $usuario) {
                Alerta::create([
                    'actividad_id'      => null,
                    'usuario_id'        => $usuario->id,
                    'unidad_organica_id' => $usuario->unidad_organica_id,
                    'modulo'            => 'encuestas',
                    'titulo'            => 'Nueva encuesta: ' . $encuesta->titulo,
                    'mensaje'           => 'Tienes una encuesta pendiente de responder.'
                        . ($encuesta->fecha_fin ? ' Fecha límite: ' . $encuesta->fecha_fin->format('d/m/Y') : ''),
                    'tipo'              => 'sistema',
                    'prioridad'         => 'media',
                    'leida'             => false,
                    'email_enviado'     => false,
                ]);
            }
        });

        return redirect()->route('encuestas.index')
            ->with('success', 'Encuesta publicada. Se notificó a ' . $userIds->count() . ' usuario(s).');
    }

    public function cerrar(Encuesta $encuesta)
    {
        abort_if($encuesta->estado !== 'publicada', 403);
        $encuesta->update(['estado' => 'cerrada']);
        return redirect()->route('encuestas.index')->with('success', 'Encuesta cerrada.');
    }

    private function guardarDestinatarios(Encuesta $encuesta, Request $request): void
    {
        $destinatarios = $request->input('destinatarios', []);

        foreach ($destinatarios as $dest) {
            $tipo = $dest['tipo'] ?? null;
            if (!$tipo) continue;

            if ($tipo === 'todos') {
                $encuesta->destinatarios()->create(['tipo' => 'todos', 'referencia_id' => null]);
                return; // si es "todos", no hace falta agregar más
            }

            $ids = $dest['ids'] ?? [];
            foreach ((array)$ids as $id) {
                $encuesta->destinatarios()->create([
                    'tipo'         => $tipo,
                    'referencia_id' => $id,
                ]);
            }
        }
    }
}
