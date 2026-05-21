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

    public function getPorcentajeAttribute(): int
    {
        $total = $this->actividades()->count();
        if ($total === 0) return 0;
        $completadas = $this->actividades()->where('estado', 'completada')->count();
        return (int) round(($completadas / $total) * 100);
    }

    public function getSemaforoAttribute(): string
    {
        $pct = $this->porcentaje;
        return $pct >= 75 ? 'verde' : ($pct >= 50 ? 'amarillo' : 'rojo');
    }
}
