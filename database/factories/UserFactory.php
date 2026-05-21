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

    // Cargos reales de una UGEL peruana
    private array $cargos = [
        'Director de UGEL',
        'Especialista en Gestión Pedagógica',
        'Especialista Administrativo',
        'Jefe de Área de Gestión Institucional',
        'Jefe de Área de Gestión Pedagógica',
        'Responsable de Contabilidad',
        'Responsable de Logística',
        'Responsable de Recursos Humanos',
        'Responsable de Tesorería',
        'Técnico Administrativo',
        'Técnico en Contabilidad',
        'Auxiliar Administrativo',
        'Asesor Legal',
        'Coordinador de Control Interno',
    ];

    public function definition(): array
    {
        $unidadId = UnidadOrganica::inRandomOrder()->value('id');

        return [
            'name'                      => fake()->name(),
            'email'                     => fake()->unique()->safeEmail(),
            'email_verified_at'         => now(),
            'password'                  => static::$password ??= Hash::make('password'),
            'dni'                       => fake()->numerify('########'),
            'cargo'                     => fake()->randomElement($this->cargos),
            'unidad_organica_id'        => $unidadId,
            'estado'                    => fake()->randomElement(['activo', 'activo', 'activo', 'inactivo']),
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
