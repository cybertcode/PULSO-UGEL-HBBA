<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UnidadOrganica;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    protected $model = User::class;

    public function definition(): array
    {
        $unidadId = UnidadOrganica::inRandomOrder()->value('id');

        return [
            'name'                      => $this->faker->name(),
            'email'                     => $this->faker->unique()->safeEmail(),
            'email_verified_at'         => now(),
            'password'                  => static::$password ??= Hash::make('password'),
            'dni'                       => $this->faker->numerify('########'),
            'unidad_organica_id'        => $unidadId,
            'estado'                    => $this->faker->randomElement(['activo', 'activo', 'activo', 'inactivo']),
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => null,
            'remember_token'            => Str::random(10),
            'profile_photo_path'        => null,
            'current_team_id'           => null,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
            'estado'            => 'pendiente',
        ]);
    }

    public function activo(): static
    {
        return $this->state(fn(array $attributes) => ['estado' => 'activo']);
    }
}
