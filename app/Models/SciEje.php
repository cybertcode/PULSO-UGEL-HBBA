<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SciEje extends Model
{
    protected $table = 'sci_ejes';

    protected $fillable = ['nombre', 'descripcion', 'anio', 'orden', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean', 'anio' => 'integer'];
    }

    public function componentes(): HasMany
    {
        return $this->hasMany(SciComponente::class, 'eje_id')->orderBy('orden');
    }

    public function preguntas()
    {
        return SciPregunta::whereHas('componente', fn($q) => $q->where('eje_id', $this->id));
    }

    public function actividades()
    {
        return Actividad::whereHas('sciPregunta.componente', fn($q) => $q->where('eje_id', $this->id));
    }
}
