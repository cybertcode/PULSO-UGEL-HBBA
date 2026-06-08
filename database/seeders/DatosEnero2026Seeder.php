<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Distribuye datos de prueba de enero 2026 a junio 2026 (fecha actual).
 * Acción: TRUNCA tablas secundarias y las repopula con fechas distribuidas.
 * Tablas de usuarios/componentes/actividades base se conservan.
 */
class DatosEnero2026Seeder extends Seeder
{
    // IDs reales de la BD
    private array $actIds   = [];
    private array $usrIds   = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17];
    private array $unitIds  = [1,2,3,4,5,6,7,8,9,10];

    // Meses: enero–junio 2026
    private array $meses = [
        ['y'=>2026,'m'=>1,'dias'=>31,'label'=>'Enero 2026'],
        ['y'=>2026,'m'=>2,'dias'=>28,'label'=>'Febrero 2026'],
        ['y'=>2026,'m'=>3,'dias'=>31,'label'=>'Marzo 2026'],
        ['y'=>2026,'m'=>4,'dias'=>30,'label'=>'Abril 2026'],
        ['y'=>2026,'m'=>5,'dias'=>31,'label'=>'Mayo 2026'],
        ['y'=>2026,'m'=>6,'dias'=>7, 'label'=>'Junio 2026'],  // hasta el 7
    ];

    public function run(): void
    {
        $this->actIds = DB::table('actividades')->pluck('id')->toArray();

        // Redistribuir created_at de actividades entre ene-jun
        $this->redistribuirActividades();

        // Limpiar y repoblar tablas secundarias
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('evidencias')->truncate();
        DB::table('alertas')->truncate();
        DB::table('actividad_historial')->truncate();
        DB::table('reconocimientos')->truncate();
        DB::table('historial_ranking')->truncate();
        DB::table('trabajadores_destacados')->truncate();
        DB::table('buenas_practicas')->truncate();
        DB::table('recomendaciones')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->seedEvidencias();
        $this->seedAlertas();
        $this->seedActividadHistorial();
        $this->seedReconocimientosMensuales();
        $this->seedHistorialRanking();
        $this->seedTrabajadoresDestacados();
        $this->seedBuenasPracticas();
        $this->seedRecomendaciones();

        $this->command->info('');
        $this->command->info('✅ Datos de prueba ENE–JUN 2026 generados correctamente.');
    }

    // ── Redistribuir actividades: asignar created_at distribuido ─────────────
    private function redistribuirActividades(): void
    {
        $acts = DB::table('actividades')->orderBy('id')->get();
        $total = count($acts);
        // ~16-17 actividades por mes durante 6 meses
        $porMes = (int)ceil($total / 6);
        $i = 0;
        foreach ($this->meses as $mes) {
            $limite = min($porMes, $total - $i);
            for ($j = 0; $j < $limite && $i < $total; $j++, $i++) {
                $dia = rand(1, $mes['dias']);
                $ts  = Carbon::create($mes['y'], $mes['m'], $dia, rand(7,17), rand(0,59))->toDateTimeString();
                DB::table('actividades')->where('id', $acts[$i]->id)->update(['created_at' => $ts, 'updated_at' => $ts]);
            }
        }
        $this->command->info("  → Actividades: created_at redistribuidas ({$total} registros, ene–jun 2026)");
    }

    // ── Evidencias: ~30 por mes ───────────────────────────────────────────────
    private function seedEvidencias(): void
    {
        $tipos  = ['validado','validado','validado','pendiente','rechazado'];
        $titulos = [
            'Acta de reunión de trabajo', 'Informe de avance mensual', 'Cronograma de actividades',
            'Plan de trabajo aprobado', 'Resolución directoral', 'Constancia de capacitación',
            'Fotografías de ejecución', 'Registro de asistencia', 'Informe técnico',
            'Memorando de coordinación', 'Oficio de comunicación', 'Declaración jurada',
            'Reporte de seguimiento', 'Matriz de actividades', 'Evidencia fotográfica',
        ];
        $inserted = 0;
        foreach ($this->meses as $mes) {
            $cantidad = ($mes['m'] == 6) ? 12 : rand(28, 35);
            for ($i = 0; $i < $cantidad; $i++) {
                $dia    = rand(1, $mes['dias']);
                $ts     = Carbon::create($mes['y'], $mes['m'], $dia, rand(8,17), rand(0,59))->toDateTimeString();
                $actId  = $this->actIds[array_rand($this->actIds)];
                $subeId = $this->usrIds[array_rand($this->usrIds)];
                $estado = $tipos[array_rand($tipos)];
                $titulo = $titulos[array_rand($titulos)];
                $valId  = $estado === 'validado' ? $this->usrIds[array_rand($this->usrIds)] : null;
                $valAt  = $estado === 'validado' ? Carbon::parse($ts)->addDays(rand(1,5))->toDateTimeString() : null;
                $mes0   = str_pad($mes['m'],2,'0',STR_PAD_LEFT);
                $sgd    = 'SGD-' . $mes['y'] . '-' . str_pad(rand(1000,9999),4,'0',STR_PAD_LEFT);

                DB::table('evidencias')->insert([
                    'actividad_id'   => $actId,
                    'subido_por'     => $subeId,
                    'numero_sgd'     => $sgd,
                    'titulo'         => $titulo,
                    'descripcion'    => "Documento de soporte para actividad registrado en {$mes['label']}.",
                    'archivo_ruta'   => "evidencias/{$mes['y']}/{$mes0}/" . uniqid() . ".pdf",
                    'archivo_nombre' => strtolower(str_replace(' ','-',$titulo)) . '.pdf',
                    'archivo_tipo'   => 'application/pdf',
                    'archivo_tamanio'=> rand(100000, 5000000),
                    'estado'         => $estado,
                    'validado_por'   => $valId,
                    'validado_at'    => $valAt,
                    'motivo_rechazo' => $estado === 'rechazado' ? 'Documento incompleto o ilegible.' : null,
                    'created_at'     => $ts,
                    'updated_at'     => $ts,
                    'deleted_at'     => null,
                ]);
                $inserted++;
            }
        }
        $this->command->info("  → Evidencias: {$inserted} registros (ene–jun 2026)");
    }

    // ── Alertas: ~25 por mes ─────────────────────────────────────────────────
    private function seedAlertas(): void
    {
        $tipos = ['vencimiento','avance_bajo','evidencia_falta','sistema'];
        $tplTitulos = [
            'vencimiento'    => ['Actividad próxima a vencer','Actividad vencida sin completar','Fecha límite en 3 días'],
            'avance_bajo'    => ['Actividad con avance crítico','Sin actualización de avance','Avance por debajo del 20%'],
            'evidencia_falta'=> ['Evidencia pendiente de subir','Falta evidencia para actividad','Evidencia no cargada'],
            'sistema'        => ['Notificación del sistema','Actualización de estado registrada','Actividad completada'],
        ];
        $prioridades = ['alta','alta','media','media','baja'];

        $nombreActs = [
            'Capacitar en uso del SIAF y SIGA',
            'Actualizar el plan de contingencia',
            'Elaborar el mapa de procesos críticos',
            'Elaborar el diagnóstico del SCI',
            'Revisar contratos y convenios',
            'Implementar controles para riesgos',
            'Actualizar la matriz de riesgos',
            'Elaborar el plan de supervisión pedagógica',
            'Realizar auditoría interna',
            'Implementar el archivo documentario',
        ];

        $inserted = 0;
        foreach ($this->meses as $mes) {
            $cantidad = ($mes['m'] == 6) ? 15 : rand(22, 30);
            for ($i = 0; $i < $cantidad; $i++) {
                $dia    = rand(1, $mes['dias']);
                $ts     = Carbon::create($mes['y'], $mes['m'], $dia, rand(7,18), rand(0,59))->toDateTimeString();
                $tipo   = $tipos[array_rand($tipos)];
                $prio   = $prioridades[array_rand($prioridades)];
                $actId  = $this->actIds[array_rand($this->actIds)];
                $uid    = $this->unitIds[array_rand($this->unitIds)];
                $usrId  = $this->usrIds[array_rand($this->usrIds)];
                $actNom = $nombreActs[array_rand($nombreActs)];
                $titulo = $tplTitulos[$tipo][array_rand($tplTitulos[$tipo])];
                $leida  = rand(0,1);

                // Alertas históricas siempre leídas para no violar el constraint único de alertas pendientes
                DB::table('alertas')->insert([
                    'actividad_id'       => $actId,
                    'usuario_id'         => $usrId,
                    'unidad_organica_id' => $uid,
                    'titulo'             => $titulo,
                    'mensaje'            => "La actividad \"{$actNom}\" requiere atención. Registrado en {$mes['label']}.",
                    'tipo'               => $tipo,
                    'prioridad'          => $prio,
                    'leida'              => 1,
                    'leida_at'           => Carbon::parse($ts)->addHours(rand(1,72))->toDateTimeString(),
                    'email_enviado'      => rand(0,1),
                    'email_enviado_at'   => null,
                    'destinatario_email' => null,
                    'created_at'         => $ts,
                    'updated_at'         => $ts,
                ]);
                $inserted++;
            }
        }
        $this->command->info("  → Alertas: {$inserted} registros (ene–jun 2026)");
    }

    // ── Historial de actividades: cambios de avance por mes ──────────────────
    private function seedActividadHistorial(): void
    {
        $inserted = 0;
        // 3-6 cambios por actividad distribuidos entre los meses
        foreach ($this->actIds as $actId) {
            $cambios = rand(3, 6);
            $avPrev  = 0;
            $mesIdx  = 0;
            for ($c = 0; $c < $cambios; $c++) {
                $mes    = $this->meses[$mesIdx % 6];
                $dia    = rand(1, $mes['dias']);
                $ts     = Carbon::create($mes['y'], $mes['m'], $dia, rand(8,17), rand(0,59))->toDateTimeString();
                $avNuevo = min(100, $avPrev + rand(10, 35));
                $usrId  = $this->usrIds[array_rand($this->usrIds)];

                DB::table('actividad_historial')->insert([
                    'actividad_id'  => $actId,
                    'usuario_id'    => $usrId,
                    'campo'         => 'avance',
                    'valor_anterior'=> (string)$avPrev,
                    'valor_nuevo'   => (string)$avNuevo,
                    'descripcion'   => "Avance actualizado al {$avNuevo}% — {$mes['label']}.",
                    'created_at'    => $ts,
                    'updated_at'    => $ts,
                ]);
                $avPrev = $avNuevo;
                $mesIdx++;
                $inserted++;
            }
        }
        $this->command->info("  → Actividad historial: {$inserted} registros (ene–jun 2026)");
    }

    // ── Reconocimientos mensuales: ranking de las 10 unidades por mes ────────
    private function seedReconocimientosMensuales(): void
    {
        // Puntajes base por unidad + variación mensual
        $bases = [1=>88, 2=>82, 3=>91, 4=>79, 5=>85, 6=>87, 7=>74, 8=>83, 9=>76, 10=>69];
        $deltas = [
            ['m'=>1, 'd'=>[1=>+2,  2=>-1, 3=>+4,  4=>-3, 5=>+1,  6=>+3,  7=>-5, 8=>+2,  9=>-2, 10=>-4]],
            ['m'=>2, 'd'=>[1=>+1,  2=>+3, 3=>+2,  4=>-1, 5=>+2,  6=>-2,  7=>-3, 8=>+4,  9=>+1, 10=>-5]],
            ['m'=>3, 'd'=>[1=>+3,  2=>+2, 3=>+5,  4=>+1, 5=>-1,  6=>+2,  7=>-2, 8=>+1,  9=>-3, 10=>-3]],
            ['m'=>4, 'd'=>[1=>+2,  2=>+4, 3=>+3,  4=>+2, 5=>+3,  6=>+1,  7=>-1, 8=>+3,  9=>-1, 10=>-2]],
            ['m'=>5, 'd'=>[1=>+4,  2=>+3, 3=>+6,  4=>+2, 5=>+2,  6=>+3,  7=>+1, 8=>+2,  9=>+0, 10=>-1]],
            ['m'=>6, 'd'=>[1=>+3,  2=>+5, 3=>+5,  4=>+3, 5=>+4,  6=>+2,  7=>+2, 8=>+3,  9=>+1, 10=>+0]],
        ];

        $medallas = [1=>'oro', 2=>'plata', 3=>'bronce'];
        $inserted = 0;

        foreach ($this->meses as $idx => $mes) {
            $puntajes = [];
            foreach ($this->unitIds as $uid) {
                $puntajes[$uid] = min(99, max(55, $bases[$uid] + $deltas[$idx]['d'][$uid] + rand(-1,2)));
            }
            arsort($puntajes);
            $pos = 1;
            $ts  = Carbon::create($mes['y'], $mes['m'], $mes['dias'])->toDateTimeString();
            foreach ($puntajes as $uid => $pts) {
                $actTotal = DB::table('actividades')->where('unidad_organica_id', $uid)->count() ?: 10;
                $actComp  = (int)round($actTotal * ($pts / 100));
                DB::table('reconocimientos')->insert([
                    'unidad_organica_id'      => $uid,
                    'anio'                    => (string)$mes['y'],
                    'mes'                     => $mes['m'],
                    'posicion'                => $pos,
                    'puntaje'                 => $pts,
                    'avance_global'           => $pts,
                    'actividades_total'       => $actTotal,
                    'actividades_completadas' => $actComp,
                    'medalla'                 => $medallas[$pos] ?? null,
                    'observaciones'           => null,
                    'created_at'              => $ts,
                    'updated_at'              => $ts,
                ]);
                $pos++;
                $inserted++;
            }
        }
        $this->command->info("  → Reconocimientos: {$inserted} registros (ene–jun 2026, 10 unidades/mes)");
    }

    // ── Historial ranking ─────────────────────────────────────────────────────
    private function seedHistorialRanking(): void
    {
        $bases = [1=>88, 2=>82, 3=>91, 4=>79, 5=>85, 6=>87, 7=>74, 8=>83, 9=>76, 10=>69];
        $inserted = 0;

        foreach ($this->meses as $mes) {
            $puntajes = [];
            foreach ($this->unitIds as $uid) {
                $puntajes[$uid] = min(99, max(55, $bases[$uid] + rand(-3, 5)));
            }
            arsort($puntajes);
            $pos = 1;
            $ts  = Carbon::create($mes['y'], $mes['m'], $mes['dias'])->toDateTimeString();
            foreach ($puntajes as $uid => $pts) {
                DB::table('historial_ranking')->insert([
                    'unidad_organica_id' => $uid,
                    'posicion'           => $pos,
                    'posicion_anterior'  => rand(1,10),
                    'porcentaje'         => $pts,
                    'anio'               => $mes['y'],
                    'mes'                => $mes['m'],
                    'created_at'         => $ts,
                    'updated_at'         => $ts,
                ]);
                $pos++;
                $inserted++;
            }
        }
        $this->command->info("  → Historial ranking: {$inserted} registros (ene–jun 2026)");
    }

    // ── Trabajadores destacados: 2 por mes ────────────────────────────────────
    private function seedTrabajadoresDestacados(): void
    {
        $trabajadores = [
            // Enero
            ['mes'=>1,'uid'=>6,'nom'=>'Sandra Milagros León Coronado','cargo'=>'Contadora Pública','dni'=>'47231896',
             'correo'=>'s.leon@ugel.gob.pe','cat'=>'Control Interno','resol'=>'RD N° 0043-2026-UGEL-HCB',
             'motivo'=>'Implementación del sistema de control de caja chica con cero observaciones en auditoría de enero.',
             'pc'=>96,'pp'=>94,'ppar'=>90,'pr'=>92,'ts'=>'2026-01-30 17:00:00'],
            ['mes'=>1,'uid'=>3,'nom'=>'Pedro Antonio Huanca Mamani','cargo'=>'Especialista Pedagógico','dni'=>'43981234',
             'correo'=>'p.huanca@ugel.gob.pe','cat'=>'Modelo de Integridad','resol'=>'RD N° 0044-2026-UGEL-HCB',
             'motivo'=>'Socialización del Código de Ética con 12 instituciones educativas visitadas en enero.',
             'pc'=>91,'pp'=>89,'ppar'=>93,'pr'=>90,'ts'=>'2026-01-30 17:00:00'],
            // Febrero
            ['mes'=>2,'uid'=>8,'nom'=>'Rosa Isabel Vargas Tarazona','cargo'=>'Especialista en Recursos Humanos','dni'=>'45678123',
             'correo'=>'r.vargas@ugel.gob.pe','cat'=>'Control Interno','resol'=>'RD N° 0089-2026-UGEL-HCB',
             'motivo'=>'Regularización al 100% de legajos de personal y actualización del escalafón en tiempo récord.',
             'pc'=>94,'pp'=>91,'ppar'=>89,'pr'=>93,'ts'=>'2026-02-27 17:00:00'],
            ['mes'=>2,'uid'=>2,'nom'=>'Jorge Luis Ramírez Castillo','cargo'=>'Jefe de Administración','dni'=>'42315678',
             'correo'=>'j.ramirez@ugel.gob.pe','cat'=>'Gestión Institucional','resol'=>'RD N° 0090-2026-UGEL-HCB',
             'motivo'=>'Implementación del sistema de citas previas reduciendo tiempos de espera en 60%.',
             'pc'=>90,'pp'=>88,'ppar'=>87,'pr'=>91,'ts'=>'2026-02-27 17:00:00'],
            // Marzo
            ['mes'=>3,'uid'=>5,'nom'=>'Ana Lucía Torres Espinoza','cargo'=>'Asesora Legal','dni'=>'46123789',
             'correo'=>'a.torres@ugel.gob.pe','cat'=>'Integridad','resol'=>'RD N° 0142-2026-UGEL-HCB',
             'motivo'=>'Elaboración del plan de gestión de riesgos de corrupción con 12 procesos críticos identificados.',
             'pc'=>95,'pp'=>93,'ppar'=>88,'pr'=>94,'ts'=>'2026-03-31 17:00:00'],
            ['mes'=>3,'uid'=>9,'nom'=>'Juan Carlos Soto Benites','cargo'=>'Tesorero','dni'=>'44567890',
             'correo'=>'j.soto@ugel.gob.pe','cat'=>'Control Interno','resol'=>'RD N° 0143-2026-UGEL-HCB',
             'motivo'=>'Estandarización del proceso de viáticos reduciendo tiempo de procesamiento de 5 a 2 días hábiles.',
             'pc'=>89,'pp'=>92,'ppar'=>85,'pr'=>90,'ts'=>'2026-03-31 17:00:00'],
            // Abril
            ['mes'=>4,'uid'=>3,'nom'=>'Juan Carlos Atoche Llontop','cargo'=>'Especialista en Gestión Pedagógica','dni'=>'45231876',
             'correo'=>'j.atoche@ugel.gob.pe','cat'=>'Control Interno','resol'=>'RD N° 0198-2026-UGEL-HCB',
             'motivo'=>'Cumplimiento excepcional del Plan de Control Interno del Área Pedagógica con 98% de actividades completadas.',
             'pc'=>95,'pp'=>92,'ppar'=>88,'pr'=>90,'ts'=>'2026-04-30 17:00:00'],
            ['mes'=>4,'uid'=>7,'nom'=>'Luis Alberto Quispe Mamani','cargo'=>'Jefe de Logística','dni'=>'43125698',
             'correo'=>'l.quispe@ugel.gob.pe','cat'=>'Transparencia','resol'=>'RD N° 0199-2026-UGEL-HCB',
             'motivo'=>'Digitalización del 45% del inventario institucional con código de barras durante abril.',
             'pc'=>87,'pp'=>85,'ppar'=>82,'pr'=>88,'ts'=>'2026-04-30 17:00:00'],
            // Mayo
            ['mes'=>5,'uid'=>8,'nom'=>'María Fernanda Rojas Valenzuela','cargo'=>'Especialista en Recursos Humanos','dni'=>'47893214',
             'correo'=>'m.rojas@ugel.gob.pe','cat'=>'Modelo de Integridad','resol'=>'RD N° 0254-2026-UGEL-HCB',
             'motivo'=>'Liderazgo en implementación del Modelo de Integridad y promoción de la ética pública en mayo.',
             'pc'=>90,'pp'=>85,'ppar'=>88,'pr'=>83,'ts'=>'2026-05-31 17:00:00'],
            ['mes'=>5,'uid'=>6,'nom'=>'Patricia Soledad Mejía Sánchez','cargo'=>'Asistente Contable','dni'=>'46789123',
             'correo'=>'p.mejia@ugel.gob.pe','cat'=>'Control Interno','resol'=>'RD N° 0255-2026-UGEL-HCB',
             'motivo'=>'Gestión sin observaciones del proceso de pago de remuneraciones durante mayo 2026.',
             'pc'=>93,'pp'=>91,'ppar'=>86,'pr'=>92,'ts'=>'2026-05-31 17:00:00'],
            // Junio
            ['mes'=>6,'uid'=>3,'nom'=>'Juan Carlos Atoche Llontop','cargo'=>'Especialista en Gestión Pedagógica','dni'=>'45231876',
             'correo'=>'j.atoche@ugel.gob.pe','cat'=>'Control Interno','resol'=>'RD N° 0298-2026-UGEL-HCB',
             'motivo'=>'Cumplimiento excepcional del Plan de Control Interno con 98% de actividades al primer semestre.',
             'pc'=>95,'pp'=>92,'ppar'=>88,'pr'=>90,'ts'=>'2026-06-07 17:00:00'],
            ['mes'=>6,'uid'=>8,'nom'=>'María Fernanda Rojas Valenzuela','cargo'=>'Especialista en Recursos Humanos','dni'=>'47893214',
             'correo'=>'m.rojas@ugel.gob.pe','cat'=>'Modelo de Integridad','resol'=>'RD N° 0299-2026-UGEL-HCB',
             'motivo'=>'Liderazgo en implementación del Modelo de Integridad Institucional y promoción de ética pública.',
             'pc'=>90,'pp'=>85,'ppar'=>88,'pr'=>83,'ts'=>'2026-06-07 17:00:00'],
            ['mes'=>6,'uid'=>6,'nom'=>'Luis Alberto Quispe Mamani','cargo'=>'Contador Público','dni'=>'43125698',
             'correo'=>'l.quispe@ugel.gob.pe','cat'=>'Control Interno','resol'=>'RD N° 0300-2026-UGEL-HCB',
             'motivo'=>'Destacada gestión en implementación de controles internos en el área de Contabilidad.',
             'pc'=>88,'pp'=>82,'ppar'=>80,'pr'=>84,'ts'=>'2026-06-07 17:00:00'],
        ];

        foreach ($trabajadores as $t) {
            DB::table('trabajadores_destacados')->insert([
                'unidad_organica_id'      => $t['uid'],
                'nombre'                  => $t['nom'],
                'cargo'                   => $t['cargo'],
                'dni'                     => $t['dni'],
                'correo'                  => $t['correo'],
                'foto_ruta'               => null,
                'puntaje_cumplimiento'    => $t['pc'],
                'puntaje_puntualidad'     => $t['pp'],
                'puntaje_participacion'   => $t['ppar'],
                'puntaje_responsabilidad' => $t['pr'],
                'anio'                    => '2026',
                'mes'                     => $t['mes'],
                'categoria'               => $t['cat'],
                'motivo'                  => $t['motivo'],
                'numero_resolucion'       => $t['resol'],
                'resolucion_ruta'         => null,
                'activo'                  => true,
                'registrado_por'          => 1,
                'created_at'              => $t['ts'],
                'updated_at'              => $t['ts'],
            ]);
        }
        $this->command->info('  → Trabajadores destacados: ' . count($trabajadores) . ' registros (ene–jun 2026, 2-3/mes)');
    }

    // ── Buenas Prácticas: 14 registros distribuidos ene–jun ──────────────────
    private function seedBuenasPracticas(): void
    {
        $practicas = [
            ['titulo'=>'Implementación del archivo documentario digital','desc'=>'Digitalización y organización del acervo documentario institucional reduciendo tiempo de búsqueda en 70%.','cat'=>'innovacion','uid'=>2,'resp'=>3,'estado'=>'completada','av'=>100,'ini'=>'2026-01-06','fin'=>'2026-02-28','imp'=>'alto','sgd'=>'SGD-001-2026','ts'=>'2026-01-06 08:00:00'],
            ['titulo'=>'Programa de atención al ciudadano con cita previa','desc'=>'Sistema de citas previas para trámites administrativos reduciendo tiempos de espera y mejorando satisfacción del usuario.','cat'=>'gestion','uid'=>2,'resp'=>5,'estado'=>'completada','av'=>100,'ini'=>'2026-01-12','fin'=>'2026-03-15','imp'=>'alto','sgd'=>'SGD-002-2026','ts'=>'2026-01-12 09:30:00'],
            ['titulo'=>'Fortalecimiento de capacidades en control interno','desc'=>'Programa de capacitación continua al personal en SCI con talleres mensuales y evaluación de competencias.','cat'=>'integridad','uid'=>1,'resp'=>2,'estado'=>'completada','av'=>100,'ini'=>'2026-02-03','fin'=>'2026-04-30','imp'=>'alto','sgd'=>'SGD-008-2026','ts'=>'2026-02-03 08:00:00'],
            ['titulo'=>'Portal de transparencia actualizado mensualmente','desc'=>'Actualización mensual oportuna del portal de transparencia con información presupuestal conforme a Ley N° 27806.','cat'=>'transparencia','uid'=>1,'resp'=>4,'estado'=>'en_implementacion','av'=>85,'ini'=>'2026-02-10','fin'=>'2026-12-31','imp'=>'alto','sgd'=>'SGD-012-2026','ts'=>'2026-02-10 10:00:00'],
            ['titulo'=>'Optimización del proceso de viáticos y comisiones','desc'=>'Estandarización del proceso de solicitud, aprobación y rendición de viáticos, reduciendo tiempo de 5 a 2 días.','cat'=>'gestion','uid'=>9,'resp'=>9,'estado'=>'completada','av'=>100,'ini'=>'2026-03-02','fin'=>'2026-04-15','imp'=>'medio','sgd'=>'SGD-018-2026','ts'=>'2026-03-02 08:30:00'],
            ['titulo'=>'Plan de gestión de riesgos de corrupción','desc'=>'Elaboración e implementación del plan de gestión de riesgos identificando 12 procesos críticos con controles preventivos.','cat'=>'integridad','uid'=>5,'resp'=>6,'estado'=>'completada','av'=>100,'ini'=>'2026-03-09','fin'=>'2026-05-30','imp'=>'alto','sgd'=>'SGD-021-2026','ts'=>'2026-03-09 09:00:00'],
            ['titulo'=>'Banco de recursos pedagógicos para docentes rurales','desc'=>'Repositorio digital de materiales educativos contextualizados para II.EE. de zonas rurales de Huacaybamba.','cat'=>'innovacion','uid'=>3,'resp'=>7,'estado'=>'en_implementacion','av'=>70,'ini'=>'2026-04-06','fin'=>'2026-07-31','imp'=>'alto','sgd'=>'SGD-031-2026','ts'=>'2026-04-06 08:00:00'],
            ['titulo'=>'Mesa de partes virtual para trámites documentarios','desc'=>'Mesa de partes virtual que permite ingreso de documentos en línea reduciendo desplazamiento de ciudadanos.','cat'=>'transparencia','uid'=>2,'resp'=>8,'estado'=>'en_implementacion','av'=>60,'ini'=>'2026-04-13','fin'=>'2026-08-31','imp'=>'alto','sgd'=>'SGD-035-2026','ts'=>'2026-04-13 09:30:00'],
            ['titulo'=>'Programa de acompañamiento pedagógico en matemáticas','desc'=>'Acompañamiento docente especializado en matemáticas para II.EE. con bajo rendimiento en ECE, visitas quincenales.','cat'=>'gestion','uid'=>3,'resp'=>10,'estado'=>'en_implementacion','av'=>55,'ini'=>'2026-05-04','fin'=>'2026-11-30','imp'=>'alto','sgd'=>'SGD-045-2026','ts'=>'2026-05-04 08:00:00'],
            ['titulo'=>'Inventario patrimonial con código de barras','desc'=>'Etiquetado con código de barras de todos los bienes institucionales y sistema de escaneo para registros rápidos.','cat'=>'gestion','uid'=>7,'resp'=>11,'estado'=>'en_implementacion','av'=>45,'ini'=>'2026-05-11','fin'=>'2026-09-30','imp'=>'medio','sgd'=>'SGD-052-2026','ts'=>'2026-05-11 09:00:00'],
            ['titulo'=>'Protocolo de atención a denuncias ciudadanas','desc'=>'Protocolo estandarizado para recepción, registro, derivación y seguimiento de denuncias con respuesta máxima en 15 días.','cat'=>'transparencia','uid'=>5,'resp'=>12,'estado'=>'en_implementacion','av'=>40,'ini'=>'2026-06-01','fin'=>'2026-09-30','imp'=>'medio','sgd'=>'SGD-058-2026','ts'=>'2026-06-01 08:00:00'],
            ['titulo'=>'Comité de ética y buen gobierno institucional','desc'=>'Conformación y activación del Comité de Ética con reuniones mensuales y difusión del Código de Ética.','cat'=>'integridad','uid'=>1,'resp'=>2,'estado'=>'pendiente','av'=>15,'ini'=>'2026-06-09','fin'=>'2026-12-31','imp'=>'medio','sgd'=>'SGD-063-2026','ts'=>'2026-06-02 09:00:00'],
            ['titulo'=>'Sistema de indicadores de gestión por unidad orgánica','desc'=>'Tablero de indicadores de desempeño por unidad con metas mensuales y reportes automáticos para la Dirección.','cat'=>'gestion','uid'=>4,'resp'=>13,'estado'=>'pendiente','av'=>10,'ini'=>'2026-06-02','fin'=>'2026-10-31','imp'=>'alto','sgd'=>'SGD-060-2026','ts'=>'2026-06-02 08:30:00'],
            ['titulo'=>'Participación ciudadana en presupuesto participativo','desc'=>'Involucramiento de comunidad educativa en priorización de necesidades institucionales y seguimiento de inversiones.','cat'=>'participacion','uid'=>4,'resp'=>5,'estado'=>'pendiente','av'=>5,'ini'=>'2026-06-07','fin'=>'2026-11-30','imp'=>'medio','sgd'=>null,'ts'=>'2026-06-07 10:00:00'],
        ];

        foreach ($practicas as $p) {
            DB::table('buenas_practicas')->insert([
                'titulo'             => $p['titulo'],
                'descripcion'        => $p['desc'],
                'categoria'          => $p['cat'],
                'unidad_organica_id' => $p['uid'],
                'responsable_id'     => $p['resp'],
                'estado'             => $p['estado'],
                'avance'             => $p['av'],
                'fecha_inicio'       => $p['ini'],
                'fecha_termino'      => $p['fin'],
                'numero_sgd'         => $p['sgd'],
                'impacto'            => $p['imp'],
                'evidencias'         => null,
                'observaciones'      => null,
                'creado_por'         => 1,
                'created_at'         => $p['ts'],
                'updated_at'         => $p['ts'],
                'deleted_at'         => null,
            ]);
        }
        $this->command->info('  → Buenas prácticas: ' . count($practicas) . ' registros (ene–jun 2026)');
    }

    // ── Recomendaciones: 13 registros distribuidos ene–jun ───────────────────
    private function seedRecomendaciones(): void
    {
        $recomendaciones = [
            ['titulo'=>'Actualizar procedimiento de rendición de cuentas de caja chica','desc'=>'El procedimiento no contempla cambios normativos de la Directiva N° 005-2023-EF/43.01. Actualizar en 30 días.','tipo'=>'recomendacion','act'=>4,'uid'=>6,'resp'=>9,'estado'=>'atendida','prio'=>'alta','emision'=>'2026-01-15','limite'=>'2026-02-15','atencion'=>'2026-02-10','sgd'=>'SGD-004-2026','origen'=>'OCI','obs'=>'Procedimiento actualizado mediante RD. Personal de Tesorería capacitado.','ts'=>'2026-01-15 09:00:00'],
            ['titulo'=>'Regularizar contratos de locadores vencidos al 31/12/2025','desc'=>'8 contratos con vigencia vencida continúan prestando servicios sin renovación. Riesgo legal y presupuestal.','tipo'=>'observacion','act'=>5,'uid'=>8,'resp'=>3,'estado'=>'atendida','prio'=>'alta','emision'=>'2026-01-20','limite'=>'2026-01-31','atencion'=>'2026-01-28','sgd'=>'SGD-006-2026','origen'=>'Auditoría','obs'=>'8 contratos renovados. Alerta de vencimientos implementada en calendario.','ts'=>'2026-01-20 10:30:00'],
            ['titulo'=>'Implementar libro de reclamaciones en formato digital','desc'=>'La UGEL no cuenta con libro de reclamaciones digital conforme DS N° 042-2011-PCM. Habilitar formulario web y QR.','tipo'=>'recomendacion','act'=>null,'uid'=>2,'resp'=>5,'estado'=>'atendida','prio'=>'media','emision'=>'2026-02-05','limite'=>'2026-03-31','atencion'=>'2026-03-20','sgd'=>'SGD-010-2026','origen'=>'SCI','obs'=>'Libro digital habilitado. Código QR instalado en mesa de partes y secretaría.','ts'=>'2026-02-05 08:00:00'],
            ['titulo'=>'Actualizar matriz de riesgos del área de Logística','desc'=>'La matriz no incluye riesgos de contratación directa y emergencias. Requiere actualización inmediata.','tipo'=>'mejora','act'=>7,'uid'=>7,'resp'=>11,'estado'=>'atendida','prio'=>'media','emision'=>'2026-02-18','limite'=>'2026-03-30','atencion'=>'2026-03-25','sgd'=>'SGD-015-2026','origen'=>'SCI','obs'=>'Matriz actualizada e incorporada al plan de gestión de riesgos institucional.','ts'=>'2026-02-18 09:00:00'],
            ['titulo'=>'Subsanar deficiencias en archivo de expedientes de personal','desc'=>'23 legajos sin documentación completa. El 35% de expedientes presenta folios desactualizados.','tipo'=>'observacion','act'=>null,'uid'=>8,'resp'=>4,'estado'=>'atendida','prio'=>'alta','emision'=>'2026-03-05','limite'=>'2026-04-30','atencion'=>'2026-04-22','sgd'=>'SGD-020-2026','origen'=>'Auditoría','obs'=>'21 de 23 legajos regularizados. 2 casos en trámite con declaración jurada provisional.','ts'=>'2026-03-05 08:30:00'],
            ['titulo'=>'Establecer indicadores de calidad para supervisión pedagógica','desc'=>'El área no cuenta con indicadores cuantitativos para medir efectividad de visitas de supervisión.','tipo'=>'mejora','act'=>8,'uid'=>3,'resp'=>7,'estado'=>'en_proceso','prio'=>'media','emision'=>'2026-03-20','limite'=>'2026-06-30','atencion'=>null,'sgd'=>'SGD-025-2026','origen'=>'SCI','obs'=>'Ficha en elaboración. Primera versión revisada por equipo pedagógico el 15/05/2026.','ts'=>'2026-03-20 09:00:00'],
            ['titulo'=>'Formalizar delegación de facultades mediante acto administrativo','desc'=>'Delegación de funciones al jefe de administración se realiza verbalmente. Debe formalizarse con RD conforme Art. 72 Ley N° 27444.','tipo'=>'observacion','act'=>null,'uid'=>1,'resp'=>2,'estado'=>'atendida','prio'=>'alta','emision'=>'2026-04-08','limite'=>'2026-04-30','atencion'=>'2026-04-25','sgd'=>'SGD-033-2026','origen'=>'OCI','obs'=>'Emitida RD N° 142-2026-DRE-UGEL-HCB formalizando delegación.','ts'=>'2026-04-08 08:00:00'],
            ['titulo'=>'Implementar control de asistencia biométrico','desc'=>'Sistema manual de asistencia presenta riesgos de error. Se recomienda sistema biométrico para mayor confiabilidad.','tipo'=>'mejora','act'=>6,'uid'=>8,'resp'=>4,'estado'=>'en_proceso','prio'=>'media','emision'=>'2026-04-21','limite'=>'2026-07-31','atencion'=>null,'sgd'=>'SGD-038-2026','origen'=>'SCI','obs'=>'Cotización recibida de 3 proveedores. Expediente en preparación.','ts'=>'2026-04-21 10:00:00'],
            ['titulo'=>'Actualizar el plan de continuidad operativa ante desastres','desc'=>'Plan de continuidad del año 2021 no contempla huaycos ni lluvias intensas frecuentes en la región.','tipo'=>'recomendacion','act'=>2,'uid'=>4,'resp'=>13,'estado'=>'en_proceso','prio'=>'alta','emision'=>'2026-05-06','limite'=>'2026-06-30','atencion'=>null,'sgd'=>'SGD-046-2026','origen'=>'DRE','obs'=>'Equipo conformado. Borrador en revisión por Asesoría Jurídica.','ts'=>'2026-05-06 08:00:00'],
            ['titulo'=>'Regularizar actas del Comité de SCI sin firmar','desc'=>'4 actas del Comité de SCI de marzo y abril sin firmar por todos los miembros. Riesgo de validez de acuerdos.','tipo'=>'observacion','act'=>null,'uid'=>1,'resp'=>2,'estado'=>'atendida','prio'=>'media','emision'=>'2026-05-15','limite'=>'2026-05-31','atencion'=>'2026-05-28','sgd'=>'SGD-051-2026','origen'=>'Autocontrol','obs'=>'4 actas regularizadas. Plazo máximo de 5 días para firma de actas futuras.','ts'=>'2026-05-15 09:30:00'],
            ['titulo'=>'Mejorar señalización de zonas de seguridad en instalaciones','desc'=>'Señalética de seguridad desactualizada en piso 2. Rutas de evacuación y extintores requieren nueva señalización NTP 399.010-1.','tipo'=>'observacion','act'=>null,'uid'=>7,'resp'=>11,'estado'=>'pendiente','prio'=>'alta','emision'=>'2026-06-02','limite'=>'2026-06-30','atencion'=>null,'sgd'=>'SGD-059-2026','origen'=>'Auditoría','obs'=>'Requerimiento de materiales presentado a Logística el 04/06/2026.','ts'=>'2026-06-02 08:00:00'],
            ['titulo'=>'Establecer política de seguridad de información institucional','desc'=>'Sin política formal de seguridad. Accesos a SIAF y SIGA no tienen procedimiento de revocación al cese del trabajador.','tipo'=>'recomendacion','act'=>null,'uid'=>2,'resp'=>3,'estado'=>'pendiente','prio'=>'alta','emision'=>'2026-06-05','limite'=>'2026-07-31','atencion'=>null,'sgd'=>'SGD-062-2026','origen'=>'OCI','obs'=>null,'ts'=>'2026-06-05 09:00:00'],
            ['titulo'=>'Estandarizar formato de informes de gestión por unidad','desc'=>'Informes mensuales sin formato uniforme, dificultando consolidación y análisis comparativo. Diseñar plantilla estándar.','tipo'=>'mejora','act'=>3,'uid'=>4,'resp'=>13,'estado'=>'pendiente','prio'=>'baja','emision'=>'2026-06-07','limite'=>'2026-08-31','atencion'=>null,'sgd'=>null,'origen'=>'Autocontrol','obs'=>null,'ts'=>'2026-06-07 11:00:00'],
        ];

        foreach ($recomendaciones as $r) {
            DB::table('recomendaciones')->insert([
                'titulo'             => $r['titulo'],
                'descripcion'        => $r['desc'],
                'tipo'               => $r['tipo'],
                'actividad_id'       => $r['act'],
                'unidad_organica_id' => $r['uid'],
                'responsable_id'     => $r['resp'],
                'estado'             => $r['estado'],
                'prioridad'          => $r['prio'],
                'fecha_emision'      => $r['emision'],
                'fecha_limite'       => $r['limite'],
                'fecha_atencion'     => $r['atencion'],
                'numero_sgd'         => $r['sgd'],
                'origen'             => $r['origen'],
                'observaciones'      => $r['obs'],
                'creado_por'         => 1,
                'created_at'         => $r['ts'],
                'updated_at'         => $r['ts'],
                'deleted_at'         => null,
            ]);
        }
        $this->command->info('  → Recomendaciones: ' . count($recomendaciones) . ' registros (ene–jun 2026)');
    }
}
