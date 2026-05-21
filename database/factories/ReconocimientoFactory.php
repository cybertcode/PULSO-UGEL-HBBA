<?php

namespace Database\Factories;

use App\Models\Reconocimiento;
use App\Models\UnidadOrganica;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReconocimientoFactory extends Factory
{
    protected $model = Reconocimiento::class;

    public function definition(): array
    {
        $completadas = fake()->numberBetween(5, 30);
        $total       = $completadas + fake()->numberBetween(0, 10);
        $avance      = $total > 0 ? round(($completadas / $total) * 100) : 0;
        $puntaje     = round($avance * 0.7 + fake()->numberBetween(0, 30), 2);

        return [
            'unidad_organica_id'       => UnidadOrganica::inRandomOrder()->value('id'),
            'anio'                     => now()->year,
            'mes'                      => null,
            'posicion'                 => 0, // se asigna en el seeder
            'puntaje'                  => $puntaje,
            'avance_global'            => $avance,
            'actividades_total'        => $total,
            'actividades_completadas'  => $completadas,
            'medalla'                  => null,
            'observaciones'            => fake()->optional(0.3)->sentence(),
        ];
    }
}
