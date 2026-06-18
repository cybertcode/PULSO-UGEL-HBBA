<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\UnidadOrganica;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use HasRoles;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'dni',
        'unidad_organica_id',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function unidadOrganica(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganica::class, 'unidad_organica_id');
    }

    public function cargos(): BelongsToMany
    {
        return $this->belongsToMany(Cargo::class, 'cargo_user')->withTimestamps();
    }

    public function sendPasswordResetNotification($token): void
    {
        $url = url(route('password.reset', ['token' => $token, 'email' => $this->email], false));
        $ci  = ConfiguracionInstitucional::cached();
        $nombre = $ci?->sigla ?? $ci?->nombre_institucion ?? config('app.name');
        $lugar  = implode(' — ', array_filter([$ci?->nombre_institucion, $ci?->departamento]));
        $expire = config('auth.passwords.users.expire', 60);

        ResetPasswordNotification::toMailUsing(function ($notifiable, $url) use ($nombre, $lugar, $expire) {
            return (new MailMessage)
                ->subject("Restablecer contraseña — {$nombre}")
                ->greeting('Hola, ' . $notifiable->name . '.')
                ->line('Recibimos una solicitud para restablecer la contraseña de tu cuenta en el sistema institucional.')
                ->line('Haz clic en el botón a continuación para crear una nueva contraseña. Este enlace expirará en **' . $expire . ' minutos**.')
                ->action('Restablecer mi contraseña', $url)
                ->line('Si no solicitaste restablecer tu contraseña, puedes ignorar este mensaje. Tu cuenta permanece segura.')
                ->salutation("Atentamente,\n{$lugar}");
        });

        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification(): void
    {
        $ci     = ConfiguracionInstitucional::cached();
        $nombre = $ci?->sigla ?? $ci?->nombre_institucion ?? config('app.name');
        $lugar  = implode(' — ', array_filter([$ci?->nombre_institucion, $ci?->departamento]));

        VerifyEmail::toMailUsing(function ($notifiable, $url) use ($nombre, $lugar) {
            return (new MailMessage)
                ->subject("Verifica tu correo electrónico — {$nombre}")
                ->greeting('Hola, ' . $notifiable->name . '.')
                ->line('Para completar el acceso al sistema, necesitamos verificar tu dirección de correo electrónico.')
                ->action('Verificar mi correo', $url)
                ->line('Si no creaste esta cuenta, no es necesario realizar ninguna acción.')
                ->salutation("Atentamente,\n{$lugar}");
        });

        $this->notify(new VerifyEmail);
    }

    public function actividadesResponsable(): BelongsToMany
    {
        return $this->belongsToMany(Actividad::class, 'actividad_responsables', 'user_id', 'actividad_id')
                    ->withPivot('tipo')
                    ->withTimestamps();
    }
}
