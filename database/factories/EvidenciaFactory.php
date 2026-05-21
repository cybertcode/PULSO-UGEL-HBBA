<?php

namespace Database\Factories;

use App\Models\Evidencia;
use App\Models\Actividad;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvidenciaFactory extends Factory
{
    protected $model = Evidencia::class;

    private array $titulos = [
        'Acta de reunión de implementación',
        'Informe de avance mensual',
        'Plan de trabajo aprobado',
        'Resolución Directoral de aprobación',
        'Evidencia fotográfica de capacitación',
        'Lista de asistencia del personal',
        'Documento de gestión actualizado',
        'Memorándum de designación',
        'Informe técnico de evaluación',
        'Acta de conformidad',
        'Constancia de capacitación',
        'Oficio de comunicación interna',
        'Cronograma de actividades',
        'Matriz de seguimiento actualizada',
    ];

    public function definition(): array
    {
        $estado = fake()->randomElement(['validado','validado','pendiente','rechazado']);
        $tipos  = ['pdf','docx','xlsx','jpg'];
        $tipo   = fake()->randomElement($tipos);

        return [
            'actividad_id'    => Actividad::inRandomOrder()->value('id'),
            'subido_por'      => User::where('estado','activo')->inRandomOrder()->value('id'),
            'numero_sgd'      => fake()->optional(0.7)->numerify('SGD-' . now()->year . '-####'),
            'titulo'          => fake()->randomElement($this->titulos),
            'descripcion'     => fake()->optional(0.5)->sentence(),
            'archivo_ruta'    => 'evidencias/' . now()->format('Y/m') . '/' . fake()->uuid() . '.' . $tipo,
            'archivo_nombre'  => fake()->slug(3) . '.' . $tipo,
            'archivo_tipo'    => match($tipo) {
                'pdf'  => 'application/pdf',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'jpg'  => 'image/jpeg',
                default => 'application/octet-stream',
            },
            'archivo_tamanio' => fake()->numberBetween(50000, 5000000),
            'estado'          => $estado,
            'validado_por'    => in_array($estado, ['validado','rechazado'])
                ? User::where('estado','activo')->inRandomOrder()->value('id') : null,
            'validado_at'     => in_array($estado, ['validado','rechazado']) ? fake()->dateTimeBetween('-1 month','now') : null,
            'motivo_rechazo'  => $estado === 'rechazado' ? fake()->sentence() : null,
        ];
    }
}
