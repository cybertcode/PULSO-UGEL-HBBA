<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class BuenaPractica extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'buenas_practicas';

    protected $fillable = [
        'titulo', 'descripcion', 'categoria', 'unidad_organica_id',
        'responsable_id', 'estado', 'avance', 'fecha_inicio', 'fecha_termino',
        'numero_sgd', 'impacto', 'evidencias', 'observaciones', 'creado_por',
        'propuesto_por',
    ];

    protected $casts = [
        'fecha_inicio'   => 'date',
        'fecha_termino'  => 'date',
        'avance'         => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->creado_por = Auth::id();
            }
        });
    }

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function unidadOrganica()
    {
        return $this->belongsTo(UnidadOrganica::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function propuestoPor()
    {
        return $this->belongsTo(User::class, 'propuesto_por');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getEstadoLabelAttribute(): string
    {
        return match($this->estado) {
            'en_implementacion' => 'En Implementación',
            'completada'        => 'Completada',
            'pendiente'         => 'Pendiente',
            'suspendida'        => 'Suspendida',
            default             => ucfirst($this->estado),
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            'en_implementacion' => 'primary',
            'completada'        => 'success',
            'pendiente'         => 'warning',
            'suspendida'        => 'secondary',
            default             => 'secondary',
        };
    }

    public function getCategoriaLabelAttribute(): string
    {
        return match($this->categoria) {
            'gestion'         => 'Gestión',
            'transparencia'   => 'Transparencia',
            'integridad'      => 'Integridad',
            'innovacion'      => 'Innovación',
            'participacion'   => 'Participación',
            default           => ucfirst($this->categoria),
        };
    }

    public function getImpactoColorAttribute(): string
    {
        return match($this->impacto) {
            'alto'  => 'danger',
            'medio' => 'warning',
            'bajo'  => 'info',
            default => 'secondary',
        };
    }

    public function getEstaVencidaAttribute(): bool
    {
        return $this->fecha_termino
            && $this->fecha_termino->isPast()
            && $this->estado !== 'completada';
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActivas($q)
    {
        return $q->whereIn('estado', ['en_implementacion', 'pendiente']);
    }

    public function scopeCompletadas($q)
    {
        return $q->where('estado', 'completada');
    }
}
