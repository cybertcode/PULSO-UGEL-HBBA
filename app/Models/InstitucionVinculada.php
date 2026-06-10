<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstitucionVinculada extends Model
{
    protected $table = 'instituciones_vinculadas';

    protected $fillable = [
        'nombre', 'sigla', 'logo_ruta', 'logo_url',
        'color_acento', 'url_sitio', 'descripcion', 'orden', 'activo',
    ];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true)->orderBy('orden');
    }

    public function getLogoSrcAttribute(): ?string
    {
        if ($this->logo_ruta) {
            return \Illuminate\Support\Facades\Storage::url($this->logo_ruta);
        }
        return $this->logo_url;
    }
}
