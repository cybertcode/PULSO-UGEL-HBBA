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
        'titulo', 'descripcion', 'url_documento',
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

}
