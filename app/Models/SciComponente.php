<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SciComponente extends Model
{
    protected $table = 'sci_componentes';

    protected $fillable = ['eje_id', 'nombre', 'icono', 'descripcion', 'orden', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function eje(): BelongsTo
    {
        return $this->belongsTo(SciEje::class, 'eje_id');
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(SciPregunta::class, 'componente_id')->orderBy('orden');
    }
}
