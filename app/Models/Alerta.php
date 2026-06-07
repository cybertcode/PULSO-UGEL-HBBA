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
        'email_enviado', 'email_enviado_at', 'destinatario_email',
    ];

    protected function casts(): array
    {
        return [
            'leida'            => 'boolean',
            'leida_at'         => 'datetime',
            'email_enviado'    => 'boolean',
            'email_enviado_at' => 'datetime',
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

    public function getPrioridadColorAttribute(): string
    {
        return match($this->prioridad) {
            'alta'  => 'danger',
            'media' => 'warning',
            default => 'info',
        };
    }

    public function getPrioridadIconAttribute(): string
    {
        return match($this->prioridad) {
            'alta'  => 'tabler-alert-octagon',
            'media' => 'tabler-alert-triangle',
            default => 'tabler-info-circle',
        };
    }

    public function getTipoLabelAttribute(): string
    {
        return match($this->tipo) {
            'vencimiento'    => 'Vencimiento',
            'avance_bajo'    => 'Avance Bajo',
            'evidencia_falta'=> 'Evidencia Faltante',
            'sistema'        => 'Sistema',
            default          => ucfirst($this->tipo),
        };
    }
}
