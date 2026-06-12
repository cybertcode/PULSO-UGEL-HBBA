<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

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
 * @property int|null    $director_id
 * @property string|null $coordinador_sci
 * @property int|null    $coordinador_sci_id
 * @property-read \App\Models\User|null $directorUser
 * @property-read \App\Models\User|null $coordinadorSciUser
 * @property string|null $correo_institucional
 * @property string|null $telefono
 * @property string|null $logo_ruta
 * @property string|null $favicon_ruta
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
        'director', 'director_id', 'coordinador_sci', 'coordinador_sci_id',
        'correo_institucional', 'telefono',
        'logo_ruta', 'favicon_ruta', 'anio_gestion',
        'umbral_verde', 'umbral_amarillo',
        'notif_vencimiento', 'notif_dias_anticipacion',
        'notif_10dias', 'notif_5dias', 'notif_1dia',
        'notif_modulo_sci', 'notif_modulo_integridad',
        'notif_avance_bajo', 'notif_umbral_avance', 'notif_email',
        'mail_host', 'mail_port', 'mail_username', 'mail_password',
        'mail_encryption', 'mail_from_name',
    ];

    protected function casts(): array
    {
        return [
            'notif_vencimiento'        => 'boolean',
            'notif_avance_bajo'        => 'boolean',
            'notif_email'              => 'boolean',
            'notif_10dias'             => 'boolean',
            'notif_5dias'              => 'boolean',
            'notif_1dia'               => 'boolean',
            'notif_modulo_sci'         => 'boolean',
            'notif_modulo_integridad'  => 'boolean',
        ];
    }

    public function directorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'director_id');
    }

    public function coordinadorSciUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinador_sci_id');
    }

    public static function actual(): self
    {
        return static::firstOrFail();
    }

    /** Devuelve la configuración cacheada por 10 minutos para no repetir la query en cada request */
    public static function cached(): self
    {
        return \Illuminate\Support\Facades\Cache::remember('config_institucional', 600, fn() => static::firstOrFail());
    }

    /** Limpia el cache al actualizar la configuración */
    protected static function booted(): void
    {
        static::saved(fn() => \Illuminate\Support\Facades\Cache::forget('config_institucional'));
    }
}
