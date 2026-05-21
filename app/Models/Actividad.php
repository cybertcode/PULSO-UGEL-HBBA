<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Actividad extends Model
{
    use SoftDeletes;

    protected $table = 'actividades';

    protected $fillable = [
        'codigo', 'nombre', 'descripcion',
        'componente_id', 'unidad_organica_id', 'responsable_id', 'creado_por',
        'numero_sgd', 'fecha_inicio', 'fecha_limite', 'fecha_cumplimiento',
        'avance', 'estado', 'prioridad', 'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio'       => 'date',
            'fecha_limite'       => 'date',
            'fecha_cumplimiento' => 'date',
        ];
    }

    public function componente(): BelongsTo
    {
        return $this->belongsTo(Componente::class, 'componente_id');
    }

    public function unidadOrganica(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganica::class, 'unidad_organica_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(Evidencia::class, 'actividad_id');
    }

    public function alertas(): HasMany
    {
        return $this->hasMany(Alerta::class, 'actividad_id');
    }

    public function getEstaVencidaAttribute(): bool
    {
        return $this->fecha_limite->isPast() && $this->estado !== 'completada';
    }
}
