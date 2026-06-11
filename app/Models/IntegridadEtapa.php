<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IntegridadEtapa extends Model
{
    protected $table = 'integridad_etapas';

    protected $fillable = ['nombre', 'descripcion', 'anio', 'orden', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean', 'anio' => 'integer'];
    }

    public function componentes(): HasMany
    {
        return $this->hasMany(IntegridadComponente::class, 'etapa_id')->orderBy('orden');
    }
}
