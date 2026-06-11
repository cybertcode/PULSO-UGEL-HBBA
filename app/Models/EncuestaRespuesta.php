<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EncuestaRespuesta extends Model
{
    use HasFactory;

    protected $fillable = [
        'encuesta_id', 'usuario_id', 'completada', 'iniciada_at', 'completada_at',
    ];

    protected function casts(): array
    {
        return [
            'completada'    => 'boolean',
            'iniciada_at'   => 'datetime',
            'completada_at' => 'datetime',
        ];
    }

    public function encuesta(): BelongsTo
    {
        return $this->belongsTo(Encuesta::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(EncuestaRespuestaDetalle::class, 'respuesta_id');
    }
}
