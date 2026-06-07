<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialRanking extends Model
{
    protected $table = 'historial_ranking';

    protected $fillable = [
        'unidad_organica_id', 'posicion', 'posicion_anterior',
        'porcentaje', 'anio', 'mes',
    ];

    public function unidadOrganica(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganica::class, 'unidad_organica_id');
    }

    public function getVariacionAttribute(): int
    {
        if (is_null($this->posicion_anterior)) return 0;
        return $this->posicion_anterior - $this->posicion; // positivo = subió
    }
}
