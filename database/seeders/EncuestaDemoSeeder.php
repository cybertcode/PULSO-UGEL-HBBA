<?php

namespace Database\Seeders;

use App\Models\Alerta;
use App\Models\Encuesta;
use App\Models\EncuestaDestinatario;
use App\Models\EncuestaPregunta;
use App\Models\EncuestaRespuesta;
use App\Models\EncuestaRespuestaDetalle;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EncuestaDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar datos anteriores del módulo encuestas
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        EncuestaRespuestaDetalle::truncate();
        EncuestaRespuesta::truncate();
        EncuestaDestinatario::truncate();
        DB::table('encuesta_opciones')->truncate();
        EncuestaPregunta::truncate();
        Encuesta::withTrashed()->forceDelete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $creador = User::first();
        $usuarios = User::where('estado', 'activo')->take(12)->get();

        // ══════════════════════════════════════════════════
        // ENCUESTA 1: Clima Laboral — PUBLICADA con respuestas
        // ══════════════════════════════════════════════════
        $enc1 = Encuesta::create([
            'titulo'      => 'Encuesta de Clima Laboral 2026',
            'descripcion' => 'Evaluación del ambiente de trabajo y satisfacción del personal de la UGEL Huacaybamba. Sus respuestas son anónimas y serán utilizadas para mejorar las condiciones laborales.',
            'modulo'      => 'ambos',
            'estado'      => 'publicada',
            'fecha_inicio' => now()->subDays(7)->toDateString(),
            'fecha_fin'   => now()->addDays(14)->toDateString(),
            'creado_por'  => $creador->id,
            'published_at' => now()->subDays(7),
        ]);

        // Destinatarios: todos
        EncuestaDestinatario::create(['encuesta_id' => $enc1->id, 'tipo' => 'todos', 'referencia_id' => null]);

        // Preguntas
        $p1 = EncuestaPregunta::create(['encuesta_id' => $enc1->id, 'orden' => 1, 'texto' => '¿Cómo calificarías el ambiente de trabajo en tu área?', 'tipo' => 'escala', 'requerida' => true]);
        $p2 = EncuestaPregunta::create(['encuesta_id' => $enc1->id, 'orden' => 2, 'texto' => '¿Te sientes valorado/a por tus superiores?', 'tipo' => 'si_no', 'requerida' => true]);
        $p3 = EncuestaPregunta::create(['encuesta_id' => $enc1->id, 'orden' => 3, 'texto' => '¿Cuál es tu principal área de preocupación laboral?', 'tipo' => 'opcion_multiple', 'requerida' => true]);
        $op3 = collect([
            ['orden' => 1, 'texto' => 'Carga de trabajo excesiva'],
            ['orden' => 2, 'texto' => 'Falta de comunicación'],
            ['orden' => 3, 'texto' => 'Recursos insuficientes'],
            ['orden' => 4, 'texto' => 'Relaciones interpersonales'],
            ['orden' => 5, 'texto' => 'Condiciones físicas del ambiente'],
        ])->map(fn($o) => $p3->opciones()->create($o));

        $p4 = EncuestaPregunta::create(['encuesta_id' => $enc1->id, 'orden' => 4, 'texto' => '¿Cuáles aspectos positivos destacarías? (puedes elegir varios)', 'tipo' => 'seleccion_multiple', 'requerida' => false]);
        $op4 = collect([
            ['orden' => 1, 'texto' => 'Compañerismo'],
            ['orden' => 2, 'texto' => 'Liderazgo directivo'],
            ['orden' => 3, 'texto' => 'Estabilidad laboral'],
            ['orden' => 4, 'texto' => 'Capacitaciones brindadas'],
        ])->map(fn($o) => $p4->opciones()->create($o));

        $p5 = EncuestaPregunta::create(['encuesta_id' => $enc1->id, 'orden' => 5, 'texto' => '¿Recomendarías la UGEL como lugar de trabajo?', 'tipo' => 'verdadero_falso', 'requerida' => true]);
        $p6 = EncuestaPregunta::create(['encuesta_id' => $enc1->id, 'orden' => 6, 'texto' => '¿Qué mejoras propones para el área de trabajo?', 'tipo' => 'texto_libre', 'requerida' => false]);

        // Crear encuesta_respuestas y simular respuestas de 8 usuarios
        foreach ($usuarios->take(8) as $i => $usuario) {
            $respuesta = EncuestaRespuesta::create([
                'encuesta_id' => $enc1->id,
                'usuario_id'  => $usuario->id,
                'completada'  => true,
                'iniciada_at' => now()->subDays(rand(1, 6)),
                'completada_at' => now()->subDays(rand(0, 5)),
            ]);

            // P1: escala
            EncuestaRespuestaDetalle::create(['respuesta_id' => $respuesta->id, 'pregunta_id' => $p1->id, 'texto_respuesta' => (string)(($i % 5) + 1)]);
            // P2: si_no
            EncuestaRespuestaDetalle::create(['respuesta_id' => $respuesta->id, 'pregunta_id' => $p2->id, 'texto_respuesta' => $i % 3 === 0 ? 'no' : 'si']);
            // P3: opcion_multiple
            EncuestaRespuestaDetalle::create(['respuesta_id' => $respuesta->id, 'pregunta_id' => $p3->id, 'opcion_id' => $op3[$i % 5]->id]);
            // P4: seleccion_multiple (2 opciones por usuario)
            EncuestaRespuestaDetalle::create(['respuesta_id' => $respuesta->id, 'pregunta_id' => $p4->id, 'opcion_id' => $op4[$i % 4]->id]);
            EncuestaRespuestaDetalle::create(['respuesta_id' => $respuesta->id, 'pregunta_id' => $p4->id, 'opcion_id' => $op4[($i + 1) % 4]->id]);
            // P5: verdadero_falso
            EncuestaRespuestaDetalle::create(['respuesta_id' => $respuesta->id, 'pregunta_id' => $p5->id, 'texto_respuesta' => $i % 4 === 2 ? 'falso' : 'verdadero']);
            // P6: texto libre (solo algunos)
            if ($i % 2 === 0) {
                $textos = [
                    'Mejorar la ventilación de las oficinas y ampliar el espacio de trabajo.',
                    'Se necesita más equipos de cómputo actualizados para el área.',
                    'Mayor comunicación entre las áreas y reuniones de coordinación mensuales.',
                    'Implementar un programa de reconocimiento al personal destacado.',
                ];
                EncuestaRespuestaDetalle::create(['respuesta_id' => $respuesta->id, 'pregunta_id' => $p6->id, 'texto_respuesta' => $textos[$i % 4]]);
            }
        }

        // 4 usuarios sin responder aún
        foreach ($usuarios->slice(8, 4) as $usuario) {
            EncuestaRespuesta::create([
                'encuesta_id' => $enc1->id,
                'usuario_id'  => $usuario->id,
                'completada'  => false,
                'iniciada_at' => null,
            ]);
        }

        // ══════════════════════════════════════════════════
        // ENCUESTA 2: Capacitación SCI — PUBLICADA con pocas respuestas
        // ══════════════════════════════════════════════════
        $enc2 = Encuesta::create([
            'titulo'      => 'Evaluación del Taller de Control Interno',
            'descripcion' => 'Evalúa la efectividad del taller de Control Interno realizado en mayo 2026. Tu opinión es fundamental para mejorar futuras capacitaciones.',
            'modulo'      => 'sci',
            'estado'      => 'publicada',
            'fecha_inicio' => now()->subDays(3)->toDateString(),
            'fecha_fin'   => now()->addDays(5)->toDateString(),
            'creado_por'  => $creador->id,
            'published_at' => now()->subDays(3),
        ]);

        EncuestaDestinatario::create(['encuesta_id' => $enc2->id, 'tipo' => 'todos', 'referencia_id' => null]);

        $q1 = EncuestaPregunta::create(['encuesta_id' => $enc2->id, 'orden' => 1, 'texto' => '¿Cómo calificarías la calidad del taller?', 'tipo' => 'escala', 'requerida' => true]);
        $q2 = EncuestaPregunta::create(['encuesta_id' => $enc2->id, 'orden' => 2, 'texto' => '¿Los temas tratados son aplicables a tu trabajo diario?', 'tipo' => 'si_no', 'requerida' => true]);
        $q3 = EncuestaPregunta::create(['encuesta_id' => $enc2->id, 'orden' => 3, 'texto' => '¿Cómo calificarías al facilitador?', 'tipo' => 'desplegable', 'requerida' => true]);
        collect([
            ['orden' => 1, 'texto' => 'Excelente'],
            ['orden' => 2, 'texto' => 'Bueno'],
            ['orden' => 3, 'texto' => 'Regular'],
            ['orden' => 4, 'texto' => 'Deficiente'],
        ])->each(fn($o) => $q3->opciones()->create($o));
        $q3->refresh();

        $q4 = EncuestaPregunta::create(['encuesta_id' => $enc2->id, 'orden' => 4, 'texto' => '¿La duración del taller fue adecuada?', 'tipo' => 'verdadero_falso', 'requerida' => true]);
        $q5 = EncuestaPregunta::create(['encuesta_id' => $enc2->id, 'orden' => 5, 'texto' => '¿Qué temas te gustaría profundizar en futuras capacitaciones?', 'tipo' => 'texto_libre', 'requerida' => false]);

        $respuestasTaller = [
            ['escala' => '5', 'si_no' => 'si', 'desp' => 1, 'vf' => 'verdadero', 'texto' => 'Me gustaría profundizar en la gestión de riesgos operativos.'],
            ['escala' => '4', 'si_no' => 'si', 'desp' => 2, 'vf' => 'verdadero', 'texto' => 'Control de inventarios y bienes estatales.'],
            ['escala' => '3', 'si_no' => 'no', 'desp' => 2, 'vf' => 'falso',     'texto' => null],
        ];

        foreach ($usuarios->take(3) as $i => $usuario) {
            $r = $respuestasTaller[$i];
            $resp = EncuestaRespuesta::create([
                'encuesta_id' => $enc2->id,
                'usuario_id'  => $usuario->id,
                'completada'  => true,
                'iniciada_at' => now()->subDays(2),
                'completada_at' => now()->subDays(1),
            ]);
            EncuestaRespuestaDetalle::create(['respuesta_id' => $resp->id, 'pregunta_id' => $q1->id, 'texto_respuesta' => $r['escala']]);
            EncuestaRespuestaDetalle::create(['respuesta_id' => $resp->id, 'pregunta_id' => $q2->id, 'texto_respuesta' => $r['si_no']]);
            EncuestaRespuestaDetalle::create(['respuesta_id' => $resp->id, 'pregunta_id' => $q3->id, 'opcion_id' => $q3->opciones[$r['desp'] - 1]->id]);
            EncuestaRespuestaDetalle::create(['respuesta_id' => $resp->id, 'pregunta_id' => $q4->id, 'texto_respuesta' => $r['vf']]);
            if ($r['texto']) {
                EncuestaRespuestaDetalle::create(['respuesta_id' => $resp->id, 'pregunta_id' => $q5->id, 'texto_respuesta' => $r['texto']]);
            }
        }

        // Restantes sin responder
        foreach ($usuarios->slice(3, 5) as $usuario) {
            EncuestaRespuesta::create(['encuesta_id' => $enc2->id, 'usuario_id' => $usuario->id, 'completada' => false]);
        }

        // ══════════════════════════════════════════════════
        // ENCUESTA 3: Integridad institucional — CERRADA (histórico)
        // ══════════════════════════════════════════════════
        $enc3 = Encuesta::create([
            'titulo'      => 'Diagnóstico del Modelo de Integridad',
            'descripcion' => 'Diagnóstico participativo sobre el conocimiento y aplicación del Modelo de Integridad en la institución.',
            'modulo'      => 'integridad',
            'estado'      => 'cerrada',
            'fecha_inicio' => now()->subDays(30)->toDateString(),
            'fecha_fin'   => now()->subDays(5)->toDateString(),
            'creado_por'  => $creador->id,
            'published_at' => now()->subDays(30),
        ]);

        EncuestaDestinatario::create(['encuesta_id' => $enc3->id, 'tipo' => 'todos', 'referencia_id' => null]);

        $r1 = EncuestaPregunta::create(['encuesta_id' => $enc3->id, 'orden' => 1, 'texto' => '¿Conoces el Modelo de Integridad de la institución?', 'tipo' => 'si_no', 'requerida' => true]);
        $r2 = EncuestaPregunta::create(['encuesta_id' => $enc3->id, 'orden' => 2, 'texto' => '¿Has recibido capacitación sobre ética e integridad en el último año?', 'tipo' => 'verdadero_falso', 'requerida' => true]);
        $r3 = EncuestaPregunta::create(['encuesta_id' => $enc3->id, 'orden' => 3, 'texto' => '¿Cuál es el nivel de compromiso ético en tu área?', 'tipo' => 'escala', 'requerida' => true]);
        $r4 = EncuestaPregunta::create(['encuesta_id' => $enc3->id, 'orden' => 4, 'texto' => '¿A través de qué medio conociste la política de integridad?', 'tipo' => 'opcion_multiple', 'requerida' => false]);
        $op4r = collect([
            ['orden' => 1, 'texto' => 'Inducción al cargo'],
            ['orden' => 2, 'texto' => 'Capacitación institucional'],
            ['orden' => 3, 'texto' => 'Correo electrónico'],
            ['orden' => 4, 'texto' => 'No la conozco'],
        ])->map(fn($o) => $r4->opciones()->create($o));

        // 10 usuarios completaron esta encuesta (ya cerrada)
        foreach ($usuarios as $i => $usuario) {
            $resp = EncuestaRespuesta::create([
                'encuesta_id' => $enc3->id,
                'usuario_id'  => $usuario->id,
                'completada'  => true,
                'iniciada_at' => now()->subDays(rand(25, 29)),
                'completada_at' => now()->subDays(rand(10, 24)),
            ]);
            EncuestaRespuestaDetalle::create(['respuesta_id' => $resp->id, 'pregunta_id' => $r1->id, 'texto_respuesta' => $i % 5 === 0 ? 'no' : 'si']);
            EncuestaRespuestaDetalle::create(['respuesta_id' => $resp->id, 'pregunta_id' => $r2->id, 'texto_respuesta' => $i % 4 === 3 ? 'falso' : 'verdadero']);
            EncuestaRespuestaDetalle::create(['respuesta_id' => $resp->id, 'pregunta_id' => $r3->id, 'texto_respuesta' => (string)(($i % 5) + 1)]);
            EncuestaRespuestaDetalle::create(['respuesta_id' => $resp->id, 'pregunta_id' => $r4->id, 'opcion_id' => $op4r[$i % 4]->id]);
        }

        // ══════════════════════════════════════════════════
        // ENCUESTA 4: Satisfacción servicios — BORRADOR
        // ══════════════════════════════════════════════════
        $enc4 = Encuesta::create([
            'titulo'      => 'Satisfacción con los Servicios Administrativos',
            'descripcion' => 'Encuesta para medir el nivel de satisfacción con los servicios brindados por las áreas administrativas.',
            'modulo'      => 'sci',
            'estado'      => 'borrador',
            'fecha_inicio' => now()->addDays(2)->toDateString(),
            'fecha_fin'   => now()->addDays(16)->toDateString(),
            'creado_por'  => $creador->id,
            'published_at' => null,
        ]);

        EncuestaDestinatario::create(['encuesta_id' => $enc4->id, 'tipo' => 'todos', 'referencia_id' => null]);

        EncuestaPregunta::create(['encuesta_id' => $enc4->id, 'orden' => 1, 'texto' => '¿Cómo calificarías la atención recibida en el área de RRHH?', 'tipo' => 'escala', 'requerida' => true]);
        EncuestaPregunta::create(['encuesta_id' => $enc4->id, 'orden' => 2, 'texto' => '¿Los plazos de respuesta del área de Logística son adecuados?', 'tipo' => 'si_no', 'requerida' => true]);
        $pDrop = EncuestaPregunta::create(['encuesta_id' => $enc4->id, 'orden' => 3, 'texto' => '¿Con qué frecuencia utilizas los servicios de Tesorería?', 'tipo' => 'desplegable', 'requerida' => true]);
        collect([
            ['orden' => 1, 'texto' => 'Diariamente'],
            ['orden' => 2, 'texto' => 'Semanalmente'],
            ['orden' => 3, 'texto' => 'Mensualmente'],
            ['orden' => 4, 'texto' => 'Eventualmente'],
        ])->each(fn($o) => $pDrop->opciones()->create($o));
        EncuestaPregunta::create(['encuesta_id' => $enc4->id, 'orden' => 4, 'texto' => '¿Tienes sugerencias para mejorar los servicios administrativos?', 'tipo' => 'texto_libre', 'requerida' => false]);

        // Crear alertas para las encuestas publicadas
        $encuestasPublicadas = Encuesta::whereIn('estado', ['publicada'])->get();
        foreach ($encuestasPublicadas as $encuesta) {
            foreach ($usuarios->take(5) as $usuario) {
                Alerta::firstOrCreate(
                    ['usuario_id' => $usuario->id, 'modulo' => 'encuestas', 'titulo' => 'Nueva encuesta: ' . $encuesta->titulo],
                    [
                        'actividad_id'      => null,
                        'unidad_organica_id' => $usuario->unidad_organica_id,
                        'mensaje'           => 'Tienes una encuesta pendiente de responder.' . ($encuesta->fecha_fin ? ' Fecha límite: ' . $encuesta->fecha_fin->format('d/m/Y') : ''),
                        'tipo'              => 'sistema',
                        'prioridad'         => 'media',
                        'leida'             => false,
                        'email_enviado'     => false,
                    ]
                );
            }
        }

        $this->command->info('✅ EncuestaDemoSeeder completado:');
        $this->command->info('   - Encuesta 1: "Clima Laboral" (PUBLICADA) — 8/12 respuestas');
        $this->command->info('   - Encuesta 2: "Taller Control Interno" (PUBLICADA) — 3/8 respuestas');
        $this->command->info('   - Encuesta 3: "Diagnóstico Integridad" (CERRADA) — 12/12 respuestas');
        $this->command->info('   - Encuesta 4: "Servicios Administrativos" (BORRADOR) — sin publicar');
    }
}
