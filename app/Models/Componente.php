<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Componente extends Model
{
    protected $fillable = ['numero', 'nombre', 'icono', 'tipo', 'descripcion', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class, 'componente_id');
    }

}
