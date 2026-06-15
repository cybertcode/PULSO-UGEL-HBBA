<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IntegridadDatosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $now  = Carbon::now();
        $anio = 2026;

        $creadorId = DB::table('users')->where('email', 'sci@ugelhuacaybamba.edu.pe')->value('id')
                  ?? DB::table('users')->where('email', 'admin@admin.com')->value('id');

        if (!$creadorId) {
            $this->command->warn('No se encontró usuario SCI ni admin para IntegridadDatosPruebaSeeder.');
            return;
        }

        $preguntas = DB::table('integridad_preguntas')->where('activo', true)->get();
        $unidades  = DB::table('unidades_organicas')->get();

        if ($preguntas->isEmpty()) {
            $this->command->warn('No hay preguntas de Integridad. Verifica EstructuraSciIntegridadSeeder.');
            return;
        }

        // Limpiar actividades Integridad previas de este año
        $existentes = DB::table('actividades')
            ->where('modulo', 'integridad')
            ->where('anio', $anio)
            ->pluck('id');

        DB::table('actividad_responsables')->whereIn('actividad_id', $existentes)->delete();
        DB::table('actividades')->whereIn('id', $existentes)->delete();

        // Distribución realista para el Modelo de Integridad:
        // más completadas al inicio (etapa diagnóstico) y más pendientes al final (monitoreo)
        $estadosDistribucion = [
            'completada', 'completada', 'completada',          // Etapa 1 — Diagnóstico: bien avanzada
            'completada', 'en_proceso', 'en_proceso',          // Etapa 1 — componentes mixtos
            'en_proceso', 'en_proceso', 'en_proceso',          // Etapa 2 — Implementación: en curso
            'observado',  'pendiente',  'pendiente',           // Etapa 2 — con observación
            'pendiente',  'pendiente',  'pendiente',           // Etapa 3 — Monitoreo: recién iniciando
            'vencida',    'pendiente',  'pendiente',           // Casos críticos
            'en_proceso', 'pendiente',  'pendiente',           // Completar distribución
            'pendiente',  'pendiente',                         // Últimas preguntas
        ];

        $contador = 1;
        foreach ($preguntas as $pregunta) {
            $unidad = $unidades->get(($contador - 1) % $unidades->count());

            $responsableId = DB::table('users')
                ->where('unidad_organica_id', $unidad->id)
                ->where('estado', 'activo')
                ->value('id') ?? $creadorId;

            $estado  = $estadosDistribucion[$contador - 1] ?? 'pendiente';
            $avance  = match($estado) {
                'completada' => 100,
                'en_proceso' => rand(30, 80),
                'observado'  => rand(10, 45),
                'vencida'    => rand(0, 30),
                default      => 0,
            };
            $prioridad   = $contador <= 5 ? 'alta' : ($contador <= 12 ? 'media' : 'baja');
            $fechaInicio = $now->copy()->subDays(rand(30, 90))->toDateString();
            $fechaLimite = $estado === 'vencida'
                ? $now->copy()->subDays(rand(1, 20))->toDateString()
                : $now->copy()->addDays(rand(10, 60))->toDateString();

            $id = DB::table('actividades')->insertGetId([
                'modulo'                 => 'integridad',
                'integridad_pregunta_id' => $pregunta->id,
                'unidad_organica_id'     => $unidad->id,
                'codigo'                 => 'INT-' . $anio . '-' . str_pad($contador, 3, '0', STR_PAD_LEFT),
                'nombre'                 => $pregunta->nombre,
                'descripcion'            => 'Actividad del Modelo de Integridad — componente de implementación institucional.',
                'anio'                   => $anio,
                'numero_sgd'             => 'SGD-' . $anio . '-' . str_pad($contador + 200, 4, '0', STR_PAD_LEFT),
                'fecha_inicio'           => $fechaInicio,
                'fecha_limite'           => $fechaLimite,
                'fecha_cumplimiento'     => $estado === 'completada'
                    ? $now->copy()->subDays(rand(1, 15))->toDateString()
                    : null,
                'estado'                 => $estado,
                'avance'                 => $avance,
                'prioridad'              => $prioridad,
                'observaciones'          => $estado === 'observado'
                    ? 'Documentación incompleta. Requiere subsanación antes del cierre del período de evaluación.'
                    : null,
                'creado_por'             => $creadorId,
                'created_at'             => $now,
                'updated_at'             => $now,
            ]);

            DB::table('actividad_responsables')->insert([
                'actividad_id' => $id,
                'user_id'      => $responsableId,
                'tipo'         => 'principal',
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);

            // Agregar colaborador en actividades activas
            if (in_array($estado, ['en_proceso', 'observado'])) {
                $colaboradorId = DB::table('users')
                    ->where('estado', 'activo')
                    ->where('id', '!=', $responsableId)
                    ->inRandomOrder()
                    ->value('id');

                if ($colaboradorId) {
                    DB::table('actividad_responsables')->insertOrIgnore([
                        'actividad_id' => $id,
                        'user_id'      => $colaboradorId,
                        'tipo'         => 'colaborador',
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]);
                }
            }

            $contador++;
        }

        $total = $contador - 1;
        $this->command->info("✓ IntegridadDatosPruebaSeeder: {$total} actividades de Integridad insertadas (anio {$anio}).");
    }
}
