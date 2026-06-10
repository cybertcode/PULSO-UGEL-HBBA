<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SliderLanding extends Model
{
    protected $table = 'slider_landing';

    protected $fillable = [
        'tipo', 'titulo', 'descripcion', 'imagen_url',
        'color_gradiente', 'etiqueta', 'url_accion',
        'texto_accion', 'orden', 'activo',
    ];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function scopeActivos($q)
    {
        return $q->where('activo', true)->orderBy('orden')->orderBy('id');
    }

    // Colores por tipo para la etiqueta
    public static function colorTipo(string $tipo): string
    {
        return match ($tipo) {
            'evento'    => '#28c76f',
            'normativa' => '#ff9f43',
            default     => '#7367f0',
        };
    }
}
