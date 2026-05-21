<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reconocimiento extends Model
{
    use HasFactory;
    protected $fillable = [
        'unidad_organica_id', 'anio', 'mes', 'posicion',
        'puntaje', 'avance_global',
        'actividades_total', 'actividades_completadas',
        'medalla', 'observaciones',
    ];

    public function unidadOrganica(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganica::class, 'unidad_organica_id');
    }

    public function getMedallaColorAttribute(): string
    {
        return match ($this->medalla) {
            'oro'     => 'warning',
            'plata'   => 'secondary',
            'bronce'  => 'orange',
            default   => 'info',
        };
    }
}
