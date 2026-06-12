<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Normativa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class NormativasController extends Controller
{
    private array $tipos = [
        'ley'        => 'Ley',
        'decreto'    => 'Decreto',
        'resolucion' => 'Resolución',
        'directiva'  => 'Directiva',
        'manual'     => 'Manual',
        'reglamento' => 'Reglamento',
        'oficio'     => 'Oficio',
        'otro'       => 'Otro',
    ];

    private array $modulos = [
        'general'    => 'General',
        'sci'        => 'Control Interno (SCI)',
        'integridad' => 'Modelo de Integridad',
    ];

    private array $alcances = [
        'nacional'      => 'Nacional',
        'regional'      => 'Regional',
        'institucional' => 'Institucional',
    ];

    public function index(Request $request)
    {
        $esGestor = Gate::check('normativas.crear') || Gate::check('normativas.editar') || Gate::check('normativas.eliminar');

        $stats = [
            'total'      => Normativa::count(),
            'vigentes'   => Normativa::vigentes()->count(),
            'con_archivo'=> Normativa::whereNotNull('archivo_path')->count(),
            'con_tutorial'=> Normativa::whereNotNull('tutorial_url')->count(),
        ];

        return view('content.normativas.index', compact(
            'stats', 'esGestor'
        ) + [
            'tipos'   => $this->tipos,
            'modulos' => $this->modulos,
            'alcances'=> $this->alcances,
        ]);
    }

    // AJAX: cards paginadas
    public function data(Request $request)
    {
        // Más recientes primero; orden ascendente como criterio secundario para fijar destacadas
        $query = Normativa::orderByDesc('created_at')->orderBy('orden');

        if ($buscar = $request->get('buscar')) {
            $query->where(fn($q) => $q
                ->where('nombre', 'like', "%{$buscar}%")
                ->orWhere('codigo', 'like', "%{$buscar}%")
                ->orWhere('descripcion', 'like', "%{$buscar}%")
                ->orWhere('entidad_emisora', 'like', "%{$buscar}%")
            );
        }
        if ($tipo = $request->get('tipo')) {
            $query->where('tipo', $tipo);
        }
        if ($modulo = $request->get('modulo')) {
            $query->where('modulo', $modulo);
        }
        if ($alcance = $request->get('alcance')) {
            $query->where('alcance', $alcance);
        }
        if ($request->get('solo_vigentes')) {
            $query->vigentes();
        }

        $normativas = $query->paginate(12)->withQueryString();
        $esGestor   = Gate::check('normativas.crear') || Gate::check('normativas.editar') || Gate::check('normativas.eliminar');

        $stats = [
            'total'       => Normativa::count(),
            'vigentes'    => Normativa::vigentes()->count(),
            'archivo'     => Normativa::whereNotNull('archivo_path')->count(),
            'tutorial'    => Normativa::whereNotNull('tutorial_url')->count(),
        ];

        return response()->json([
            'html'  => view('content.normativas._cards', compact('normativas', 'esGestor'))->render(),
            'total' => $normativas->total(),
            'from'  => $normativas->firstItem() ?? 0,
            'to'    => $normativas->lastItem()  ?? 0,
            'links' => (string) $normativas->links(),
            'stats' => $stats,
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('normativas.crear');

        $validator = Validator::make($request->all(), [
            'nombre'          => 'required|string|max:255',
            'codigo'          => 'nullable|string|max:100',
            'descripcion'     => 'nullable|string',
            'tipo'            => 'required|in:ley,decreto,resolucion,directiva,manual,reglamento,oficio,otro',
            'alcance'         => 'required|in:nacional,regional,institucional',
            'modulo'          => 'required|in:general,sci,integridad',
            'archivo'         => 'nullable|file|mimes:pdf,doc,docx,zip,pptx,xls,xlsx|max:20480',
            'link_externo'    => 'nullable|url|max:500',
            'tutorial_url'    => 'nullable|url|max:500',
            'fecha_emision'   => 'nullable|date',
            'fecha_vigencia'  => 'nullable|date|after_or_equal:fecha_emision',
            'vigente'         => 'boolean',
            'entidad_emisora' => 'nullable|string|max:150',
            'observacion'     => 'nullable|string',
            'orden'           => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only([
            'nombre', 'codigo', 'descripcion', 'tipo', 'alcance', 'modulo',
            'link_externo', 'tutorial_url', 'fecha_emision', 'fecha_vigencia',
            'entidad_emisora', 'observacion', 'orden',
        ]);

        $data['vigente'] = $request->boolean('vigente', true);

        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $data['archivo_path']            = $file->store('normativas', 'public');
            $data['archivo_nombre_original'] = $file->getClientOriginalName();
        }

        // Detectar tipo tutorial
        if (!empty($data['tutorial_url'])) {
            $data['tutorial_tipo'] = str_contains($data['tutorial_url'], 'youtu') ? 'youtube' : 'link';
        }

        Normativa::create($data);

        return response()->json(['success' => true, 'message' => 'Normativa registrada correctamente.']);
    }

    public function show(Normativa $normativa)
    {
        return response()->json([
            'normativa' => $normativa,
            'tipo_label'   => $normativa->tipo_label,
            'tipo_color'   => $normativa->tipo_color,
            'tipo_icon'    => $normativa->tipo_icon,
            'modulo_label' => $normativa->modulo_label,
            'modulo_color' => $normativa->modulo_color,
            'alcance_label'=> $normativa->alcance_label,
            'tiene_archivo'=> $normativa->tiene_archivo,
            'tiene_link'   => $normativa->tiene_link,
            'tiene_tutorial'=> $normativa->tiene_tutorial,
            'esta_vigente' => $normativa->esta_vigente,
            'youtube_embed'=> $normativa->youtube_embed,
            'archivo_url'  => $normativa->archivo_path ? asset('storage/' . $normativa->archivo_path) : null,
        ]);
    }

    public function update(Request $request, Normativa $normativa)
    {
        Gate::authorize('normativas.editar');

        $validator = Validator::make($request->all(), [
            'nombre'          => 'required|string|max:255',
            'codigo'          => 'nullable|string|max:100',
            'descripcion'     => 'nullable|string',
            'tipo'            => 'required|in:ley,decreto,resolucion,directiva,manual,reglamento,oficio,otro',
            'alcance'         => 'required|in:nacional,regional,institucional',
            'modulo'          => 'required|in:general,sci,integridad',
            'archivo'         => 'nullable|file|mimes:pdf,doc,docx,zip,pptx,xls,xlsx|max:20480',
            'link_externo'    => 'nullable|url|max:500',
            'tutorial_url'    => 'nullable|url|max:500',
            'fecha_emision'   => 'nullable|date',
            'fecha_vigencia'  => 'nullable|date',
            'vigente'         => 'boolean',
            'entidad_emisora' => 'nullable|string|max:150',
            'observacion'     => 'nullable|string',
            'orden'           => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only([
            'nombre', 'codigo', 'descripcion', 'tipo', 'alcance', 'modulo',
            'link_externo', 'tutorial_url', 'fecha_emision', 'fecha_vigencia',
            'entidad_emisora', 'observacion', 'orden',
        ]);

        $data['vigente'] = $request->boolean('vigente', true);

        if ($request->hasFile('archivo')) {
            // Eliminar archivo anterior
            if ($normativa->archivo_path) {
                Storage::disk('public')->delete($normativa->archivo_path);
            }
            $file = $request->file('archivo');
            $data['archivo_path']            = $file->store('normativas', 'public');
            $data['archivo_nombre_original'] = $file->getClientOriginalName();
        }

        if (!empty($data['tutorial_url'])) {
            $data['tutorial_tipo'] = str_contains($data['tutorial_url'], 'youtu') ? 'youtube' : 'link';
        } else {
            $data['tutorial_tipo'] = null;
        }

        $normativa->update($data);

        return response()->json(['success' => true, 'message' => 'Normativa actualizada correctamente.']);
    }

    public function toggleVigente(Normativa $normativa)
    {
        Gate::authorize('normativas.editar');
        $nuevoValor = !$normativa->vigente;
        $normativa->update(['vigente' => $nuevoValor]);
        return response()->json([
            'success' => true,
            'vigente' => $nuevoValor,
            'message' => $nuevoValor ? 'Normativa marcada como vigente.' : 'Normativa marcada como no vigente.',
        ]);
    }

    public function destroy(Normativa $normativa)
    {
        Gate::authorize('normativas.eliminar');

        if ($normativa->archivo_path) {
            Storage::disk('public')->delete($normativa->archivo_path);
        }

        $normativa->delete();

        return response()->json(['success' => true, 'message' => 'Normativa eliminada.']);
    }
}
