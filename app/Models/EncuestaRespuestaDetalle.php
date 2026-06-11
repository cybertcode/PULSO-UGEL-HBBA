<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EncuestaRespuestaDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'respuesta_id', 'pregunta_id', 'opcion_id', 'texto_respuesta',
    ];

    public function respuesta(): BelongsTo
    {
        return $this->belongsTo(EncuestaRespuesta::class, 'respuesta_id');
    }

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(EncuestaPregunta::class, 'pregunta_id');
    }

    public function opcion(): BelongsTo
    {
        return $this->belongsTo(EncuestaOpcion::class, 'opcion_id');
    }
}
