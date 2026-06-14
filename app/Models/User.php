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
        'cargo_id',
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

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }

    public function sendPasswordResetNotification($token): void
    {
        $url = url(route('password.reset', ['token' => $token, 'email' => $this->email], false));

        ResetPasswordNotification::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Restablecer contraseña — PULSO UGEL')
                ->greeting('Hola, ' . $notifiable->name . '.')
                ->line('Recibimos una solicitud para restablecer la contraseña de tu cuenta.')
                ->action('Restablecer contraseña', $url)
                ->line('Este enlace expirará en ' . config('auth.passwords.users.expire', 60) . ' minutos.')
                ->line('Si no solicitaste restablecer tu contraseña, ignora este mensaje.')
                ->salutation('Atentamente, PULSO UGEL — UGEL Huacaybamba');
        });

        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification(): void
    {
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verifica tu correo electrónico — PULSO UGEL')
                ->greeting('Hola, ' . $notifiable->name . '.')
                ->line('Haz clic en el botón de abajo para verificar tu dirección de correo electrónico.')
                ->action('Verificar correo electrónico', $url)
                ->line('Si no creaste esta cuenta, no es necesario realizar ninguna acción.')
                ->salutation('Atentamente, PULSO UGEL — UGEL Huacaybamba');
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
