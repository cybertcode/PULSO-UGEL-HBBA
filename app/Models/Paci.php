<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paci extends Model
{
    use SoftDeletes;

    protected $table = 'paci';

    protected $fillable = [
        'titulo', 'anio', 'descripcion', 'numero_resolucion',
        'fecha_aprobacion', 'fecha_inicio', 'fecha_fin',
        'estado', 'avance', 'creado_por', 'archivo', 'observaciones',
    ];

    protected $casts = [
        'fecha_aprobacion' => 'date',
        'fecha_inicio'     => 'date',
        'fecha_fin'        => 'date',
    ];

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function actividades()
    {
        return $this->belongsToMany(Actividad::class, 'paci_actividades');
    }

    public function getEtiquetaEstadoAttribute(): string
    {
        return match($this->estado) {
            'borrador'     => 'Borrador',
            'aprobado'     => 'Aprobado',
            'en_ejecucion' => 'En Ejecución',
            'cerrado'      => 'Cerrado',
            default        => $this->estado,
        };
    }

    public function getColorEstadoAttribute(): string
    {
        return match($this->estado) {
            'borrador'     => 'secondary',
            'aprobado'     => 'info',
            'en_ejecucion' => 'primary',
            'cerrado'      => 'success',
            default        => 'secondary',
        };
    }
}
