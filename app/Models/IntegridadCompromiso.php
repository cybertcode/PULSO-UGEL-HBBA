<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntegridadCompromiso extends Model
{
    use SoftDeletes;

    protected $table = 'integridad_compromisos';

    protected $fillable = [
        'pilar', 'titulo', 'descripcion', 'avance', 'estado',
        'fecha_inicio', 'fecha_fin', 'responsable_id', 'evidencia', 'observaciones', 'anio',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function getEtiquetaPilarAttribute(): string
    {
        return match($this->pilar) {
            'compromiso' => 'Compromiso con la Integridad',
            'cultura'    => 'Cultura de Integridad',
            'regulacion' => 'Regulación Interna',
            'control'    => 'Control y Sanción',
            default      => ucfirst($this->pilar),
        };
    }

    public function getIconoPilarAttribute(): string
    {
        return match($this->pilar) {
            'compromiso' => 'tabler-hand-stop',
            'cultura'    => 'tabler-users',
            'regulacion' => 'tabler-book',
            'control'    => 'tabler-shield',
            default      => 'tabler-circle',
        };
    }

    public function getColorEstadoAttribute(): string
    {
        return match($this->estado) {
            'completado' => 'success',
            'en_proceso' => 'warning',
            'pendiente'  => 'secondary',
            default      => 'secondary',
        };
    }
}
