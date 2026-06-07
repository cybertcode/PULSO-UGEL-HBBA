<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $nombre_institucion
 * @property string $sigla
 * @property string|null $ugel_codigo
 * @property string|null $region
 * @property string|null $provincia
 * @property string|null $departamento
 * @property string|null $distrito
 * @property string|null $ubigeo
 * @property string|null $direccion
 * @property string|null $sitio_web
 * @property string $timezone
 * @property string|null $director
 * @property string|null $coordinador_sci
 * @property string|null $correo_institucional
 * @property string|null $telefono
 * @property string|null $logo_ruta
 * @property int|null    $anio_gestion
 * @property int $umbral_verde
 * @property int $umbral_amarillo
 * @property bool $notif_vencimiento
 * @property int  $notif_dias_anticipacion
 * @property bool $notif_avance_bajo
 * @property int  $notif_umbral_avance
 * @property bool $notif_email
 */
class ConfiguracionInstitucional extends Model
{
    protected $table = 'configuracion_institucional';

    protected $fillable = [
        'nombre_institucion', 'sigla', 'ugel_codigo', 'region', 'provincia',
        'departamento', 'distrito', 'ubigeo', 'direccion', 'sitio_web', 'timezone',
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
