<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\ActaComite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ActasComiteController extends Controller
{
    public function index(Request $request)
    {
        $query = ActaComite::with(['secretario'])
            ->withCount('participantes');

        if ($request->filled('estado'))     $query->where('estado', $request->estado);
        if ($request->filled('tipo'))       $query->where('tipo_sesion', $request->tipo);
        if ($request->filled('anio'))       $query->whereYear('fecha_sesion', $request->anio);

        $actas = $query->orderByDesc('fecha_sesion')->paginate(10)->withQueryString();

        $stats = [
            'total'      => ActaComite::count(),
            'realizadas' => ActaComite::where('estado', 'realizada')->count(),
            'convocadas' => ActaComite::where('estado', 'convocada')->count(),
            'anio_actual'=> ActaComite::whereYear('fecha_sesion', now()->year)->count(),
        ];

        $usuarios = User::orderBy('name')->get(['id', 'name', 'cargo']);
        $anios    = range(now()->year - 2, now()->year + 1);

        return view('content.actas-comite.index', compact('actas', 'stats', 'usuarios', 'anios'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero_acta'    => 'required|string|max:50',
            'titulo'         => 'required|string|max:255',
            'fecha_sesion'   => 'required|date',
            'hora_inicio'    => 'nullable|date_format:H:i',
            'hora_fin'       => 'nullable|date_format:H:i',
            'lugar'          => 'nullable|string|max:200',
            'tipo_sesion'    => 'required|in:ordinaria,extraordinaria',
            'agenda'         => 'nullable|string',
            'desarrollo'     => 'nullable|string',
            'acuerdos'       => 'nullable|string',
            'compromisos'    => 'nullable|string',
            'estado'         => 'required|in:convocada,realizada,cancelada',
            'secretario_id'  => 'nullable|exists:users,id',
            'observaciones'  => 'nullable|string',
            'archivo_acta'   => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'participantes'  => 'nullable|array',
            'participantes.*'=> 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Corrija los errores.');
        }

        $data = $request->only([
            'numero_acta', 'titulo', 'fecha_sesion', 'hora_inicio', 'hora_fin',
            'lugar', 'tipo_sesion', 'agenda', 'desarrollo', 'acuerdos',
            'compromisos', 'estado', 'secretario_id', 'observaciones',
        ]);

        if ($request->hasFile('archivo_acta')) {
            $data['archivo_acta'] = $request->file('archivo_acta')->store('actas', 'public');
        }

        $acta = ActaComite::create($data);

        if ($request->filled('participantes')) {
            $sync = [];
            foreach ($request->participantes as $uid) {
                $sync[$uid] = ['asistio' => false];
            }
            $acta->participantes()->sync($sync);
        }

        return back()->with('success', 'Acta registrada correctamente.');
    }

    public function update(Request $request, ActaComite $actasComite)
    {
        $validator = Validator::make($request->all(), [
            'numero_acta'    => 'required|string|max:50',
            'titulo'         => 'required|string|max:255',
            'fecha_sesion'   => 'required|date',
            'hora_inicio'    => 'nullable|date_format:H:i',
            'hora_fin'       => 'nullable|date_format:H:i',
            'lugar'          => 'nullable|string|max:200',
            'tipo_sesion'    => 'required|in:ordinaria,extraordinaria',
            'agenda'         => 'nullable|string',
            'desarrollo'     => 'nullable|string',
            'acuerdos'       => 'nullable|string',
            'compromisos'    => 'nullable|string',
            'estado'         => 'required|in:convocada,realizada,cancelada',
            'secretario_id'  => 'nullable|exists:users,id',
            'observaciones'  => 'nullable|string',
            'archivo_acta'   => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Corrija los errores.');
        }

        $data = $request->only([
            'numero_acta', 'titulo', 'fecha_sesion', 'hora_inicio', 'hora_fin',
            'lugar', 'tipo_sesion', 'agenda', 'desarrollo', 'acuerdos',
            'compromisos', 'estado', 'secretario_id', 'observaciones',
        ]);

        if ($request->hasFile('archivo_acta')) {
            if ($actasComite->archivo_acta) Storage::disk('public')->delete($actasComite->archivo_acta);
            $data['archivo_acta'] = $request->file('archivo_acta')->store('actas', 'public');
        }

        $actasComite->update($data);

        return back()->with('success', 'Acta actualizada correctamente.');
    }

    public function destroy(ActaComite $actasComite)
    {
        if ($actasComite->archivo_acta) Storage::disk('public')->delete($actasComite->archivo_acta);
        $actasComite->delete();
        return back()->with('success', 'Acta eliminada.');
    }
}
