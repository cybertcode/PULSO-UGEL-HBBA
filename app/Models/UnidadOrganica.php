<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class UnidadOrganica extends Model
{
    protected $table = 'unidades_organicas';

    protected $fillable = [
        'codigo', 'nombre', 'sigla', 'responsable_id',
        'foto_ruta', 'correo', 'telefono', 'descripcion', 'activo',
    ];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'unidad_organica_id');
    }

    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class, 'unidad_organica_id');
    }

    public function reconocimientos(): HasMany
    {
        return $this->hasMany(Reconocimiento::class, 'unidad_organica_id');
    }

    public function historialRanking(): HasMany
    {
        return $this->hasMany(HistorialRanking::class, 'unidad_organica_id');
    }

    public function trabajadoresDestacados(): HasMany
    {
        return $this->hasMany(TrabajadorDestacado::class, 'unidad_organica_id');
    }

    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto_ruta) {
            return Storage::url($this->foto_ruta);
        }
        return null;
    }

}
