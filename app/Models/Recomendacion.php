<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Recomendacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'recomendaciones';

    protected $fillable = [
        'titulo', 'descripcion', 'tipo', 'actividad_id', 'unidad_organica_id',
        'responsable_id', 'estado', 'prioridad', 'fecha_emision', 'fecha_limite',
        'fecha_atencion', 'numero_sgd', 'origen', 'modulo', 'observaciones', 'creado_por',
    ];

    protected $casts = [
        'fecha_emision'  => 'date',
        'fecha_limite'   => 'date',
        'fecha_atencion' => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->creado_por = Auth::id();
                if (!$model->fecha_emision) {
                    $model->fecha_emision = now()->toDateString();
                }
            }
        });
    }

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }

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

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getEstadoLabelAttribute(): string
    {
        return match($this->estado) {
            'pendiente'  => 'Pendiente',
            'en_proceso' => 'En Proceso',
            'atendida'   => 'Atendida',
            'rechazada'  => 'Rechazada',
            default      => ucfirst($this->estado),
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            'pendiente'  => 'warning',
            'en_proceso' => 'primary',
            'atendida'   => 'success',
            'rechazada'  => 'danger',
            default      => 'secondary',
        };
    }

    public function getTipoLabelAttribute(): string
    {
        return match($this->tipo) {
            'observacion'   => 'Observación',
            'recomendacion' => 'Recomendación',
            'mejora'        => 'Oportunidad de Mejora',
            default         => ucfirst($this->tipo),
        };
    }

    public function getTipoColorAttribute(): string
    {
        return match($this->tipo) {
            'observacion'   => 'danger',
            'recomendacion' => 'primary',
            'mejora'        => 'info',
            default         => 'secondary',
        };
    }

    public function getPrioridadColorAttribute(): string
    {
        return match($this->prioridad) {
            'alta'  => 'danger',
            'media' => 'warning',
            'baja'  => 'info',
            default => 'secondary',
        };
    }

    public function getEstaVencidaAttribute(): bool
    {
        return $this->fecha_limite
            && $this->fecha_limite->isPast()
            && !in_array($this->estado, ['atendida', 'rechazada']);
    }

    public function getDiasRestantesAttribute(): ?int
    {
        if (!$this->fecha_limite) return null;
        return now()->diffInDays($this->fecha_limite, false);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePendientes($q)
    {
        return $q->whereIn('estado', ['pendiente', 'en_proceso']);
    }

    public function scopeVencidas($q)
    {
        return $q->whereIn('estado', ['pendiente', 'en_proceso'])
                 ->where('fecha_limite', '<', now());
    }

    public function scopePorPrioridad($q, string $prioridad)
    {
        return $q->where('prioridad', $prioridad);
    }
}
