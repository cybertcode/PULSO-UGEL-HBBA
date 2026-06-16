<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Encuesta;
use App\Models\EncuestaPregunta;
use App\Models\EncuestaOpcion;
use App\Models\EncuestaDestinatario;
use App\Models\EncuestaRespuesta;
use App\Models\EncuestaRespuestaDetalle;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class EncuestasSeeder extends Seeder
{
    public function run(): void
    {
        EncuestaRespuestaDetalle::query()->delete();
        EncuestaRespuesta::query()->delete();
        EncuestaDestinatario::query()->delete();
        EncuestaOpcion::query()->delete();
        EncuestaPregunta::query()->delete();
        Encuesta::withTrashed()->forceDelete();

        $admin = User::first();
        $adminId = $admin?->id ?? 1;

        // ══════════════════════════════════════════════════════════
        // ENCUESTA 1 — Cerrada con respuestas (para ver resultados)
        // ══════════════════════════════════════════════════════════
        $enc1 = Encuesta::create([
            'titulo'       => 'Evaluación del Sistema de Control Interno 2025',
            'descripcion'  => 'Encuesta de autoevaluación del SCI dirigida a todos los servidores de la UGEL Huacaybamba para medir el nivel de conocimiento e implementación del sistema.',
            'modulo'       => 'sci',
            'estado'       => 'cerrada',
            'fecha_inicio' => '2025-03-01',
            'fecha_fin'    => '2025-03-31',
            'creado_por'   => $adminId,
            'published_at' => '2025-03-01',
        ]);

        $p1 = $enc1->preguntas()->create(['orden' => 1, 'texto' => '¿Conoce usted los objetivos del Sistema de Control Interno (SCI) de la UGEL Huacaybamba?', 'tipo' => 'opcion_multiple', 'requerida' => true]);
        $p1->opciones()->createMany([
            ['orden' => 1, 'texto' => 'Sí, los conozco completamente'],
            ['orden' => 2, 'texto' => 'Los conozco parcialmente'],
            ['orden' => 3, 'texto' => 'Los conozco muy poco'],
            ['orden' => 4, 'texto' => 'No los conozco'],
        ]);

        $p2 = $enc1->preguntas()->create(['orden' => 2, 'texto' => '¿Ha participado en alguna capacitación sobre Control Interno en el último año?', 'tipo' => 'si_no', 'requerida' => true]);

        $p3 = $enc1->preguntas()->create(['orden' => 3, 'texto' => '¿Qué componentes del SCI conoce o aplica en su trabajo diario?', 'tipo' => 'seleccion_multiple', 'requerida' => false]);
        $p3->opciones()->createMany([
            ['orden' => 1, 'texto' => 'Ambiente de control'],
            ['orden' => 2, 'texto' => 'Evaluación de riesgos'],
            ['orden' => 3, 'texto' => 'Actividades de control'],
            ['orden' => 4, 'texto' => 'Información y comunicación'],
            ['orden' => 5, 'texto' => 'Supervisión'],
        ]);

        $p4 = $enc1->preguntas()->create(['orden' => 4, 'texto' => 'En una escala del 1 al 5, ¿cómo califica la implementación del SCI en su unidad orgánica?', 'tipo' => 'escala', 'requerida' => true]);

        $p5 = $enc1->preguntas()->create(['orden' => 5, 'texto' => '¿Considera que el SCI contribuye a mejorar la gestión institucional?', 'tipo' => 'verdadero_falso', 'requerida' => true]);

        $p6 = $enc1->preguntas()->create(['orden' => 6, 'texto' => '¿Qué sugerencias tiene para mejorar la implementación del SCI en la UGEL?', 'tipo' => 'texto_libre', 'requerida' => false]);

        // Destinatario: todos
        $enc1->destinatarios()->create(['tipo' => 'todos', 'referencia_id' => null]);

        // Simular respuestas de 8 usuarios
        $usuarios = User::where('estado', 'activo')->take(8)->get();
        $opc1Ids = $p1->opciones->pluck('id')->toArray();
        $opc3Ids = $p3->opciones->pluck('id')->toArray();

        $respuestasSimuladas = [
            [0, true, [0,2], 4, true,  'Mejorar la comunicación interna sobre avances del SCI.'],
            [1, true, [0,1,3], 5, true, 'Realizar más talleres prácticos con los responsables.'],
            [1, false, [2], 3, true,  ''],
            [2, true, [0,1,2,3,4], 5, true, 'Muy buena iniciativa, seguir capacitando al personal.'],
            [0, true, [1,3], 4, false, 'Falta socialización en algunas unidades.'],
            [3, false, [], 2, false, 'Se necesita más apoyo de la dirección.'],
            [1, true, [0,2,4], 4, true, 'El sistema ha mejorado bastante este año.'],
            [0, true, [0,1], 3, true,  ''],
        ];

        foreach ($usuarios as $i => $usuario) {
            if (!isset($respuestasSimuladas[$i])) break;
            $r = $respuestasSimuladas[$i];

            $respuesta = EncuestaRespuesta::create([
                'encuesta_id'   => $enc1->id,
                'usuario_id'    => $usuario->id,
                'completada'    => true,
                'iniciada_at'   => Carbon::parse('2025-03-' . str_pad($i + 2, 2, '0', STR_PAD_LEFT)),
                'completada_at' => Carbon::parse('2025-03-' . str_pad($i + 2, 2, '0', STR_PAD_LEFT)),
            ]);

            // P1: opcion_multiple
            EncuestaRespuestaDetalle::create(['respuesta_id' => $respuesta->id, 'pregunta_id' => $p1->id, 'opcion_id' => $opc1Ids[$r[0]] ?? $opc1Ids[0]]);
            // P2: si_no
            EncuestaRespuestaDetalle::create(['respuesta_id' => $respuesta->id, 'pregunta_id' => $p2->id, 'texto_respuesta' => $r[1] ? 'si' : 'no']);
            // P3: seleccion_multiple
            foreach ($r[2] as $opcIdx) {
                if (isset($opc3Ids[$opcIdx])) {
                    EncuestaRespuestaDetalle::create(['respuesta_id' => $respuesta->id, 'pregunta_id' => $p3->id, 'opcion_id' => $opc3Ids[$opcIdx]]);
                }
            }
            // P4: escala
            EncuestaRespuestaDetalle::create(['respuesta_id' => $respuesta->id, 'pregunta_id' => $p4->id, 'texto_respuesta' => (string)$r[3]]);
            // P5: verdadero_falso
            EncuestaRespuestaDetalle::create(['respuesta_id' => $respuesta->id, 'pregunta_id' => $p5->id, 'texto_respuesta' => $r[4] ? 'verdadero' : 'falso']);
            // P6: texto_libre
            if (!empty($r[5])) {
                EncuestaRespuestaDetalle::create(['respuesta_id' => $respuesta->id, 'pregunta_id' => $p6->id, 'texto_respuesta' => $r[5]]);
            }
        }

        // ══════════════════════════════════════════════════════════
        // ENCUESTA 2 — Publicada (pendiente de responder)
        // ══════════════════════════════════════════════════════════
        $enc2 = Encuesta::create([
            'titulo'       => 'Autoevaluación del Modelo de Integridad — Primer Semestre 2026',
            'descripcion'  => 'Evaluación del nivel de implementación de los 5 pilares del Modelo de Integridad en la UGEL Huacaybamba. Dirigida a responsables de unidad y coordinadores.',
            'modulo'       => 'integridad',
            'estado'       => 'publicada',
            'fecha_inicio' => Carbon::now()->subDays(5)->toDateString(),
            'fecha_fin'    => Carbon::now()->addDays(20)->toDateString(),
            'creado_por'   => $adminId,
            'published_at' => Carbon::now()->subDays(5),
        ]);

        $enc2->preguntas()->create(['orden' => 1, 'texto' => '¿Su unidad cuenta con un Plan de Integridad actualizado para el año 2026?', 'tipo' => 'si_no', 'requerida' => true]);

        $p2b = $enc2->preguntas()->create(['orden' => 2, 'texto' => '¿Qué pilares del Modelo de Integridad se han implementado en su unidad?', 'tipo' => 'seleccion_multiple', 'requerida' => true]);
        $p2b->opciones()->createMany([
            ['orden' => 1, 'texto' => 'Compromiso institucional'],
            ['orden' => 2, 'texto' => 'Gestión de conflictos de intereses'],
            ['orden' => 3, 'texto' => 'Transparencia'],
            ['orden' => 4, 'texto' => 'Rendición de cuentas'],
            ['orden' => 5, 'texto' => 'Control interno'],
        ]);

        $p3b = $enc2->preguntas()->create(['orden' => 3, 'texto' => '¿Cómo califica el compromiso de la dirección con la política de integridad?', 'tipo' => 'escala', 'requerida' => true]);

        $p4b = $enc2->preguntas()->create(['orden' => 4, 'texto' => '¿En qué área considera que se necesita mayor fortalecimiento?', 'tipo' => 'opcion_multiple', 'requerida' => true]);
        $p4b->opciones()->createMany([
            ['orden' => 1, 'texto' => 'Capacitación en ética pública'],
            ['orden' => 2, 'texto' => 'Mecanismos de denuncia'],
            ['orden' => 3, 'texto' => 'Transparencia de información'],
            ['orden' => 4, 'texto' => 'Gestión de conflictos de interés'],
        ]);

        $enc2->preguntas()->create(['orden' => 5, 'texto' => '¿Ha identificado algún riesgo de integridad en su área en los últimos 6 meses?', 'tipo' => 'verdadero_falso', 'requerida' => true]);
        $enc2->preguntas()->create(['orden' => 6, 'texto' => 'Describa brevemente las acciones de integridad más destacadas de su unidad este semestre.', 'tipo' => 'texto_libre', 'requerida' => false]);

        // Destinatario: rol Coordinador SCI + Responsable de Unidad
        $rolCoord = Role::where('name', 'Coordinador SCI')->first();
        $rolResp  = Role::where('name', 'Responsable de Unidad')->first();
        if ($rolCoord) $enc2->destinatarios()->create(['tipo' => 'rol', 'referencia_id' => $rolCoord->id]);
        if ($rolResp)  $enc2->destinatarios()->create(['tipo' => 'rol', 'referencia_id' => $rolResp->id]);

        // Crear registros de respuesta (sin completar) para esos usuarios
        $usuariosEnc2 = collect();
        if ($rolCoord) $usuariosEnc2 = $usuariosEnc2->merge(User::role($rolCoord->name)->where('estado','activo')->get());
        if ($rolResp)  $usuariosEnc2 = $usuariosEnc2->merge(User::role($rolResp->name)->where('estado','activo')->get());
        foreach ($usuariosEnc2->unique('id') as $u) {
            EncuestaRespuesta::firstOrCreate(['encuesta_id' => $enc2->id, 'usuario_id' => $u->id], ['completada' => false]);
        }

        // ══════════════════════════════════════════════════════════
        // ENCUESTA 3 — Borrador (en preparación)
        // ══════════════════════════════════════════════════════════
        $enc3 = Encuesta::create([
            'titulo'       => 'Satisfacción con el Sistema PULSO UGEL',
            'descripcion'  => 'Encuesta de satisfacción del usuario sobre el sistema de monitoreo PULSO UGEL, sus módulos y funcionalidades.',
            'modulo'       => 'ambos',
            'estado'       => 'borrador',
            'fecha_inicio' => Carbon::now()->addDays(3)->toDateString(),
            'fecha_fin'    => Carbon::now()->addDays(30)->toDateString(),
            'creado_por'   => $adminId,
        ]);

        $p1c = $enc3->preguntas()->create(['orden' => 1, 'texto' => '¿Con qué frecuencia utiliza el sistema PULSO UGEL?', 'tipo' => 'opcion_multiple', 'requerida' => true]);
        $p1c->opciones()->createMany([
            ['orden' => 1, 'texto' => 'Todos los días'],
            ['orden' => 2, 'texto' => 'Varias veces por semana'],
            ['orden' => 3, 'texto' => 'Una vez por semana'],
            ['orden' => 4, 'texto' => 'Ocasionalmente'],
        ]);

        $enc3->preguntas()->create(['orden' => 2, 'texto' => '¿Cómo califica la facilidad de uso del sistema?', 'tipo' => 'escala', 'requerida' => true]);

        $p3c = $enc3->preguntas()->create(['orden' => 3, 'texto' => '¿Qué módulos utiliza con mayor frecuencia?', 'tipo' => 'seleccion_multiple', 'requerida' => false]);
        $p3c->opciones()->createMany([
            ['orden' => 1, 'texto' => 'Actividades SCI'],
            ['orden' => 2, 'texto' => 'Modelo de Integridad'],
            ['orden' => 3, 'texto' => 'Semáforo institucional'],
            ['orden' => 4, 'texto' => 'Buenas Prácticas'],
            ['orden' => 5, 'texto' => 'Encuestas'],
            ['orden' => 6, 'texto' => 'Normativas'],
        ]);

        $enc3->preguntas()->create(['orden' => 4, 'texto' => '¿El sistema le ayuda a cumplir mejor con sus obligaciones de control interno?', 'tipo' => 'si_no', 'requerida' => true]);
        $enc3->preguntas()->create(['orden' => 5, 'texto' => '¿Qué mejoras o nuevas funciones le gustaría ver en PULSO UGEL?', 'tipo' => 'texto_libre', 'requerida' => false]);

        // Destinatario: todos (se asignará al publicar)
        $enc3->destinatarios()->create(['tipo' => 'todos', 'referencia_id' => null]);

        $this->command->info('✅ EncuestasSeeder: 3 encuestas creadas (1 cerrada con respuestas, 1 publicada, 1 borrador).');
    }
}
