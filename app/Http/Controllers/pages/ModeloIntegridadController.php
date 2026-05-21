<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Componente;
use App\Models\Actividad;

class ModeloIntegridadController extends Controller
{
    public function index()
    {
        $componentes = Componente::withCount([
            'actividades',
            'actividades as completadas_count' => fn($q) => $q->where('estado', 'completada'),
            'actividades as en_proceso_count'  => fn($q) => $q->where('estado', 'en_proceso'),
            'actividades as vencidas_count'    => fn($q) => $q->where('estado', '!=', 'completada')
                                                               ->where('estado', '!=', 'cancelada')
                                                               ->whereDate('fecha_limite', '<', now()),
        ])->where('activo', true)->orderBy('numero')->get()
          ->map(function ($c) {
              $c->porcentaje = $c->actividades_count > 0
                  ? round(($c->completadas_count / $c->actividades_count) * 100) : 0;
              $c->color    = $c->porcentaje >= 75 ? 'success' : ($c->porcentaje >= 50 ? 'warning' : 'danger');
              $c->semaforo = $c->porcentaje >= 75 ? 'Verde'   : ($c->porcentaje >= 50 ? 'Amarillo' : 'Rojo');
              return $c;
          });

        $avance_global = $componentes->avg('porcentaje');

        return view('content.modelo-integridad.index', compact('componentes', 'avance_global'));
    }
}
