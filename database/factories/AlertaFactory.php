<?php

namespace Database\Factories;

use App\Models\Alerta;
use App\Models\Actividad;
use App\Models\User;
use App\Models\UnidadOrganica;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlertaFactory extends Factory
{
    protected $model = Alerta::class;

    private array $mensajesVencimiento = [
        'La actividad está próxima a vencer. Actualice el avance.',
        'Quedan pocos días para la fecha límite de esta actividad.',
        'La actividad superó su fecha límite sin completarse.',
        'Se requiere acción inmediata para cumplir con el plazo establecido.',
    ];

    private array $mensajesAvance = [
        'El avance registrado está por debajo del umbral mínimo esperado.',
        'La actividad presenta bajo nivel de avance respecto al tiempo transcurrido.',
        'Se recomienda acelerar la implementación de esta actividad.',
    ];

    private array $mensajesEvidencia = [
        'La actividad no cuenta con evidencias registradas.',
        'Se requiere subir documentos de respaldo para esta actividad.',
        'Faltan evidencias para sustentar el avance declarado.',
    ];

    public function definition(): array
    {
        $tipo = fake()->randomElement(['vencimiento','vencimiento','avance_bajo','evidencia_falta','sistema']);
        $actividad = Actividad::inRandomOrder()->first();

        [$titulo, $mensaje] = match($tipo) {
            'vencimiento'     => ['Actividad próxima a vencer', fake()->randomElement($this->mensajesVencimiento)],
            'avance_bajo'     => ['Avance insuficiente detectado', fake()->randomElement($this->mensajesAvance)],
            'evidencia_falta' => ['Sin evidencias registradas', fake()->randomElement($this->mensajesEvidencia)],
            default           => ['Notificación del sistema', fake()->sentence()],
        };

        return [
            'actividad_id'      => $actividad?->id,
            'usuario_id'        => User::where('estado','activo')->inRandomOrder()->value('id'),
            'unidad_organica_id'=> $actividad?->unidad_organica_id ?? UnidadOrganica::inRandomOrder()->value('id'),
            'titulo'            => $titulo . ($actividad ? ': ' . \Illuminate\Support\Str::limit($actividad->nombre, 40) : ''),
            'mensaje'           => $mensaje,
            'tipo'              => $tipo,
            'prioridad'         => fake()->randomElement(['alta','media','media','baja']),
            'leida'             => true,
            'leida_at'          => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
