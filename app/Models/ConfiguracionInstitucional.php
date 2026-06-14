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
        'whatsapp_sci', 'correo_sci', 'cargo_sci',
        'correo_institucional', 'telefono',
        'logo_ruta', 'logo_url_publica', 'favicon_ruta', 'anio_gestion',
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

    public function setSiglaAttribute(string $value): void
    {
        $this->attributes['sigla'] = strtoupper($value);
    }

    /**
     * URL pública del logo para incrustar en emails.
     * Prioridad: 1) logo_url_publica (campo BD, URL externa como CDN/hosting)
     *            2) APP_URL/storage/... si APP_URL es público (producción)
     *            3) base64 embebido como último recurso (local/desarrollo)
     */
    public function logoUrlEmail(): ?string
    {
        if (empty($this->logo_ruta)) {
            return null;
        }

        // 1. URL externa configurada explícitamente (CDN, hosting público)
        if (!empty($this->logo_url_publica)) {
            return $this->logo_url_publica;
        }

        // 2. En producción usa la URL absoluta del storage
        $host = parse_url(config('app.url', ''), PHP_URL_HOST) ?? '';
        $esLocal = in_array($host, ['localhost', '127.0.0.1'])
                || str_ends_with($host, '.test')
                || str_ends_with($host, '.local');

        if (!$esLocal) {
            return rtrim(config('app.url'), '/') . '/storage/' . $this->logo_ruta;
        }

        // 3. Local: base64 embebido
        try {
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            if (!$disk->exists($this->logo_ruta)) {
                return null;
            }
            $path = $disk->path($this->logo_ruta);
            $mime = mime_content_type($path) ?: 'image/jpeg';
            return 'data:' . $mime . ';base64,' . base64_encode($disk->get($this->logo_ruta));
        } catch (\Throwable) {
            return null;
        }
    }

    /** Limpia el cache al actualizar la configuración */
    protected static function booted(): void
    {
        static::saved(static function () {
            \Illuminate\Support\Facades\Cache::forget('config_institucional');
        });
    }
}
