<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EncuestaOpcion extends Model
{
    use HasFactory;

    protected $table = 'encuesta_opciones';

    protected $fillable = [
        'pregunta_id', 'orden', 'texto',
    ];

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(EncuestaPregunta::class, 'pregunta_id');
    }
}
