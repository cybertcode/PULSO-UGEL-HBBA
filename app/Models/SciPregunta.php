<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SciPregunta extends Model
{
    protected $table = 'sci_preguntas';

    protected $fillable = ['componente_id', 'nombre', 'link_ficha', 'orden', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function componente(): BelongsTo
    {
        return $this->belongsTo(SciComponente::class, 'componente_id');
    }

    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class, 'sci_pregunta_id');
    }
}
