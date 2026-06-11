<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class TrabajadorDestacado extends Model
{
    use SoftDeletes;

    protected $table = 'trabajadores_destacados';

    protected $fillable = [
        'unidad_organica_id', 'user_id', 'nombre', 'cargo', 'dni', 'correo',
        'foto_ruta',
        'puntaje_cumplimiento', 'puntaje_puntualidad',
        'puntaje_participacion', 'puntaje_responsabilidad',
        'anio', 'mes', 'categoria', 'motivo',
        'numero_resolucion', 'resolucion_ruta',
        'activo', 'registrado_por',
    ];

    protected function casts(): array
    {
        return [
            'activo'                   => 'boolean',
            'puntaje_cumplimiento'     => 'float',
            'puntaje_puntualidad'      => 'float',
            'puntaje_participacion'    => 'float',
            'puntaje_responsabilidad'  => 'float',
            'puntaje_total'            => 'float',
        ];
    }

    public function unidadOrganica(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganica::class, 'unidad_organica_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto_ruta && Storage::disk('public')->exists($this->foto_ruta)) {
            return Storage::url($this->foto_ruta);
        }
        return asset('assets/img/avatars/1.png');
    }

    public function getNivelAttribute(): string
    {
        $p = $this->puntaje_total;
        if ($p >= 90) return 'Excelente';
        if ($p >= 75) return 'Bueno';
        if ($p >= 60) return 'Regular';
        return 'En riesgo';
    }

    public function getNivelColorAttribute(): string
    {
        return match($this->nivel) {
            'Excelente' => 'success',
            'Bueno'     => 'primary',
            'Regular'   => 'warning',
            default     => 'danger',
        };
    }

    public function getMesNombreAttribute(): ?string
    {
        if (!$this->mes) return null;
        $meses = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
                  7=>'Julio',8=>'Agosto',9=>'Setiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
        return $meses[$this->mes] ?? null;
    }
}
