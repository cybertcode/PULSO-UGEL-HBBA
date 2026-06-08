<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Autoevaluacion extends Model
{
    use SoftDeletes;

    protected $table = 'autoevaluaciones';

    protected $fillable = [
        'titulo', 'anio', 'periodo', 'fecha_inicio', 'fecha_cierre',
        'estado', 'puntaje_total', 'conclusiones', 'recomendaciones', 'elaborado_por',
    ];

    protected $casts = [
        'fecha_inicio'  => 'date',
        'fecha_cierre'  => 'date',
    ];

    public function elaboradoPor()
    {
        return $this->belongsTo(User::class, 'elaborado_por');
    }

    public function respuestas()
    {
        return $this->hasMany(AutoevaluacionRespuesta::class);
    }

    public function getPuntajeMaximoAttribute(): int
    {
        return $this->respuestas()->count() * 3;
    }

    public function getPorcentajeAttribute(): int
    {
        $max = $this->puntaje_maximo;
        if ($max === 0) return 0;
        return (int) round(($this->puntaje_total / $max) * 100);
    }

    public function getColorEstadoAttribute(): string
    {
        return match($this->estado) {
            'abierta'    => 'info',
            'en_proceso' => 'warning',
            'cerrada'    => 'success',
            default      => 'secondary',
        };
    }
}
