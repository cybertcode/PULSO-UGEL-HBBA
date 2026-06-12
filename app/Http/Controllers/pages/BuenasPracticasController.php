<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\BuenaPractica;
use App\Models\UnidadOrganica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BuenasPracticasController extends Controller
{
    private array $categorias = [
        'gestion'       => 'Gestión',
        'transparencia' => 'Transparencia',
        'integridad'    => 'Integridad',
        'innovacion'    => 'Innovación',
        'participacion' => 'Participación',
    ];

    private array $modulos = [
        'sci'        => 'Control Interno (SCI)',
        'integridad' => 'Modelo de Integridad',
    ];

    public function index(Request $request)
    {
        $esGestor = Gate::check('buenas-practicas.ver');
        $user     = Auth::user();

        $unidades = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();

        // Stats generales
        $stats = [
            'total_proyectos'     => BuenaPractica::whereIn('estado', BuenaPractica::ESTADOS_CONCURSO)->count(),
            'presentados'         => BuenaPractica::where('estado', 'presentado')->count(),
            'elegibles'           => BuenaPractica::where('estado', 'elegible')->count(),
            'ganadores_ugel'      => BuenaPractica::where('estado', 'ganador_ugel')->count(),
            'en_externo'          => BuenaPractica::whereIn('estado', ['participante_externo', 'ganador_externo'])->count(),
            'ganadores_externos'  => BuenaPractica::where('estado', 'ganador_externo')->count(),
            'mis_proyectos'       => BuenaPractica::where('propuesto_por', $user->id)->count(),
            'practicas_activas'   => BuenaPractica::whereIn('estado', BuenaPractica::ESTADOS_PRACTICA)->count(),
        ];

        // Pendientes de revisión por el gestor
        $pendientesRevision = $esGestor
            ? BuenaPractica::where('estado', 'presentado')->count()
            : 0;

        return view('content.buenas-practicas.index', compact(
            'stats', 'unidades', 'usuarios', 'pendientesRevision',
            'esGestor', 'user'
        ) + ['categorias' => $this->categorias, 'modulos' => $this->modulos]);
    }

    // AJAX: devuelve cards HTML paginadas
    public function data(Request $request)
    {
        $esGestor = Gate::check('buenas-practicas.ver');
        $user     = Auth::user();

        $tab      = $request->get('tab', 'concurso');
        $modulo   = $request->get('modulo', '');
        $estado   = $request->get('estado', '');
        $categoria= $request->get('categoria', '');
        $unidad   = $request->get('unidad', '');
        $buscar   = $request->get('buscar', '');

        $query = BuenaPractica::with(['unidadOrganica', 'responsable', 'propuestoPor', 'creadoPor'])
            ->orderByDesc('created_at');

        switch ($tab) {
            case 'presentados':
                // Gestor: proyectos nuevos por recepcionar
                $query->where('estado', 'presentado');
                if (!$esGestor) $query->whereRaw('1=0');
                break;

            case 'recepcionados':
                // Gestor: recepcionados pendientes de evaluar elegibilidad
                $query->where('estado', 'recepcionado');
                if (!$esGestor) $query->whereRaw('1=0');
                break;

            case 'elegibles':
                // Gestor: proyectos elegibles pendientes de declarar ganador UGEL
                $query->where('estado', 'elegible');
                if (!$esGestor) $query->whereRaw('1=0');
                break;

            case 'concurso_ugel':
                // Vista pública concurso interno UGEL: elegibles + ganadores UGEL
                $query->whereIn('estado', ['elegible', 'ganador_ugel', 'participante_externo', 'ganador_externo']);
                break;

            case 'concurso_externo':
                // Vista concurso externo MINEDU/DRE
                $query->whereIn('estado', ['participante_externo', 'ganador_externo']);
                break;

            case 'mis':
                // Usuario ve todos sus proyectos en cualquier estado del concurso
                $query->where('propuesto_por', $user->id)
                      ->whereIn('estado', BuenaPractica::ESTADOS_CONCURSO);
                break;

            case 'practicas':
                // Prácticas institucionales registradas por SCI
                $query->whereIn('estado', BuenaPractica::ESTADOS_PRACTICA);
                if ($estado) $query->where('estado', $estado);
                break;

            default:
                // Tab "concurso" por defecto: vista pública concurso UGEL
                $query->whereIn('estado', ['elegible', 'ganador_ugel', 'participante_externo', 'ganador_externo']);
                break;
        }

        if ($modulo) {
            $query->where('modulo', $modulo);
        }
        if ($categoria) {
            $query->where('categoria', $categoria);
        }
        if ($unidad) {
            $query->where('unidad_organica_id', $unidad);
        }
        if ($buscar) {
            $query->where(fn($q) => $q
                ->where('titulo', 'like', "%{$buscar}%")
                ->orWhere('descripcion', 'like', "%{$buscar}%")
            );
        }

        $practicas = $query->paginate(12)->withQueryString();

        return response()->json([
            'html'  => view('content.buenas-practicas._cards', compact('practicas', 'esGestor', 'tab'))->render(),
            'total' => $practicas->total(),
            'links' => (string) $practicas->links(),
        ]);
    }

    // Usuario presenta su proyecto al concurso
    public function proponer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo'            => 'required|string|max:255',
            'descripcion'       => 'required|string',
            'categoria'         => 'required|in:gestion,transparencia,integridad,innovacion,participacion',
            'modulo'            => 'required|in:sci,integridad',
            'unidad_organica_id'=> 'nullable|exists:unidades_organicas,id',
            'fecha_inicio'      => 'nullable|date',
            'fecha_termino'     => 'nullable|date|after_or_equal:fecha_inicio',
            'evidencias'        => 'nullable|string',
            'archivo_proyecto'  => 'nullable|file|mimes:pdf,doc,docx,zip,pptx|max:10240',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Corrija los errores del formulario.');
        }

        $archivoPath = null;
        if ($request->hasFile('archivo_proyecto')) {
            $archivoPath = $request->file('archivo_proyecto')
                ->store('buenas-practicas/proyectos', 'public');
        }

        BuenaPractica::create([
            'titulo'             => $request->titulo,
            'descripcion'        => $request->descripcion,
            'categoria'          => $request->categoria,
            'modulo'             => $request->modulo,
            'unidad_organica_id' => $request->unidad_organica_id,
            'fecha_inicio'       => $request->fecha_inicio,
            'fecha_termino'      => $request->fecha_termino,
            'evidencias'         => $request->evidencias,
            'archivo_proyecto'   => $archivoPath,
            'estado'             => 'presentado',
            'avance'             => 0,
            'propuesto_por'      => Auth::id(),
            'creado_por'         => Auth::id(),
        ]);

        return back()->with('success', '¡Proyecto presentado exitosamente! El Responsable SCI lo recepcionará y notificará el resultado.');
    }

    // SCI recepciona el documento del proyecto (confirma que lo recibió)
    public function recepcionar(Request $request, BuenaPractica $buenaPractica)
    {
        Gate::authorize('buenas-practicas.editar');

        $request->validate([
            'numero_expediente' => 'nullable|string|max:50',
            'fecha_recepcion'   => 'nullable|date',
            'observaciones'     => 'nullable|string|max:500',
        ]);

        $buenaPractica->update([
            'estado'            => 'recepcionado',
            'numero_expediente' => $request->numero_expediente,
            'fecha_recepcion'   => $request->fecha_recepcion ?? now()->toDateString(),
            'observaciones'     => $request->observaciones,
        ]);

        return back()->with('success', 'Proyecto recepcionado. Se notificará al usuario que fue recibido.');
    }

    // NIVEL 1: Comisión declara proyecto ELEGIBLE → pasa al concurso interno UGEL
    public function declararElegible(Request $request, BuenaPractica $buenaPractica)
    {
        Gate::authorize('buenas-practicas.editar');

        $request->validate([
            'puntaje_comision'    => 'required|integer|min:0|max:100',
            'observacion_comision'=> 'required|string|max:500',
            'responsable_id'      => 'nullable|exists:users,id',
        ]);

        $buenaPractica->update([
            'estado'              => 'elegible',
            'puntaje_comision'    => $request->puntaje_comision,
            'observacion_comision'=> $request->observacion_comision,
            'responsable_id'      => $request->responsable_id,
            'feedback_sci'        => $request->observacion_comision,
        ]);

        return back()->with('success', 'Proyecto declarado ELEGIBLE. Participará en el concurso interno UGEL Huacaybamba.');
    }

    // NIVEL 1: Comisión declara proyecto NO ELEGIBLE
    public function noElegible(Request $request, BuenaPractica $buenaPractica)
    {
        Gate::authorize('buenas-practicas.editar');

        $request->validate([
            'observacion_comision' => 'required|string|max:500',
        ]);

        $buenaPractica->update([
            'estado'               => 'no_elegible',
            'observacion_comision' => $request->observacion_comision,
            'feedback_sci'         => $request->observacion_comision,
        ]);

        return back()->with('success', 'Proyecto marcado como No Elegible. El participante recibirá el feedback.');
    }

    // NIVEL 1: Comisión declara GANADOR UGEL → representará a la UGEL en concurso externo
    public function declararGanadorUgel(Request $request, BuenaPractica $buenaPractica)
    {
        Gate::authorize('buenas-practicas.editar');

        $request->validate([
            'observacion_comision' => 'nullable|string|max:500',
        ]);

        $buenaPractica->update([
            'estado'               => 'ganador_ugel',
            'observacion_comision' => $request->observacion_comision,
            'feedback_sci'         => $request->observacion_comision
                ?? '¡Felicitaciones! Tu proyecto ganó el concurso interno y representará a la UGEL Huacaybamba.',
        ]);

        return back()->with('success', '¡Ganador UGEL declarado! Este proyecto representará a la UGEL Huacaybamba en el concurso externo.');
    }

    // NIVEL 2: Registrar participación en concurso externo (MINEDU o DRE Huánuco)
    public function registrarExterno(Request $request, BuenaPractica $buenaPractica)
    {
        Gate::authorize('buenas-practicas.editar');

        $request->validate([
            'nivel_externo'          => 'required|in:minedu,dre',
            'fecha_concurso_externo' => 'nullable|date',
            'observacion_comision'   => 'nullable|string|max:500',
        ]);

        $buenaPractica->update([
            'estado'                 => 'participante_externo',
            'nivel_externo'          => $request->nivel_externo,
            'fecha_concurso_externo' => $request->fecha_concurso_externo,
            'observacion_comision'   => $request->observacion_comision,
            'feedback_sci'           => $request->observacion_comision
                ?? 'Tu proyecto fue registrado para participar en el concurso externo (' . strtoupper($request->nivel_externo) . ').',
        ]);

        return back()->with('success', 'Proyecto registrado en el concurso externo ' . strtoupper($request->nivel_externo) . '.');
    }

    // NIVEL 2: Registrar resultado del concurso externo
    public function resultadoExterno(Request $request, BuenaPractica $buenaPractica)
    {
        Gate::authorize('buenas-practicas.editar');

        $request->validate([
            'gano_externo'    => 'required|boolean',
            'resultado_externo' => 'nullable|string|max:500',
            'observacion_comision' => 'nullable|string|max:500',
        ]);

        $buenaPractica->update([
            'estado'               => $request->gano_externo ? 'ganador_externo' : 'participante_externo',
            'resultado_externo'    => $request->resultado_externo,
            'observacion_comision' => $request->observacion_comision,
            'feedback_sci'         => $request->observacion_comision,
        ]);

        $msg = $request->gano_externo
            ? '¡Felicitaciones! El proyecto ganó en el concurso externo ' . strtoupper($buenaPractica->nivel_externo ?? '') . '.'
            : 'Resultado del concurso externo registrado.';

        return back()->with('success', $msg);
    }

    // SCI registra práctica institucional directamente (no es concurso)
    public function store(Request $request)
    {
        Gate::authorize('buenas-practicas.crear');

        $validator = Validator::make($request->all(), [
            'titulo'             => 'required|string|max:255',
            'descripcion'        => 'nullable|string',
            'categoria'          => 'required|in:gestion,transparencia,integridad,innovacion,participacion',
            'modulo'             => 'required|in:sci,integridad',
            'unidad_organica_id' => 'nullable|exists:unidades_organicas,id',
            'responsable_id'     => 'nullable|exists:users,id',
            'estado'             => 'required|in:en_implementacion,completada,pendiente,suspendida',
            'avance'             => 'required|integer|min:0|max:100',
            'fecha_inicio'       => 'nullable|date',
            'fecha_termino'      => 'nullable|date|after_or_equal:fecha_inicio',
            'numero_sgd'         => 'nullable|string|max:50',
            'impacto'            => 'nullable|in:alto,medio,bajo',
            'evidencias'         => 'nullable|string',
            'observaciones'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Corrija los errores del formulario.');
        }

        BuenaPractica::create($request->only([
            'titulo', 'descripcion', 'categoria', 'modulo', 'unidad_organica_id',
            'responsable_id', 'estado', 'avance', 'fecha_inicio', 'fecha_termino',
            'numero_sgd', 'impacto', 'evidencias', 'observaciones',
        ]));

        return back()->with('success', 'Buena práctica institucional registrada correctamente.');
    }

    public function update(Request $request, BuenaPractica $buenaPractica)
    {
        Gate::authorize('buenas-practicas.editar');

        $validator = Validator::make($request->all(), [
            'titulo'             => 'required|string|max:255',
            'descripcion'        => 'nullable|string',
            'categoria'          => 'required|in:gestion,transparencia,integridad,innovacion,participacion',
            'modulo'             => 'required|in:sci,integridad',
            'unidad_organica_id' => 'nullable|exists:unidades_organicas,id',
            'responsable_id'     => 'nullable|exists:users,id',
            'estado'             => 'required|in:presentado,recepcionado,en_concurso,ganador,no_elegible,en_implementacion,completada,pendiente,suspendida',
            'avance'             => 'required|integer|min:0|max:100',
            'fecha_inicio'       => 'nullable|date',
            'fecha_termino'      => 'nullable|date',
            'numero_sgd'         => 'nullable|string|max:50',
            'impacto'            => 'nullable|in:alto,medio,bajo',
            'evidencias'         => 'nullable|string',
            'observaciones'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Corrija los errores del formulario.');
        }

        $buenaPractica->update($request->only([
            'titulo', 'descripcion', 'categoria', 'modulo', 'unidad_organica_id',
            'responsable_id', 'estado', 'avance', 'fecha_inicio', 'fecha_termino',
            'numero_sgd', 'impacto', 'evidencias', 'observaciones',
        ]));

        return back()->with('success', 'Proyecto actualizado correctamente.');
    }

    public function destroy(BuenaPractica $buenaPractica)
    {
        Gate::authorize('buenas-practicas.eliminar');
        $buenaPractica->delete();
        return back()->with('success', 'Proyecto eliminado.');
    }

    public function updateAvance(Request $request, BuenaPractica $buenaPractica)
    {
        Gate::authorize('buenas-practicas.editar');
        $request->validate(['avance' => 'required|integer|min:0|max:100']);
        $data = ['avance' => $request->avance];
        if ($request->avance == 100) {
            $data['estado'] = 'completada';
        }
        $buenaPractica->update($data);
        return response()->json(['success' => true, 'avance' => $buenaPractica->avance]);
    }
}
