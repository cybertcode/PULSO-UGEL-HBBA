<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\ActividadHistorial;
use App\Models\Evidencia;
use App\Models\IntegridadPregunta;
use App\Models\SciPregunta;
use App\Models\UnidadOrganica;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Crea actividades de prueba con TODOS los campos visibles en los cards:
 * - link_ficha visible
 * - panel de detalle con descripción, N° SGD, fecha inicio, observaciones
 * - evidencias en distintos estados
 * - responsables con tipo supervisor (para notificaciones de avance)
 *
 * Asignadas al usuario: logistica@ugelhuacaybamba.edu.pe (Operador)
 *
 * Ejecutar: php artisan db:seed --class=ActividadesPruebaCardsSeeder
 */
class ActividadesPruebaCardsSeeder extends Seeder
{
    public function run(): void
    {
        $operador  = User::where('email', 'logistica@ugelhuacaybamba.edu.pe')->firstOrFail();
        $director  = User::where('email', 'director@ugelhuacaybamba.edu.pe')->firstOrFail();
        $sci       = User::where('email', 'sci@ugelhuacaybamba.edu.pe')->firstOrFail();
        $jefe      = User::where('email', 'administracion@ugelhuacaybamba.edu.pe')->firstOrFail();
        $unidad    = UnidadOrganica::where('codigo', 'LOG')->firstOrFail();

        // Asegurar links en preguntas
        $this->asegurarLinksFicha();

        $sciPregConLink    = SciPregunta::whereNotNull('link_ficha')->first();
        $integPregConLink  = IntegridadPregunta::whereNotNull('link_ficha')->first();
        $sciPregSinLink    = SciPregunta::whereNull('link_ficha')->first();

        $anio = now()->year;
        $this->command->info("Creando actividades de prueba para {$operador->name}...");

        // ── 1. SCI — Pendiente con link ficha, descripción y SGD ─────────────
        $a1 = Actividad::create([
            'codigo'              => 'SCI-DEMO-001',
            'nombre'              => '[DEMO] Elaborar plan de control interno — con ficha y detalle',
            'modulo'              => 'sci',
            'sci_pregunta_id'     => $sciPregConLink?->id,
            'unidad_organica_id'  => $unidad->id,
            'anio'                => $anio,
            'estado'              => 'pendiente',
            'avance'              => 0,
            'prioridad'           => 'alta',
            'fecha_inicio'        => now()->subDays(5)->toDateString(),
            'fecha_limite'        => now()->addDays(20)->toDateString(),
            'numero_sgd'          => 'SGD-2024-00451',
            'descripcion'         => 'Elaborar y aprobar el Plan Anual de Control Interno en coordinación con las áreas usuarias, de acuerdo a la normativa vigente de la Contraloría General de la República.',
            'observaciones'       => null,
            'creado_por'          => $sci->id,
        ]);
        $a1->responsables()->attach($operador->id, ['tipo' => 'principal']);
        $a1->responsables()->attach($director->id, ['tipo' => 'supervisor']);
        ActividadHistorial::create([
            'actividad_id' => $a1->id, 'usuario_id' => $sci->id,
            'campo' => 'estado', 'valor_anterior' => null, 'valor_nuevo' => 'pendiente',
            'descripcion' => 'Actividad creada',
        ]);

        // ── 2. SCI — En proceso con evidencia pendiente de revisión ──────────
        $a2 = Actividad::create([
            'codigo'              => 'SCI-DEMO-002',
            'nombre'              => '[DEMO] Capacitación en control interno — evidencia en revisión',
            'modulo'              => 'sci',
            'sci_pregunta_id'     => $sciPregConLink?->id,
            'unidad_organica_id'  => $unidad->id,
            'anio'                => $anio,
            'estado'              => 'en_proceso',
            'avance'              => 100,
            'prioridad'           => 'media',
            'fecha_inicio'        => now()->subDays(15)->toDateString(),
            'fecha_limite'        => now()->addDays(10)->toDateString(),
            'numero_sgd'          => 'SGD-2024-00512',
            'descripcion'         => 'Realizar taller de capacitación al personal sobre control interno y normativa vigente.',
            'observaciones'       => null,
            'creado_por'          => $sci->id,
        ]);
        $a2->responsables()->attach($operador->id, ['tipo' => 'principal']);
        $a2->responsables()->attach($director->id, ['tipo' => 'supervisor']);
        Evidencia::create([
            'actividad_id' => $a2->id,
            'subido_por'   => $operador->id,
            'titulo'       => 'Acta de capacitación firmada',
            'descripcion'  => 'Lista de asistentes y acta de la sesión de capacitación realizada el ' . now()->subDays(2)->format('d/m/Y'),
            'numero_sgd'   => 'SGD-2024-00518',
            'estado'       => 'pendiente',
        ]);
        ActividadHistorial::create([
            'actividad_id' => $a2->id, 'usuario_id' => $operador->id,
            'campo' => 'avance', 'valor_anterior' => '0%', 'valor_nuevo' => '100%',
            'descripcion' => 'Avance forzado a 100% al enviar evidencia',
        ]);

        // ── 3. SCI — Con evidencia rechazada (requiere corrección) ────────────
        $a3 = Actividad::create([
            'codigo'              => 'SCI-DEMO-003',
            'nombre'              => '[DEMO] Diagnóstico de riesgos — evidencia rechazada',
            'modulo'              => 'sci',
            'sci_pregunta_id'     => $sciPregSinLink?->id ?? $sciPregConLink?->id,
            'unidad_organica_id'  => $unidad->id,
            'anio'                => $anio,
            'estado'              => 'observado',
            'avance'              => 60,
            'prioridad'           => 'alta',
            'fecha_inicio'        => now()->subDays(30)->toDateString(),
            'fecha_limite'        => now()->addDays(5)->toDateString(),
            'numero_sgd'          => null,
            'descripcion'         => 'Elaborar matriz de identificación y evaluación de riesgos por proceso.',
            'observaciones'       => 'La evidencia enviada no corresponde al formato solicitado. Adjuntar la matriz en formato Excel aprobado por la CGR.',
            'creado_por'          => $sci->id,
        ]);
        $a3->responsables()->attach($operador->id, ['tipo' => 'principal']);
        $ev3 = Evidencia::create([
            'actividad_id'   => $a3->id,
            'subido_por'     => $operador->id,
            'titulo'         => 'Matriz de riesgos v1',
            'descripcion'    => 'Primera versión de la matriz de riesgos.',
            'estado'         => 'rechazado',
            'motivo_rechazo' => 'El documento no usa el formato oficial CGR-2024. Favor adjuntar el archivo Excel descargado del portal de la Contraloría.',
        ]);

        // ── 4. Integridad — Completada con evidencia validada ─────────────────
        $a4 = Actividad::create([
            'codigo'                  => 'INTEG-DEMO-001',
            'nombre'                  => '[DEMO] Diagnóstico de integridad institucional — completada',
            'modulo'                  => 'integridad',
            'integridad_pregunta_id'  => $integPregConLink?->id,
            'unidad_organica_id'      => $unidad->id,
            'anio'                    => $anio,
            'estado'                  => 'completada',
            'avance'                  => 100,
            'prioridad'               => 'alta',
            'fecha_inicio'            => now()->subDays(60)->toDateString(),
            'fecha_limite'            => now()->subDays(10)->toDateString(),
            'fecha_cumplimiento'      => now()->subDays(12)->toDateString(),
            'numero_sgd'              => 'SGD-2024-00389',
            'descripcion'             => 'Elaborar el diagnóstico sobre la situación de integridad institucional conforme a los lineamientos de la PCM.',
            'observaciones'           => null,
            'creado_por'              => $director->id,
        ]);
        $a4->responsables()->attach($operador->id, ['tipo' => 'principal']);
        $a4->responsables()->attach($jefe->id, ['tipo' => 'colaborador']);
        Evidencia::create([
            'actividad_id' => $a4->id,
            'subido_por'   => $operador->id,
            'titulo'       => 'Informe de diagnóstico de integridad 2024',
            'descripcion'  => 'Documento aprobado por la Dirección mediante Resolución Directoral N° 0234-2024.',
            'numero_sgd'   => 'SGD-2024-00395',
            'estado'       => 'validado',
        ]);

        // ── 5. SCI — Vencida sin evidencia ────────────────────────────────────
        $a5 = Actividad::create([
            'codigo'              => 'SCI-DEMO-004',
            'nombre'              => '[DEMO] Informe trimestral SCI — vencida sin evidencia',
            'modulo'              => 'sci',
            'sci_pregunta_id'     => $sciPregConLink?->id,
            'unidad_organica_id'  => $unidad->id,
            'anio'                => $anio,
            'estado'              => 'vencida',
            'avance'              => 35,
            'prioridad'           => 'alta',
            'fecha_inicio'        => now()->subDays(45)->toDateString(),
            'fecha_limite'        => now()->subDays(8)->toDateString(),
            'numero_sgd'          => null,
            'descripcion'         => 'Elaborar y remitir el informe trimestral de avance del SCI al órgano rector.',
            'observaciones'       => 'Actividad vencida sin evidencia sustentoria. Requiere subsanación urgente.',
            'creado_por'          => $sci->id,
        ]);
        $a5->responsables()->attach($operador->id, ['tipo' => 'principal']);

        // ── 6. Integridad — En proceso con detalle completo ───────────────────
        $a6 = Actividad::create([
            'codigo'                  => 'INTEG-DEMO-002',
            'nombre'                  => '[DEMO] Plan de integridad — en proceso, link ficha visible',
            'modulo'                  => 'integridad',
            'integridad_pregunta_id'  => $integPregConLink?->id,
            'unidad_organica_id'      => $unidad->id,
            'anio'                    => $anio,
            'estado'                  => 'en_proceso',
            'avance'                  => 55,
            'prioridad'               => 'media',
            'fecha_inicio'            => now()->subDays(20)->toDateString(),
            'fecha_limite'            => now()->addDays(30)->toDateString(),
            'numero_sgd'              => 'SGD-2024-00501',
            'descripcion'             => 'Formular el Plan de Integridad Institucional 2024 en coordinación con todas las unidades orgánicas.',
            'observaciones'           => null,
            'creado_por'              => $director->id,
        ]);
        $a6->responsables()->attach($operador->id, ['tipo' => 'principal']);
        $a6->responsables()->attach($director->id, ['tipo' => 'supervisor']);
        ActividadHistorial::create([
            'actividad_id' => $a6->id, 'usuario_id' => $operador->id,
            'campo' => 'avance', 'valor_anterior' => '0%', 'valor_nuevo' => '55%',
            'descripcion' => 'Avance actualizado de 0% a 55%',
        ]);

        $total = 6;
        $this->command->newLine();
        $this->command->info("✓ {$total} actividades DEMO creadas para {$operador->name}.");
        $this->command->newLine();
        $this->command->line('Inicia sesión con: logistica@ugelhuacaybamba.edu.pe / Ugel@2024');
        $this->command->line('Ir a: http://127.0.0.1:8000/mis-actividades');
        $this->command->newLine();
        $this->command->table(
            ['Código', 'Escenario visible en card'],
            [
                ['SCI-DEMO-001', 'Link ficha + panel detalle (Eje, Descripción, SGD, Fecha inicio)'],
                ['SCI-DEMO-002', 'Link ficha + chip "en revisión" (evidencia pendiente)'],
                ['SCI-DEMO-003', 'Observaciones en detalle + chip "rechazada" + botón corregir'],
                ['INTEG-DEMO-001', 'Link ficha integridad + chip "validada" + estado completada'],
                ['SCI-DEMO-004', 'Vencida + chip "Sin evidencia" + panel con observaciones'],
                ['INTEG-DEMO-002', 'Link ficha integridad + panel con SGD + avance 55%'],
            ]
        );
    }

    private function asegurarLinksFicha(): void
    {
        $base = 'https://www.gob.pe/institucion/contraloria/informes-publicaciones/sci-ficha-';
        SciPregunta::whereNull('link_ficha')->limit(10)->get()->each(
            fn($p) => $p->update(['link_ficha' => $base . $p->id])
        );

        $base2 = 'https://www.gob.pe/institucion/pcm/informes-publicaciones/integridad-ficha-';
        IntegridadPregunta::whereNull('link_ficha')->limit(10)->get()->each(
            fn($p) => $p->update(['link_ficha' => $base2 . $p->id])
        );
    }
}
