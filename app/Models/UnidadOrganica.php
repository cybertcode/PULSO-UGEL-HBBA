<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadOrganica extends Model
{
    protected $table = 'unidades_organicas';

    protected $fillable = ['codigo', 'nombre', 'sigla', 'responsable', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'unidad_organica_id');
    }

    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class, 'unidad_organica_id');
    }

    public function reconocimientos(): HasMany
    {
        return $this->hasMany(Reconocimiento::class, 'unidad_organica_id');
    }
}
