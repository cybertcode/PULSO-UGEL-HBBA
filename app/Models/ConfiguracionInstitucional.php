<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionInstitucional extends Model
{
    protected $table = 'configuracion_institucional';

    protected $fillable = [
        'nombre_institucion', 'sigla', 'ugel_codigo', 'region', 'provincia',
        'director', 'coordinador_sci', 'correo_institucional', 'telefono',
        'logo_ruta', 'anio_gestion',
        'umbral_verde', 'umbral_amarillo',
        'notif_vencimiento', 'notif_dias_anticipacion',
        'notif_avance_bajo', 'notif_umbral_avance', 'notif_email',
    ];

    protected function casts(): array
    {
        return [
            'notif_vencimiento' => 'boolean',
            'notif_avance_bajo' => 'boolean',
            'notif_email'       => 'boolean',
        ];
    }

    public static function actual(): self
    {
        return static::firstOrFail();
    }
}
