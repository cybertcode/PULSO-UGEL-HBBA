<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Actividad extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'actividades';

    protected $fillable = [
        'codigo', 'nombre', 'descripcion',
        'componente_id', 'unidad_organica_id', 'creado_por',
        'numero_sgd', 'fecha_inicio', 'fecha_limite', 'fecha_cumplimiento',
        'avance', 'estado', 'prioridad', 'observaciones',
    ];

    const ESTADOS = ['pendiente', 'en_proceso', 'completada', 'observado', 'vencida'];
    const PRIORIDADES = ['alta', 'media', 'baja'];
    const TIPOS_RESPONSABLE = ['principal', 'colaborador', 'supervisor'];

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
            $dirty    = $actividad->getDirty();
            $original = $actividad->getOriginal();
            $usuario  = Auth::id();

            $camposAuditar = [
                'nombre', 'estado', 'avance', 'prioridad',
                'unidad_organica_id', 'fecha_limite', 'observaciones',
            ];

            foreach ($camposAuditar as $campo) {
                if (array_key_exists($campo, $dirty) && $dirty[$campo] != $original[$campo]) {
                    ActividadHistorial::create([
                        'actividad_id'   => $actividad->id,
                        'usuario_id'     => $usuario,
                        'campo'          => $campo,
                        'valor_anterior' => $original[$campo],
                        'valor_nuevo'    => $dirty[$campo],
                        'descripcion'    => "Campo «{$campo}» actualizado",
                    ]);
                }
            }
        });
    }

    // ─── Relaciones ───────────────────────────────────────────────────────────

    public function componente(): BelongsTo
    {
        return $this->belongsTo(Componente::class, 'componente_id');
    }

    public function unidadOrganica(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganica::class, 'unidad_organica_id');
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /** Responsables múltiples con tipo: principal | colaborador | supervisor */
    public function responsables(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'actividad_responsables', 'actividad_id', 'user_id')
                    ->withPivot('tipo')
                    ->withTimestamps()
                    ->orderByPivot('tipo');
    }

    /** Acceso rápido al responsable principal */
    public function responsablePrincipal(): BelongsToMany
    {
        return $this->responsables()->wherePivot('tipo', 'principal');
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

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getEstaVencidaAttribute(): bool
    {
        return $this->fecha_limite->isPast()
            && !in_array($this->estado, ['completada', 'observado']);
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

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Sincroniza responsables a partir de un array:
     * [user_id => tipo] o [user_id, user_id, ...] con tipo default
     */
    public function sincronizarResponsables(array $responsables): void
    {
        $syncData = [];
        foreach ($responsables as $userId => $tipo) {
            if (is_int($userId)) {
                $syncData[$userId] = ['tipo' => $tipo];
            } else {
                // array plano de IDs → todos como 'principal'
                $syncData[$tipo] = ['tipo' => 'principal'];
            }
        }
        $this->responsables()->sync($syncData);

        // Auditar cambio de responsables
        ActividadHistorial::create([
            'actividad_id'   => $this->id,
            'usuario_id'     => Auth::id(),
            'campo'          => 'responsables',
            'valor_anterior' => null,
            'valor_nuevo'    => implode(', ', array_keys($syncData)),
            'descripcion'    => 'Responsables actualizados',
        ]);
    }
}
