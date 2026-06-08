<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoevaluacionRespuesta extends Model
{
    protected $table = 'autoevaluacion_respuestas';

    protected $fillable = [
        'autoevaluacion_id', 'componente_id', 'pregunta',
        'respuesta', 'puntaje', 'evidencia', 'observacion',
    ];

    public function autoevaluacion()
    {
        return $this->belongsTo(Autoevaluacion::class);
    }

    public function componente()
    {
        return $this->belongsTo(Componente::class);
    }
}
