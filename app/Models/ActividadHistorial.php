<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActividadHistorial extends Model
{
    protected $table = 'actividad_historial';

    protected $fillable = [
        'actividad_id', 'usuario_id', 'campo',
        'valor_anterior', 'valor_nuevo', 'descripcion',
    ];

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class, 'actividad_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function getCampoLabelAttribute(): string
    {
        return match($this->campo) {
            'nombre'            => 'Nombre',
            'estado'            => 'Estado',
            'avance'            => 'Avance (%)',
            'prioridad'         => 'Prioridad',
            'responsable_id'    => 'Responsable',
            'unidad_organica_id'=> 'Unidad Orgánica',
            'fecha_limite'      => 'Fecha Límite',
            'observaciones'     => 'Observaciones',
            default             => ucfirst(str_replace('_', ' ', $this->campo)),
        };
    }
}
