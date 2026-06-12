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

    // Estados del concurso:
    // presentado   → usuario envió su proyecto
    // recepcionado → SCI confirma recepción del documento
    // en_concurso  → comisión admitió el proyecto al concurso
    // ganador      → proyecto elegido para representar a la UGEL
    // no_elegible  → comisión determinó que no pasa
    // (estados legacy para prácticas ya existentes registradas directamente por SCI)
    // en_implementacion, completada, pendiente, suspendida

    protected $fillable = [
        'titulo', 'descripcion', 'categoria', 'modulo', 'unidad_organica_id',
        'responsable_id', 'estado', 'avance', 'fecha_inicio', 'fecha_termino',
        'numero_sgd', 'numero_expediente', 'fecha_recepcion', 'impacto',
        'evidencias', 'archivo_proyecto', 'observaciones', 'creado_por', 'propuesto_por',
        'calificacion', 'puntaje_comision', 'feedback_sci', 'observacion_comision',
        'nivel_externo', 'fecha_concurso_externo', 'resultado_externo',
    ];

    protected $casts = [
        'fecha_inicio'           => 'date',
        'fecha_termino'          => 'date',
        'fecha_recepcion'        => 'date',
        'fecha_concurso_externo' => 'date',
        'avance'                 => 'integer',
        'calificacion'           => 'integer',
        'puntaje_comision'       => 'integer',
    ];

    // Flujo concurso NIVEL 1 — UGEL Huacaybamba
    // presentado → recepcionado → elegible → ganador_ugel
    //                          └→ no_elegible
    // Flujo concurso NIVEL 2 — Externo (MINEDU / DRE Huánuco)
    // ganador_ugel → participante_externo → ganador_externo
    const ESTADOS_CONCURSO = [
        'presentado', 'recepcionado',
        'elegible', 'no_elegible',
        'ganador_ugel',
        'participante_externo', 'ganador_externo',
    ];

    // Estados de prácticas institucionales (registradas directamente por SCI)
    const ESTADOS_PRACTICA = ['pendiente', 'en_implementacion', 'completada', 'suspendida'];

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
            'presentado'          => 'Presentado',
            'recepcionado'        => 'Recepcionado',
            'elegible'            => 'Elegible — En Concurso UGEL',
            'no_elegible'         => 'No Elegible',
            'ganador_ugel'        => 'Ganador UGEL',
            'participante_externo'=> 'En Concurso Externo',
            'ganador_externo'     => 'Ganador ' . (ucfirst($this->nivel_externo ?? 'Externo')),
            'en_implementacion'   => 'En Implementación',
            'completada'          => 'Completada',
            'pendiente'           => 'Pendiente',
            'suspendida'          => 'Suspendida',
            default               => ucfirst($this->estado ?? ''),
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            'presentado'          => 'info',
            'recepcionado'        => 'primary',
            'elegible'            => 'warning',
            'no_elegible'         => 'danger',
            'ganador_ugel'        => 'success',
            'participante_externo'=> 'purple',
            'ganador_externo'     => 'success',
            'en_implementacion'   => 'primary',
            'completada'          => 'success',
            'pendiente'           => 'secondary',
            'suspendida'          => 'danger',
            default               => 'secondary',
        };
    }

    public function getEstadoIconAttribute(): string
    {
        return match($this->estado) {
            'presentado'          => 'tabler-send',
            'recepcionado'        => 'tabler-inbox',
            'elegible'            => 'tabler-tournament',
            'no_elegible'         => 'tabler-circle-x',
            'ganador_ugel'        => 'tabler-trophy',
            'participante_externo'=> 'tabler-world',
            'ganador_externo'     => 'tabler-star',
            'en_implementacion'   => 'tabler-loader',
            'completada'          => 'tabler-circle-check',
            'pendiente'           => 'tabler-clock',
            'suspendida'          => 'tabler-ban',
            default               => 'tabler-circle',
        };
    }

    public function getEsConcursoAttribute(): bool
    {
        return in_array($this->estado, self::ESTADOS_CONCURSO);
    }

    public function getEsGanadorUgelAttribute(): bool
    {
        return in_array($this->estado, ['ganador_ugel', 'participante_externo', 'ganador_externo']);
    }

    public function getNivelExternoLabelAttribute(): string
    {
        return match($this->nivel_externo) {
            'minedu' => 'MINEDU',
            'dre'    => 'DRE Huánuco',
            default  => 'Externo',
        };
    }

    public function getCategoriaLabelAttribute(): string
    {
        return match($this->categoria) {
            'gestion'       => 'Gestión',
            'transparencia' => 'Transparencia',
            'integridad'    => 'Integridad',
            'innovacion'    => 'Innovación',
            'participacion' => 'Participación',
            default         => ucfirst($this->categoria ?? ''),
        };
    }

    public function getModuloLabelAttribute(): string
    {
        return match($this->modulo) {
            'sci'        => 'Control Interno',
            'integridad' => 'Modelo Integridad',
            default      => ucfirst($this->modulo ?? 'sci'),
        };
    }

    public function getModuloColorAttribute(): string
    {
        return match($this->modulo) {
            'sci'        => 'primary',
            'integridad' => 'warning',
            default      => 'secondary',
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
            && !in_array($this->estado, ['completada', 'suspendida', 'ganador', 'no_elegible']);
    }

    public function getDiasRestantesAttribute(): ?int
    {
        if (!$this->fecha_termino) return null;
        return (int) now()->startOfDay()->diffInDays($this->fecha_termino->startOfDay(), false);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeProyectosConcurso($q)
    {
        return $q->whereIn('estado', self::ESTADOS_CONCURSO);
    }

    public function scopePresentados($q)
    {
        return $q->where('estado', 'presentado');
    }

    // Concurso interno UGEL: proyectos elegibles y ganadores UGEL (vista pública nivel 1)
    public function scopeConcursoUgel($q)
    {
        return $q->whereIn('estado', ['elegible', 'ganador_ugel', 'participante_externo', 'ganador_externo']);
    }

    // Concurso externo (MINEDU/DRE): proyectos que representan a la UGEL
    public function scopeConcursoExterno($q)
    {
        return $q->whereIn('estado', ['participante_externo', 'ganador_externo']);
    }

    public function scopePorModulo($q, string $modulo)
    {
        return $q->where('modulo', $modulo);
    }
}
