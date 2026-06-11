<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IntegridadComponente extends Model
{
    protected $table = 'integridad_componentes';

    protected $fillable = ['etapa_id', 'nombre', 'icono', 'descripcion', 'orden', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function etapa(): BelongsTo
    {
        return $this->belongsTo(IntegridadEtapa::class, 'etapa_id');
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(IntegridadPregunta::class, 'componente_id')->orderBy('orden');
    }
}
