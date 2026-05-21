<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evidencia extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'actividad_id', 'subido_por', 'numero_sgd',
        'titulo', 'descripcion',
        'archivo_ruta', 'archivo_nombre', 'archivo_tipo', 'archivo_tamanio',
        'estado', 'validado_por', 'validado_at', 'motivo_rechazo',
    ];

    protected function casts(): array
    {
        return ['validado_at' => 'datetime'];
    }

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class, 'actividad_id');
    }

    public function subidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    public function validadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validado_por');
    }

    public function getTamanioFormateadoAttribute(): string
    {
        $bytes = $this->archivo_tamanio ?? 0;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
