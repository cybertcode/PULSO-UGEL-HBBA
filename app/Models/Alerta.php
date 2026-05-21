<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alerta extends Model
{
    use HasFactory;
    protected $fillable = [
        'actividad_id', 'usuario_id', 'unidad_organica_id',
        'titulo', 'mensaje', 'tipo', 'prioridad',
        'leida', 'leida_at',
    ];

    protected function casts(): array
    {
        return [
            'leida'    => 'boolean',
            'leida_at' => 'datetime',
        ];
    }

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class, 'actividad_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function unidadOrganica(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganica::class, 'unidad_organica_id');
    }

    public function marcarLeida(): void
    {
        $this->update(['leida' => true, 'leida_at' => now()]);
    }
}
