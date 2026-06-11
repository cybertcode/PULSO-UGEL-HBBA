<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EncuestaPregunta extends Model
{
    use HasFactory;

    protected $fillable = [
        'encuesta_id', 'orden', 'texto', 'tipo', 'requerida',
    ];

    protected function casts(): array
    {
        return [
            'requerida' => 'boolean',
        ];
    }

    public function encuesta(): BelongsTo
    {
        return $this->belongsTo(Encuesta::class);
    }

    public function opciones(): HasMany
    {
        return $this->hasMany(EncuestaOpcion::class, 'pregunta_id')->orderBy('orden');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(EncuestaRespuestaDetalle::class, 'pregunta_id');
    }

    public function tieneOpciones(): bool
    {
        return in_array($this->tipo, ['opcion_multiple', 'seleccion_multiple']);
    }

    public function getTipoLabelAttribute(): string
    {
        return match($this->tipo) {
            'opcion_multiple'      => 'Opción múltiple',
            'seleccion_multiple'   => 'Selección múltiple',
            'escala'               => 'Escala de valoración',
            'texto_libre'          => 'Texto libre',
            default                => $this->tipo,
        };
    }
}
