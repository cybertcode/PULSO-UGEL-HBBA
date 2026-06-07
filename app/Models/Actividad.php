<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Actividad extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'actividades';

    protected $fillable = [
        'codigo', 'nombre', 'descripcion',
        'componente_id', 'unidad_organica_id', 'responsable_id', 'creado_por',
        'numero_sgd', 'fecha_inicio', 'fecha_limite', 'fecha_cumplimiento',
        'avance', 'estado', 'prioridad', 'observaciones',
    ];

    // Estados válidos según informe N° 054-2026
    const ESTADOS = ['pendiente', 'en_proceso', 'completada', 'observado', 'vencida'];

    protected function casts(): array
    {
        return [
            'fecha_inicio'       => 'date',
            'fecha_limite'       => 'date',
            'fecha_cumplimiento' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (self $actividad) {
            $dirty = $actividad->getDirty();
            $original = $actividad->getOriginal();
            $usuario = Auth::id();

            $camposAuditar = ['nombre', 'estado', 'avance', 'prioridad', 'responsable_id',
                              'unidad_organica_id', 'fecha_limite', 'observaciones'];

            foreach ($camposAuditar as $campo) {
                if (array_key_exists($campo, $dirty) && $dirty[$campo] != $original[$campo]) {
                    ActividadHistorial::create([
                        'actividad_id'    => $actividad->id,
                        'usuario_id'      => $usuario,
                        'campo'           => $campo,
                        'valor_anterior'  => $original[$campo],
                        'valor_nuevo'     => $dirty[$campo],
                        'descripcion'     => "Campo «{$campo}» actualizado",
                    ]);
                }
            }
        });
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

    public function historial(): HasMany
    {
        return $this->hasMany(ActividadHistorial::class, 'actividad_id')->latest();
    }

    public function getEstaVencidaAttribute(): bool
    {
        return $this->fecha_limite->isPast() && !in_array($this->estado, ['completada', 'observado']);
    }

    public function getEstadoLabelAttribute(): string
    {
        return match($this->estado) {
            'pendiente'  => 'Pendiente',
            'en_proceso' => 'En Proceso',
            'completada' => 'Completada',
            'observado'  => 'Observado',
            'vencida'    => 'Vencida',
            default      => ucfirst($this->estado),
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            'completada' => 'success',
            'en_proceso' => 'warning',
            'observado'  => 'info',
            'vencida'    => 'danger',
            default      => 'secondary',
        };
    }
}
