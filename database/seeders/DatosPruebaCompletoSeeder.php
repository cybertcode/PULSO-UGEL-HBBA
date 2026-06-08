<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatosPruebaCompletoSeeder extends Seeder
{
    // Unidades orgánicas reales del sistema
    private array $unidades = [
        1  => ['nombre' => 'Dirección',                    'sigla' => 'DIR'],
        2  => ['nombre' => 'Oficina de Administración',    'sigla' => 'OA'],
        3  => ['nombre' => 'Área de Gestión Pedagógica',   'sigla' => 'AGP'],
        4  => ['nombre' => 'Área de Gestión Institucional','sigla' => 'AGI'],
        5  => ['nombre' => 'Asesoría Jurídica',            'sigla' => 'AJ'],
        6  => ['nombre' => 'Contabilidad',                 'sigla' => 'CONT'],
        7  => ['nombre' => 'Logística',                    'sigla' => 'LOG'],
        8  => ['nombre' => 'Recursos Humanos',             'sigla' => 'RRHH'],
        9  => ['nombre' => 'Tesorería',                    'sigla' => 'TES'],
        10 => ['nombre' => 'Infraestructura',              'sigla' => 'INF'],
    ];

    // Puntajes base por unidad (simulando su desempeño relativo)
    private array $puntajesBase = [
        1 => 88, 2 => 82, 3 => 91, 4 => 79, 5 => 85,
        6 => 87, 7 => 74, 8 => 83, 9 => 76, 10 => 69,
    ];

    private array $actividadesIds;
    private array $usuariosIds = [2,3,4,5,6,7,8,9,10,11,12,13];

    public function run(): void
    {
        $this->actividadesIds = DB::table('actividades')->pluck('id')->toArray();

        $this->seedHistorialRankingFaltante();
        $this->seedReconocimientoJunio2026();
        $this->seedAlertasMesesAnteriores();
        $this->seedActividad_historial();
        $this->seedConfiguracionInstitucional();

        $this->command->info('✅ Datos de prueba completos insertados correctamente.');
    }

    // ── Historial Ranking: enero–abril 2026 (mayo y junio ya existen) ────────
    private function seedHistorialRankingFaltante(): void
    {
        $mesesFaltantes = [
            ['anio' => 2026, 'mes' => 1],
            ['anio' => 2026, 'mes' => 2],
            ['anio' => 2026, 'mes' => 3],
            ['anio' => 2026, 'mes' => 4],
        ];

        // Variaciones mensuales por unidad (delta sobre puntaje base)
        $variaciones = [
            ['anio'=>2026,'mes'=>1, 'deltas'=>[1=>+2, 2=>-1, 3=>+4, 4=>-3, 5=>+1, 6=>+3, 7=>-5, 8=>+2, 9=>-2, 10=>-4]],
            ['anio'=>2026,'mes'=>2, 'deltas'=>[1=>+1, 2=>+3, 3=>+2, 4=>-1, 5=>+2, 6=>-2, 7=>-3, 8=>+4, 9=>+1, 10=>-5]],
            ['anio'=>2026,'mes'=>3, 'deltas'=>[1=>+3, 2=>+2, 3=>+5, 4=>+1, 5=>-1, 6=>+2, 7=>-2, 8=>+1, 9=>-3, 10=>-3]],
            ['anio'=>2026,'mes'=>4, 'deltas'=>[1=>+2, 2=>+4, 3=>+3, 4=>+2, 5=>+3, 6=>+1, 7=>-1, 8=>+3, 9=>-1, 10=>-2]],
        ];

        $inserted = 0;
        foreach ($variaciones as $v) {
            // Verificar que no existe ya
            $existe = DB::table('historial_ranking')
                ->where('anio', $v['anio'])->where('mes', $v['mes'])->exists();
            if ($existe) continue;

            $puntajes = [];
            foreach ($this->unidades as $uid => $u) {
                $puntajes[$uid] = min(100, max(50, $this->puntajesBase[$uid] + $v['deltas'][$uid] + rand(-2, 2)));
            }
            arsort($puntajes);
            $posicion = 1;
            $ts = Carbon::create($v['anio'], $v['mes'], 28)->toDateTimeString();
            foreach ($puntajes as $uid => $puntaje) {
                DB::table('historial_ranking')->insert([
                    'unidad_organica_id' => $uid,
                    'posicion'           => $posicion,
                    'posicion_anterior'  => rand(1, 10),
                    'porcentaje'         => $puntaje,
                    'anio'               => $v['anio'],
                    'mes'                => $v['mes'],
                    'created_at'         => $ts,
                    'updated_at'         => $ts,
                ]);
                $posicion++;
                $inserted++;
            }
        }
        $this->command->info("  → Historial Ranking: {$inserted} registros (ene–abr 2026)");
    }

    // ── Reconocimiento junio 2026 (meses anteriores ya existen) ─────────────
    private function seedReconocimientoJunio2026(): void
    {
        $existe = DB::table('reconocimientos')->where('anio', 2026)->where('mes', 6)->exists();
        if ($existe) {
            $this->command->info('  → Reconocimientos jun 2026: ya existen, omitido.');
            return;
        }

        $datos = [
            ['uid'=>3,  'pos'=>1, 'puntaje'=>93.5, 'completadas'=>9,  'total'=>10, 'medalla'=>'oro'],
            ['uid'=>1,  'pos'=>2, 'puntaje'=>90.0, 'completadas'=>8,  'total'=>10, 'medalla'=>'plata'],
            ['uid'=>6,  'pos'=>3, 'puntaje'=>88.5, 'completadas'=>8,  'total'=>10, 'medalla'=>'bronce'],
            ['uid'=>5,  'pos'=>4, 'puntaje'=>86.0, 'completadas'=>7,  'total'=>10, 'medalla'=>null],
            ['uid'=>8,  'pos'=>5, 'puntaje'=>84.0, 'completadas'=>7,  'total'=>10, 'medalla'=>null],
            ['uid'=>2,  'pos'=>6, 'puntaje'=>82.5, 'completadas'=>7,  'total'=>10, 'medalla'=>null],
            ['uid'=>4,  'pos'=>7, 'puntaje'=>79.0, 'completadas'=>6,  'total'=>10, 'medalla'=>null],
            ['uid'=>9,  'pos'=>8, 'puntaje'=>76.5, 'completadas'=>6,  'total'=>10, 'medalla'=>null],
            ['uid'=>7,  'pos'=>9, 'puntaje'=>73.0, 'completadas'=>5,  'total'=>10, 'medalla'=>null],
            ['uid'=>10, 'pos'=>10,'puntaje'=>68.0, 'completadas'=>4,  'total'=>10, 'medalla'=>null],
        ];

        foreach ($datos as $d) {
            DB::table('reconocimientos')->insert([
                'unidad_organica_id'      => $d['uid'],
                'anio'                    => '2026',
                'mes'                     => 6,
                'posicion'                => $d['pos'],
                'puntaje'                 => $d['puntaje'],
                'avance_global'           => (int)$d['puntaje'],
                'actividades_total'       => $d['total'],
                'actividades_completadas' => $d['completadas'],
                'medalla'                 => $d['medalla'],
                'observaciones'           => null,
                'created_at'              => '2026-06-07 17:00:00',
                'updated_at'              => '2026-06-07 17:00:00',
            ]);
        }
        $this->command->info('  → Reconocimientos jun 2026: 10 registros insertados');
    }

    // ── Alertas: enero–mayo 2026 distribuidas por mes ────────────────────────
    private function seedAlertasMesesAnteriores(): void
    {
        // Tipos válidos del ENUM en BD: vencimiento, evidencia_falta, sistema, avance_bajo
        $tiposYMensajes = [
            ['tipo'=>'vencimiento',    'prioridad'=>'alta',
             'tpl_titulo'=>'Actividad próxima a vencer',
             'tpl_msg'=>'La actividad "%s" de la unidad "%s" vence en menos de 7 días. Avance actual: %d%%.'],
            ['tipo'=>'avance_bajo',    'prioridad'=>'alta',
             'tpl_titulo'=>'Actividad con avance crítico',
             'tpl_msg'=>'La actividad "%s" de "%s" lleva más de 15 días sin avance. Avance actual: %d%%.'],
            ['tipo'=>'evidencia_falta','prioridad'=>'media',
             'tpl_titulo'=>'Evidencia pendiente de carga',
             'tpl_msg'=>'La actividad "%s" de "%s" tiene avance de %d%% pero no cuenta con evidencia adjunta.'],
            ['tipo'=>'sistema',        'prioridad'=>'baja',
             'tpl_titulo'=>'Notificación del sistema',
             'tpl_msg'=>'La actividad "%s" de "%s" fue actualizada con %d%% de avance. Verifique el estado.'],
            ['tipo'=>'vencimiento',    'prioridad'=>'media',
             'tpl_titulo'=>'Actividad vencida sin completar',
             'tpl_msg'=>'La actividad "%s" de "%s" superó su fecha límite con %d%% de avance. Requiere atención inmediata.'],
        ];

        $nombres = [
            'Capacitar en uso del SIAF y SIGA',
            'Actualizar el plan de contingencia institucional',
            'Elaborar el mapa de procesos críticos',
            'Elaborar el diagnóstico del sistema de control interno',
            'Revisar contratos y convenios vigentes',
            'Implementar controles para riesgos identificados',
            'Actualizar la matriz de riesgos institucional',
            'Elaborar el plan de supervisión pedagógica',
            'Realizar auditoría interna de procesos administrativos',
            'Implementar el sistema de archivo documentario',
        ];

        $meses = [
            ['anio'=>2026,'mes'=>1,'dias'=>31],
            ['anio'=>2026,'mes'=>2,'dias'=>28],
            ['anio'=>2026,'mes'=>3,'dias'=>31],
            ['anio'=>2026,'mes'=>4,'dias'=>30],
            ['anio'=>2026,'mes'=>5,'dias'=>31],
        ];

        $inserted = 0;
        $actIdx = 0;
        foreach ($meses as $m) {
            // 12–18 alertas por mes
            $cantidad = rand(12, 18);
            for ($i = 0; $i < $cantidad; $i++) {
                $tpl   = $tiposYMensajes[array_rand($tiposYMensajes)];
                $uid   = array_rand($this->unidades) ;
                $unom  = $this->unidades[$uid]['nombre'];
                $actId = $this->actividadesIds[$actIdx % count($this->actividadesIds)];
                $actNom= $nombres[$actIdx % count($nombres)];
                $avance= rand(10, 95);
                $dia   = rand(1, $m['dias']);
                $ts    = Carbon::create($m['anio'], $m['mes'], $dia, rand(7,17), rand(0,59))->toDateTimeString();
                $usrId = $this->usuariosIds[array_rand($this->usuariosIds)];

                DB::table('alertas')->insert([
                    'actividad_id'      => $actId,
                    'usuario_id'        => $usrId,
                    'unidad_organica_id'=> $uid,
                    'titulo'            => $tpl['tpl_titulo'],
                    'mensaje'           => sprintf($tpl['tpl_msg'], $actNom, $unom, $avance),
                    'tipo'              => $tpl['tipo'],
                    'prioridad'         => $tpl['prioridad'],
                    'leida'             => rand(0, 1),
                    'leida_at'          => rand(0,1) ? Carbon::parse($ts)->addHours(rand(1,48))->toDateTimeString() : null,
                    'email_enviado'     => rand(0, 1),
                    'email_enviado_at'  => null,
                    'destinatario_email'=> null,
                    'created_at'        => $ts,
                    'updated_at'        => $ts,
                ]);
                $actIdx++;
                $inserted++;
            }
        }
        $this->command->info("  → Alertas ene–may 2026: {$inserted} registros insertados");
    }

    // ── Actividad historial: registros de cambio de estado ───────────────────
    private function seedActividad_historial(): void
    {
        // Verificar si ya tiene datos
        $existe = DB::table('actividad_historial')->count();
        if ($existe > 10) {
            $this->command->info("  → Actividad historial: ya tiene {$existe} registros, omitido.");
            return;
        }

        $cols = \Illuminate\Support\Facades\Schema::getColumnListing('actividad_historial');

        // Solo si tiene las columnas esperadas
        if (!in_array('actividad_id', $cols)) {
            $this->command->warn('  → actividad_historial: estructura desconocida, omitido.');
            return;
        }

        $estados = ['pendiente','en_proceso','completada','observada','vencida'];
        $inserted = 0;

        // Tomar 30 actividades y crear 2-4 registros de historial cada una
        $acts = DB::table('actividades')->select('id','estado','avance','unidad_organica_id')->take(30)->get();

        foreach ($acts as $act) {
            $pasos = rand(2, 4);
            $fecha = Carbon::create(2026, 1, rand(5, 20));
            $avancePrev = 0;

            for ($i = 0; $i < $pasos; $i++) {
                $avanceNuevo = min(100, $avancePrev + rand(10, 30));
                $estadoNuevo = $i === $pasos - 1 ? $act->estado : $estados[array_rand(['pendiente','en_proceso'])];
                $usrId = $this->usuariosIds[array_rand($this->usuariosIds)];

                $row = ['actividad_id' => $act->id, 'created_at' => $fecha->toDateTimeString(), 'updated_at' => $fecha->toDateTimeString()];

                if (in_array('usuario_id', $cols))       $row['usuario_id']       = $usrId;
                if (in_array('avance_anterior', $cols))  $row['avance_anterior']  = $avancePrev;
                if (in_array('avance_nuevo', $cols))     $row['avance_nuevo']     = $avanceNuevo;
                if (in_array('estado_anterior', $cols))  $row['estado_anterior']  = $i === 0 ? 'pendiente' : $estados[$i % count($estados)];
                if (in_array('estado_nuevo', $cols))     $row['estado_nuevo']     = $estadoNuevo;
                if (in_array('observacion', $cols))      $row['observacion']      = $i === 0 ? 'Inicio de actividad registrado.' : 'Avance actualizado según informe mensual.';
                if (in_array('descripcion', $cols))      $row['descripcion']      = 'Actualización de avance mensual.';
                if (in_array('tipo', $cols))             $row['tipo']             = 'avance';

                try {
                    DB::table('actividad_historial')->insert($row);
                    $inserted++;
                } catch (\Exception $e) {
                    // columna incompatible, continuar
                }

                $avancePrev = $avanceNuevo;
                $fecha->addDays(rand(7, 21));
            }
        }
        $this->command->info("  → Actividad historial: {$inserted} registros insertados");
    }

    // ── Configuración institucional: asegurar que existe un registro ─────────
    private function seedConfiguracionInstitucional(): void
    {
        $cols = \Illuminate\Support\Facades\Schema::getColumnListing('configuracion_institucional');
        $existe = DB::table('configuracion_institucional')->count();
        if ($existe > 0) {
            $this->command->info('  → Configuración institucional: ya existe, omitido.');
            return;
        }

        $row = [
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];

        if (in_array('nombre_institucion', $cols))  $row['nombre_institucion']  = 'UGEL Huacaybamba';
        if (in_array('sigla', $cols))               $row['sigla']               = 'UGEL HCB';
        if (in_array('ruc', $cols))                 $row['ruc']                 = '20572613954';
        if (in_array('director', $cols))            $row['director']            = 'Mg. Roberto Enrique Chávez Palacios';
        if (in_array('cargo_director', $cols))      $row['cargo_director']      = 'Director de UGEL Huacaybamba';
        if (in_array('region', $cols))              $row['region']              = 'Huánuco';
        if (in_array('provincia', $cols))           $row['provincia']           = 'Huacaybamba';
        if (in_array('distrito', $cols))            $row['distrito']            = 'Huacaybamba';
        if (in_array('direccion', $cols))           $row['direccion']           = 'Jr. Libertad N° 450, Huacaybamba';
        if (in_array('telefono', $cols))            $row['telefono']            = '062-462100';
        if (in_array('correo', $cols))              $row['correo']              = 'ugel.huacaybamba@minedu.gob.pe';
        if (in_array('anio_gestion', $cols))        $row['anio_gestion']        = 2026;
        if (in_array('periodo_inicio', $cols))      $row['periodo_inicio']      = '2026-01-01';
        if (in_array('periodo_fin', $cols))         $row['periodo_fin']         = '2026-12-31';

        DB::table('configuracion_institucional')->insert($row);
        $this->command->info('  → Configuración institucional: 1 registro insertado');
    }
}
