<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Reconocimiento;
use App\Models\UnidadOrganica;
use Illuminate\Http\Request;

class ReconocimientosController extends Controller
{
    public function index(Request $request)
    {
        $anio = $request->get('anio', now()->year);
        $mes  = $request->get('mes');

        $query = Reconocimiento::with('unidadOrganica')
            ->where('anio', $anio)
            ->orderBy('posicion');

        if ($mes) $query->where('mes', $mes);
        else       $query->whereNull('mes');

        $ranking = $query->get();
        $top3    = $ranking->take(3);
        $resto   = $ranking->skip(3);

        $anios = range(now()->year, now()->year - 3);
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Setiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        return view('content.reconocimientos.index', compact(
            'ranking', 'top3', 'resto', 'anios', 'meses', 'anio', 'mes'
        ));
    }
}
