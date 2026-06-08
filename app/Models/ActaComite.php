<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActaComite extends Model
{
    use SoftDeletes;

    protected $table = 'actas_comite';

    protected $fillable = [
        'numero_acta', 'titulo', 'fecha_sesion', 'hora_inicio', 'hora_fin',
        'lugar', 'tipo_sesion', 'agenda', 'desarrollo', 'acuerdos',
        'compromisos', 'estado', 'secretario_id', 'archivo_acta', 'observaciones',
    ];

    protected $casts = [
        'fecha_sesion' => 'date',
    ];

    public function secretario()
    {
        return $this->belongsTo(User::class, 'secretario_id');
    }

    public function participantes()
    {
        return $this->belongsToMany(User::class, 'acta_participantes', 'acta_id', 'usuario_id')
                    ->withPivot('asistio', 'cargo_en_comite')
                    ->withTimestamps();
    }

    public function getColorEstadoAttribute(): string
    {
        return match($this->estado) {
            'convocada'  => 'warning',
            'realizada'  => 'success',
            'cancelada'  => 'danger',
            default      => 'secondary',
        };
    }
}
