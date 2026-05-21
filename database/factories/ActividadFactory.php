<?php

namespace Database\Factories;

use App\Models\Actividad;
use App\Models\Componente;
use App\Models\UnidadOrganica;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActividadFactory extends Factory
{
    protected $model = Actividad::class;

    private array $actividades = [
        'Elaborar el diagnóstico del sistema de control interno',
        'Implementar el plan de trabajo SCI',
        'Capacitar al personal en gestión de riesgos',
        'Revisar y actualizar el MOF institucional',
        'Elaborar el mapa de procesos críticos',
        'Identificar y registrar riesgos operativos',
        'Implementar controles para riesgos identificados',
        'Elaborar el informe semestral de avance SCI',
        'Actualizar la matriz de riesgos institucional',
        'Difundir el Código de Ética institucional',
        'Aplicar encuesta de clima organizacional',
        'Revisar el reglamento interno de trabajo',
        'Elaborar el plan de capacitación anual',
        'Implementar el sistema de archivo documentario',
        'Actualizar el TUPA institucional',
        'Realizar auditoría interna de procesos administrativos',
        'Implementar indicadores de gestión por área',
        'Elaborar el Plan Estratégico Institucional',
        'Actualizar el inventario de bienes patrimoniales',
        'Revisar contratos y convenios vigentes',
        'Implementar el sistema de control de asistencia',
        'Elaborar el informe de gestión anual',
        'Capacitar en uso del SIAF y SIGA',
        'Actualizar el plan de contingencia institucional',
        'Implementar el portal de transparencia',
        'Registrar actos administrativos en SGDOC',
        'Elaborar el plan de supervisión pedagógica',
        'Actualizar el padrón de instituciones educativas',
        'Implementar el sistema de quejas y sugerencias',
        'Elaborar la memoria institucional anual',
    ];

    public function definition(): array
    {
        $fechaInicio = fake()->dateTimeBetween('-6 months', '-1 month');
        $fechaLimite = fake()->dateTimeBetween('-2 months', '+4 months');
        $estado      = fake()->randomElement([
            'completada','completada','en_proceso','en_proceso','en_proceso','pendiente','vencida',
        ]);

        $avance = match($estado) {
            'completada' => 100,
            'en_proceso' => fake()->numberBetween(20, 90),
            'pendiente'  => fake()->numberBetween(0, 20),
            'vencida'    => fake()->numberBetween(0, 60),
            default      => 0,
        };

        $anio  = now()->year;
        $lastNum = Actividad::withTrashed()
            ->where('codigo', 'like', 'SCI-' . $anio . '-%')
            ->count();
        static $offset = null;
        if ($offset === null) $offset = $lastNum;
        $offset++;

        return [
            'codigo'             => 'SCI-' . $anio . '-' . str_pad($offset, 3, '0', STR_PAD_LEFT),
            'nombre'             => fake()->randomElement($this->actividades),
            'descripcion'        => fake()->optional(0.6)->paragraph(2),
            'componente_id'      => Componente::inRandomOrder()->value('id'),
            'unidad_organica_id' => UnidadOrganica::inRandomOrder()->value('id'),
            'responsable_id'     => User::where('estado', 'activo')->inRandomOrder()->value('id'),
            'creado_por'         => User::where('estado', 'activo')->inRandomOrder()->value('id'),
            'numero_sgd'         => fake()->optional(0.7)->numerify('SGD-' . $anio . '-####'),
            'fecha_inicio'       => $fechaInicio,
            'fecha_limite'       => $fechaLimite,
            'fecha_cumplimiento' => $estado === 'completada' ? fake()->dateTimeBetween($fechaInicio, 'now') : null,
            'avance'             => $avance,
            'estado'             => $estado,
            'prioridad'          => fake()->randomElement(['alta','media','media','baja']),
            'observaciones'      => fake()->optional(0.4)->sentence(),
        ];
    }

    public function completada(): static
    {
        return $this->state(fn() => [
            'estado'             => 'completada',
            'avance'             => 100,
            'fecha_cumplimiento' => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
    }

    public function enProceso(): static
    {
        return $this->state(fn() => [
            'estado'  => 'en_proceso',
            'avance'  => fake()->numberBetween(20, 90),
            'fecha_limite' => fake()->dateTimeBetween('now', '+3 months'),
        ]);
    }

    public function vencida(): static
    {
        return $this->state(fn() => [
            'estado'       => 'vencida',
            'avance'       => fake()->numberBetween(0, 50),
            'fecha_limite' => fake()->dateTimeBetween('-2 months', '-1 day'),
        ]);
    }
}
