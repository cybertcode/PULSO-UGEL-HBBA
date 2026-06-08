<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatrizRiesgo extends Model
{
    use SoftDeletes;

    protected $table = 'matriz_riesgos';

    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'componente_id', 'unidad_organica_id',
        'tipo', 'probabilidad', 'impacto', 'clasificacion',
        'controles_existentes', 'acciones_tratamiento', 'tipo_tratamiento',
        'responsable_id', 'fecha_revision', 'estado', 'observaciones', 'anio',
    ];

    protected $casts = [
        'fecha_revision' => 'date',
    ];

    public function componente()
    {
        return $this->belongsTo(Componente::class);
    }

    public function unidadOrganica()
    {
        return $this->belongsTo(UnidadOrganica::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function getNivelRiesgoCalculadoAttribute(): int
    {
        return $this->probabilidad * $this->impacto;
    }

    public function getClasificacionAutoAttribute(): string
    {
        $nivel = $this->probabilidad * $this->impacto;
        return match(true) {
            $nivel >= 15 => 'critico',
            $nivel >= 8  => 'alto',
            $nivel >= 4  => 'moderado',
            default      => 'bajo',
        };
    }

    public function getColorClasificacionAttribute(): string
    {
        return match($this->clasificacion) {
            'critico'  => 'danger',
            'alto'     => 'warning',
            'moderado' => 'info',
            'bajo'     => 'success',
            default    => 'secondary',
        };
    }

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            $nivel = $model->probabilidad * $model->impacto;
            $model->clasificacion = match(true) {
                $nivel >= 15 => 'critico',
                $nivel >= 8  => 'alto',
                $nivel >= 4  => 'moderado',
                default      => 'bajo',
            };
        });
    }
}
