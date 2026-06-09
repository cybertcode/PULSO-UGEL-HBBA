<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\ActaComite;
use App\Models\Autoevaluacion;
use App\Models\AutoevaluacionRespuesta;
use App\Models\Componente;
use App\Models\IntegridadCompromiso;
use App\Models\MatrizRiesgo;
use App\Models\Paci;
use App\Models\UnidadOrganica;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NuevosModulosSeeder extends Seeder
{
    public function run(): void
    {
        $sciUser   = User::where('email', 'sci@ugelhuacaybamba.edu.pe')->first();
        $dirUser   = User::where('email', 'director@ugelhuacaybamba.edu.pe')->first();
        $adminUser = User::where('email', 'admin@admin.com')->first();

        $usuarios    = User::where('estado', 'activo')->get();
        $unidades    = UnidadOrganica::all();
        $componentes = Componente::all();

        $this->seedPaci($sciUser, $adminUser);
        $this->seedMatrizRiesgos($unidades, $componentes, $usuarios);
        $this->seedActasComite($sciUser, $dirUser, $usuarios);
        $this->seedAutoevaluacion($sciUser, $componentes);
        $this->seedIntegridadCompromisos($sciUser, $dirUser, $usuarios);

        $this->command->info('✓ Módulos nuevos sembrados: PACI, Matriz Riesgos, Actas, Autoevaluación, Integridad — UGEL Huacaybamba.');
    }

    // ─── PACI ────────────────────────────────────────────────────────────────

    private function seedPaci(User $sci, User $admin): void
    {
        $actividades = Actividad::take(20)->pluck('id');

        $paciVigente = Paci::updateOrCreate(
            ['anio' => 2026, 'titulo' => 'Plan Anual de Control Interno 2026 — UGEL Huacaybamba'],
            [
                'descripcion'       => 'Plan Anual de Control Interno institucional para el ejercicio 2026 de la UGEL Huacaybamba (UE 307), aprobado mediante Resolución Directoral. Comprende actividades de implementación del SCI en las unidades orgánicas de la sede y las 4 jurisdicciones: Huacaybamba, Cochabamba, Canchabamba y Pinra.',
                'numero_resolucion' => 'RD-001-2026-UGEL-HCB',
                'fecha_aprobacion'  => '2026-01-20',
                'fecha_inicio'      => '2026-01-01',
                'fecha_fin'         => '2026-12-31',
                'estado'            => 'en_ejecucion',
                'avance'            => 38,
                'creado_por'        => $sci->id,
                'observaciones'     => 'Aprobado en la primera sesión ordinaria del Comité de Control Interno 2026 — UGEL Huacaybamba.',
            ]
        );
        if ($actividades->isNotEmpty()) {
            $paciVigente->actividades()->syncWithoutDetaching($actividades->take(15)->toArray());
        }

        $paciAnterior = Paci::updateOrCreate(
            ['anio' => 2025, 'titulo' => 'Plan Anual de Control Interno 2025 — UGEL Huacaybamba'],
            [
                'descripcion'       => 'Plan Anual de Control Interno institucional para el ejercicio 2025 de la UGEL Huacaybamba.',
                'numero_resolucion' => 'RD-002-2025-UGEL-HCB',
                'fecha_aprobacion'  => '2025-01-22',
                'fecha_inicio'      => '2025-01-01',
                'fecha_fin'         => '2025-12-31',
                'estado'            => 'cerrado',
                'avance'            => 78,
                'creado_por'        => $admin->id,
                'observaciones'     => 'Cerrado con 78% de avance. 4 actividades pendientes trasladadas al PACI 2026. Limitaciones por accesibilidad a distritos de Pinra y Canchabamba.',
            ]
        );
        if ($actividades->isNotEmpty()) {
            $paciAnterior->actividades()->syncWithoutDetaching($actividades->take(10)->toArray());
        }

        Paci::updateOrCreate(
            ['anio' => 2027, 'titulo' => 'Plan Anual de Control Interno 2027 — UGEL Huacaybamba (Borrador)'],
            [
                'descripcion' => 'Borrador preliminar del PACI 2027 en elaboración. Se priorizará el fortalecimiento del SCI en instituciones educativas unidocentes y multigrado de zonas rurales.',
                'estado'      => 'borrador',
                'avance'      => 0,
                'creado_por'  => $sci->id,
            ]
        );
    }

    // ─── Matriz de Riesgos ───────────────────────────────────────────────────

    private function seedMatrizRiesgos($unidades, $componentes, $usuarios): void
    {
        $riesgos = [
            [
                'codigo'               => 'R-001',
                'nombre'               => 'Incumplimiento del PACI por baja ejecución presupuestal',
                'descripcion'          => 'Riesgo de no ejecutar las actividades planificadas en el PACI por insuficiencia presupuestal o demora en la aprobación de créditos suplementarios por parte de la DRE Huánuco.',
                'tipo'                 => 'cumplimiento',
                'probabilidad'         => 4,
                'impacto'              => 4,
                'clasificacion'        => 'critico',
                'controles_existentes' => 'Seguimiento mensual por el Coordinador SCI. Reporte trimestral a la DRE Huánuco. Reuniones de coordinación con el área de presupuesto.',
                'acciones_tratamiento' => 'Gestionar anticipadamente créditos suplementarios. Priorizar actividades de mayor impacto SCI. Establecer indicadores de ejecución mensual.',
                'tipo_tratamiento'     => 'mitigar',
                'estado'               => 'activo',
            ],
            [
                'codigo'               => 'R-002',
                'nombre'               => 'Inadecuado registro de evidencias documentales del SCI',
                'descripcion'          => 'Riesgo de que las evidencias presentadas por las unidades orgánicas no cumplan los requisitos técnicos establecidos por la CGR, por desconocimiento del personal o falta de capacitación.',
                'tipo'                 => 'cumplimiento',
                'probabilidad'         => 4,
                'impacto'              => 3,
                'clasificacion'        => 'alto',
                'controles_existentes' => 'Guía de evidencias publicada en intranet de la UGEL. Capacitación inicial al personal en enero 2026.',
                'acciones_tratamiento' => 'Capacitaciones periódicas cada trimestre. Revisión previa por el coordinador SCI antes del cierre de cada actividad.',
                'tipo_tratamiento'     => 'mitigar',
                'estado'               => 'activo',
            ],
            [
                'codigo'               => 'R-003',
                'nombre'               => 'Alta rotación de personal en puestos clave del SCI',
                'descripcion'          => 'La alta rotación de personal administrativo y docente destacado en la UGEL Huacaybamba genera brechas de conocimiento en los procesos de Control Interno, afectando la continuidad de la implementación.',
                'tipo'                 => 'operativo',
                'probabilidad'         => 4,
                'impacto'              => 3,
                'clasificacion'        => 'alto',
                'controles_existentes' => 'Plan de capacitación anual. Manuales de funciones actualizados. Inducción al personal nuevo.',
                'acciones_tratamiento' => 'Crear repositorio de conocimiento institucional. Establecer plan de sucesión para puestos SCI. Inducción obligatoria de 5 días para nuevo personal.',
                'tipo_tratamiento'     => 'mitigar',
                'estado'               => 'activo',
            ],
            [
                'codigo'               => 'R-004',
                'nombre'               => 'Actos de corrupción en procesos de contratación y adquisiciones',
                'descripcion'          => 'Riesgo de actos de corrupción en procesos de adquisición de bienes y servicios, especialmente en compras directas para mantenimiento de locales escolares rurales de Canchabamba y Pinra.',
                'tipo'                 => 'estrategico',
                'probabilidad'         => 2,
                'impacto'              => 5,
                'clasificacion'        => 'alto',
                'controles_existentes' => 'Comité de selección plural. Publicación en SEACE. Revisión jurídica previa a toda contratación.',
                'acciones_tratamiento' => 'Fortalecer revisión de expedientes técnicos. Declaraciones juradas de conflicto de interés obligatorias. Supervisión de obras por inspector independiente.',
                'tipo_tratamiento'     => 'mitigar',
                'estado'               => 'activo',
            ],
            [
                'codigo'               => 'R-005',
                'nombre'               => 'Pérdida de información institucional por desastres naturales',
                'descripcion'          => 'Riesgo de pérdida de datos críticos e infraestructura física por huaycos, lluvias intensas o sismos frecuentes en la zona andina de Huacaybamba (3,168 msnm). La UGEL no cuenta con plan de continuidad operativa actualizado.',
                'tipo'                 => 'tecnologico',
                'probabilidad'         => 3,
                'impacto'              => 4,
                'clasificacion'        => 'alto',
                'controles_existentes' => 'Backups manuales semanales. Antivirus institucional. Archivo físico en segundo piso.',
                'acciones_tratamiento' => 'Implementar backup automático en la nube. Actualizar plan de continuidad operativa considerando riesgos geográficos. Coordinar con INDECI Huánuco.',
                'tipo_tratamiento'     => 'mitigar',
                'estado'               => 'activo',
            ],
            [
                'codigo'               => 'R-006',
                'nombre'               => 'Retrasos en pagos a docentes por errores en planillas',
                'descripcion'          => 'Subejecución del proceso de planillas y pagos por errores en el NEXUS o demoras en validaciones de DITEN, afectando el bienestar del personal docente y generando reclamos ante el MINEDU.',
                'tipo'                 => 'operativo',
                'probabilidad'         => 3,
                'impacto'              => 4,
                'clasificacion'        => 'alto',
                'controles_existentes' => 'Revisión mensual de planillas antes del cierre. Coordinación con DRE Huánuco para validaciones.',
                'acciones_tratamiento' => 'Implementar lista de verificación doble antes del cierre de planillas. Establecer cronograma de validaciones con margen de 5 días.',
                'tipo_tratamiento'     => 'mitigar',
                'estado'               => 'activo',
            ],
            [
                'codigo'               => 'R-007',
                'nombre'               => 'Dificultad de acceso a IIEE rurales para supervisión pedagógica',
                'descripcion'          => 'Las condiciones geográficas de los distritos de Pinra y Canchabamba (vías de tierra, lluvias estacionales) limitan la frecuencia de visitas de supervisión a instituciones educativas unidocentes y multigrado.',
                'tipo'                 => 'operativo',
                'probabilidad'         => 4,
                'impacto'              => 3,
                'clasificacion'        => 'alto',
                'controles_existentes' => 'Cronograma de visitas con margen por contingencia. Coordinación con GOREHCO para estado de vías.',
                'acciones_tratamiento' => 'Implementar supervisión virtual para IIEE con conectividad. Reprogramar visitas durante temporada seca. Coordinar con UGEL vecinas para supervisiones conjuntas.',
                'tipo_tratamiento'     => 'mitigar',
                'estado'               => 'activo',
            ],
            [
                'codigo'               => 'R-008',
                'nombre'               => 'Incumplimiento normativo en gestión documental y archivo',
                'descripcion'          => 'Riesgo de sanciones administrativas por no cumplir con plazos de transferencia al Archivo Central y no contar con el inventario de gestión documental actualizado conforme a la Ley N° 25323.',
                'tipo'                 => 'cumplimiento',
                'probabilidad'         => 2,
                'impacto'              => 2,
                'clasificacion'        => 'bajo',
                'controles_existentes' => 'Cronograma de archivo anual. Responsable de archivo designado mediante memorándum.',
                'acciones_tratamiento' => 'Monitorear cumplimiento mensual. Actualizar inventario de gestión documental. Coordinar transferencias con Archivo Regional de Huánuco.',
                'tipo_tratamiento'     => 'aceptar',
                'estado'               => 'aceptado',
            ],
        ];

        foreach ($riesgos as $idx => $datos) {
            $unidad      = $unidades->get($idx % $unidades->count());
            $componente  = $componentes->isNotEmpty() ? $componentes->get($idx % $componentes->count()) : null;
            $responsable = $usuarios->where('unidad_organica_id', $unidad?->id)->first() ?? $usuarios->random();

            MatrizRiesgo::updateOrCreate(
                ['codigo' => $datos['codigo']],
                array_merge($datos, [
                    'unidad_organica_id' => $unidad?->id,
                    'componente_id'      => $componente?->id,
                    'responsable_id'     => $responsable?->id,
                    'fecha_revision'     => Carbon::now()->addMonths(3)->format('Y-m-d'),
                    'anio'               => 2026,
                ])
            );
        }
    }

    // ─── Actas de Comité ─────────────────────────────────────────────────────

    private function seedActasComite(User $sci, User $dir, $usuarios): void
    {
        $actas = [
            [
                'numero_acta'  => 'ACTA-001-2026-UGEL-HCB',
                'titulo'       => 'Primera Sesión Ordinaria del Comité de Control Interno — UGEL Huacaybamba 2026',
                'fecha_sesion' => '2026-02-14',
                'hora_inicio'  => '09:00:00',
                'hora_fin'     => '11:30:00',
                'lugar'        => 'Sala de Reuniones — UGEL Huacaybamba, Av. 28 de Julio N° 502-504',
                'tipo_sesion'  => 'ordinaria',
                'agenda'       => "1. Instalación formal del Comité de Control Interno 2026\n2. Aprobación del Plan de Trabajo SCI 2026\n3. Revisión del balance del PACI 2025 (78% de avance)\n4. Asignación de responsabilidades por unidad orgánica\n5. Cronograma de sesiones ordinarias del ejercicio\n6. Varios",
                'desarrollo'   => 'Se instaló formalmente el Comité de Control Interno para el ejercicio 2026 bajo la presidencia del Director Mg. Julio Luis Lozano Yllatopa. Se presentó el informe de cierre del PACI 2025 con 78% de avance, identificándose las limitaciones de acceso a los distritos de Pinra y Canchabamba como factor de incumplimiento. Se aprobó el PACI 2026 con 18 actividades y se estableció el cronograma de sesiones bimestrales.',
                'acuerdos'     => "- Aprobar el Plan de Trabajo SCI 2026 (RD N° 001-2026-UGEL-HCB)\n- Designar al Coordinador SCI como Secretario Técnico del Comité\n- Establecer sesiones ordinarias bimestrales (feb, abr, jun, ago, oct, dic)\n- Incorporar en PACI 2026 estrategia de supervisión para zonas de difícil acceso\n- Jefes de unidad: designar enlace SCI en 15 días",
                'compromisos'  => "- Coord. SCI: elaborar guía de evidencias actualizada — 28/02/2026\n- Directora: gestionar presupuesto para capacitaciones — 15/03/2026\n- Jefes de área: designar enlace SCI y remitir mediante memorándum — 28/02/2026",
                'estado'       => 'realizada',
                'secretario_id' => $sci->id,
            ],
            [
                'numero_acta'  => 'ACTA-002-2026-UGEL-HCB',
                'titulo'       => 'Segunda Sesión Ordinaria — Revisión de Avance PACI I Trimestre 2026',
                'fecha_sesion' => '2026-04-10',
                'hora_inicio'  => '10:00:00',
                'hora_fin'     => '12:00:00',
                'lugar'        => 'Sala de Reuniones — UGEL Huacaybamba, Av. 28 de Julio N° 502-504',
                'tipo_sesion'  => 'ordinaria',
                'agenda'       => "1. Verificación de cumplimiento de acuerdos — ACTA-001-2026\n2. Informe de avance del PACI 2026 (I Trimestre: 38%)\n3. Presentación de resultados de autoevaluación SCI I Trimestre\n4. Aprobación de la Matriz de Riesgos institucional actualizada\n5. Informe de Asesoría Jurídica sobre hallazgos OCI\n6. Varios",
                'desarrollo'   => 'Se verificó el cumplimiento de acuerdos de la sesión anterior. Se presentó el informe de avance del I Trimestre con 38% de ejecución del PACI. El Coordinador SCI expuso los resultados de la autoevaluación con puntaje de 62/100. Se analizó la Matriz de Riesgos con 8 riesgos identificados, destacando el R-007 relacionado a dificultad de acceso a IIEE rurales de Pinra y Canchabamba.',
                'acuerdos'     => "- Aprobar la Matriz de Riesgos institucional 2026 (RD N° 142-2026-UGEL-HCB)\n- Notificar mediante memorándum a unidades con avance menor al 20%\n- Programar jornada de sensibilización en integridad para II Trimestre\n- Implementar supervisión virtual para IIEE con conectividad en zonas rurales",
                'compromisos'  => "- Coord. SCI: notificar a unidades con bajo avance — 15/04/2026\n- AGA: proveer materiales para jornada de sensibilización — 30/04/2026\n- AGP: proponer lista de IIEE con conectividad para supervisión virtual — 20/04/2026",
                'estado'       => 'realizada',
                'secretario_id' => $sci->id,
            ],
            [
                'numero_acta'  => 'ACTA-003-2026-UGEL-HCB',
                'titulo'       => 'Sesión Extraordinaria — Levantamiento de Observaciones OCI',
                'fecha_sesion' => '2026-05-22',
                'hora_inicio'  => '15:00:00',
                'hora_fin'     => '17:00:00',
                'lugar'        => 'Oficina de la Dirección — UGEL Huacaybamba',
                'tipo_sesion'  => 'extraordinaria',
                'agenda'       => "1. Presentación de hallazgos del Informe de Auditoría OCI N° 012-2026\n2. Plan de levantamiento de observaciones en 60 días\n3. Designación de responsables por observación\n4. Coordinación con DRE Huánuco",
                'desarrollo'   => 'Se convocó sesión extraordinaria para atender el Informe de Auditoría N° 012-2026-OCI emitido por la Oficina de Control Institucional de la DRE Huánuco. Se presentaron 4 observaciones: (1) gestión documental sin inventario actualizado, (2) contratos de locadores vencidos, (3) delegación de funciones sin formalizar, (4) señalética de seguridad deficiente. Se aprobó plan de levantamiento en 60 días.',
                'acuerdos'     => "- Elaborar plan de levantamiento en 10 días hábiles\n- Asignar responsable individual por cada una de las 4 observaciones\n- Informar avance semanal a la Directora y remitir informe a DRE Huánuco al cierre",
                'compromisos'  => "- Todos los jefes: remitir descargos documentados — 05/06/2026\n- Asesora Jurídica: emitir RD de delegación de funciones — 30/05/2026\n- Logística: regularizar señalética con proveedor local — 30/06/2026",
                'estado'       => 'realizada',
                'secretario_id' => $sci->id,
            ],
            [
                'numero_acta'  => 'ACTA-004-2026-UGEL-HCB',
                'titulo'       => 'Tercera Sesión Ordinaria — Avance II Trimestre y Modelo de Integridad',
                'fecha_sesion' => Carbon::now()->addDays(20)->format('Y-m-d'),
                'hora_inicio'  => '09:00:00',
                'hora_fin'     => null,
                'lugar'        => 'Sala de Reuniones — UGEL Huacaybamba, Av. 28 de Julio N° 502-504',
                'tipo_sesion'  => 'ordinaria',
                'agenda'       => "1. Avance del PACI 2026 (II Trimestre)\n2. Revisión del Modelo de Integridad — compromisos por pilar\n3. Aprobación de la autoevaluación SCI II Trimestre\n4. Informe de levantamiento de observaciones OCI\n5. Banco de recursos pedagógicos para docentes rurales — avance\n6. Varios",
                'desarrollo'   => null,
                'acuerdos'     => null,
                'compromisos'  => null,
                'estado'       => 'convocada',
                'secretario_id' => $sci->id,
            ],
        ];

        $emailsComite = [
            'director@ugelhuacaybamba.edu.pe',
            'sci@ugelhuacaybamba.edu.pe',
            'administracion@ugelhuacaybamba.edu.pe',
            'pedagogia@ugelhuacaybamba.edu.pe',
            'asesoria@ugelhuacaybamba.edu.pe',
        ];
        $miembrosComite = $usuarios->whereIn('email', $emailsComite)->values();

        foreach ($actas as $datos) {
            $acta = ActaComite::updateOrCreate(
                ['numero_acta' => $datos['numero_acta']],
                $datos
            );

            foreach ($miembrosComite as $miembro) {
                DB::table('acta_participantes')->updateOrInsert(
                    ['acta_id' => $acta->id, 'usuario_id' => $miembro->id],
                    [
                        'asistio'         => $datos['estado'] === 'realizada',
                        'cargo_en_comite' => match($miembro->email) {
                            'director@ugelhuacaybamba.edu.pe'       => 'Presidente del Comité',
                            'sci@ugelhuacaybamba.edu.pe'             => 'Secretario Técnico',
                            'administracion@ugelhuacaybamba.edu.pe'  => 'Miembro — Gestión Administrativa',
                            'pedagogia@ugelhuacaybamba.edu.pe'       => 'Miembro — Gestión Pedagógica',
                            'asesoria@ugelhuacaybamba.edu.pe'        => 'Miembro — Asesoría Jurídica',
                            default                                  => 'Miembro',
                        },
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }

    // ─── Autoevaluación SCI ──────────────────────────────────────────────────

    private function seedAutoevaluacion(User $sci, $componentes): void
    {
        $preguntas = [
            ['componente' => 1, 'pregunta' => '¿La UGEL Huacaybamba cuenta con un Comité de Control Interno formalmente constituido mediante Resolución Directoral vigente?', 'respuesta' => 'si', 'puntaje' => 3],
            ['componente' => 1, 'pregunta' => '¿Existe un Plan de Trabajo del Comité de Control Interno aprobado para el ejercicio 2026?', 'respuesta' => 'si', 'puntaje' => 3],
            ['componente' => 1, 'pregunta' => '¿Se realizan sesiones del Comité con la periodicidad bimestral establecida?', 'respuesta' => 'parcial', 'puntaje' => 1],
            ['componente' => 2, 'pregunta' => '¿El personal de la UGEL conoce el Código de Ética de la Función Pública y ha suscrito declaración de compromiso?', 'respuesta' => 'si', 'puntaje' => 3],
            ['componente' => 2, 'pregunta' => '¿Se han realizado capacitaciones en integridad y ética pública en el primer semestre 2026?', 'respuesta' => 'si', 'puntaje' => 3],
            ['componente' => 2, 'pregunta' => '¿Existe canal de denuncias anónimas habilitado y comunicado al personal y ciudadanos?', 'respuesta' => 'no', 'puntaje' => 0],
            ['componente' => 3, 'pregunta' => '¿La UGEL Huacaybamba tiene identificados y documentados sus principales riesgos institucionales en una Matriz de Riesgos aprobada?', 'respuesta' => 'si', 'puntaje' => 3],
            ['componente' => 3, 'pregunta' => '¿La Matriz de Riesgos ha sido actualizada en el primer semestre 2026?', 'respuesta' => 'si', 'puntaje' => 3],
            ['componente' => 3, 'pregunta' => '¿Existen controles definidos y documentados para los riesgos clasificados como alto y crítico?', 'respuesta' => 'parcial', 'puntaje' => 1],
            ['componente' => 4, 'pregunta' => '¿El portal de transparencia de la UGEL se actualiza mensualmente conforme a la Ley N° 27806?', 'respuesta' => 'si', 'puntaje' => 3],
            ['componente' => 4, 'pregunta' => '¿Se han implementado controles preventivos en los procesos de contrataciones y adquisiciones?', 'respuesta' => 'parcial', 'puntaje' => 1],
            ['componente' => 5, 'pregunta' => '¿Se cuenta con procedimientos documentados para los principales procesos administrativos de la UGEL?', 'respuesta' => 'si', 'puntaje' => 3],
            ['componente' => 5, 'pregunta' => '¿Se generan reportes periódicos de avance del SCI a la Dirección y a la DRE Huánuco?', 'respuesta' => 'si', 'puntaje' => 3],
            ['componente' => 6, 'pregunta' => '¿Se cuenta con sistema de información para el seguimiento de actividades del SCI en la UGEL?', 'respuesta' => 'si', 'puntaje' => 3],
            ['componente' => 6, 'pregunta' => '¿El personal nuevo recibe inducción sobre el SCI y el Modelo de Integridad al inicio de funciones?', 'respuesta' => 'parcial', 'puntaje' => 1],
        ];

        $autoevaluacion = Autoevaluacion::updateOrCreate(
            ['anio' => 2026, 'periodo' => 'I_trimestre'],
            [
                'titulo'          => 'Autoevaluación SCI — I Trimestre 2026 — UGEL Huacaybamba',
                'fecha_inicio'    => '2026-03-01',
                'fecha_cierre'    => '2026-03-31',
                'estado'          => 'cerrada',
                'puntaje_total'   => 62,
                'conclusiones'    => 'La UGEL Huacaybamba muestra avance significativo en la implementación del SCI, alcanzando el 62% de las condiciones requeridas según la Directiva N° 006-2019-CG/INTEG. Se identifican brechas críticas en: (1) ausencia de canal de denuncias, (2) periodicidad de sesiones del Comité, y (3) controles insuficientes para riesgos en zonas rurales de Pinra y Canchabamba.',
                'recomendaciones' => "- Habilitar canal de denuncias anónimas antes del II Trimestre 2026.\n- Reforzar sesiones del Comité con convocatoria formal con 5 días de anticipación.\n- Fortalecer controles en procesos de adquisiciones para mantenimiento de IIEE rurales.\n- Implementar inducción SCI obligatoria para personal nuevo en los primeros 30 días.",
                'elaborado_por'   => $sci->id,
            ]
        );

        foreach ($preguntas as $p) {
            $componente = $componentes->where('numero', $p['componente'])->first()
                       ?? $componentes->get($p['componente'] - 1)
                       ?? $componentes->first();

            if (!$componente) continue;

            AutoevaluacionRespuesta::updateOrCreate(
                [
                    'autoevaluacion_id' => $autoevaluacion->id,
                    'pregunta'          => $p['pregunta'],
                ],
                [
                    'componente_id' => $componente->id,
                    'respuesta'     => $p['respuesta'],
                    'puntaje'       => $p['puntaje'],
                    'evidencia'     => $p['respuesta'] === 'si' ? 'Documentación disponible en archivo institucional de la UGEL Huacaybamba.' : null,
                    'observacion'   => $p['respuesta'] === 'no' ? 'Pendiente de implementar en el II Trimestre 2026.' : null,
                ]
            );
        }

        Autoevaluacion::updateOrCreate(
            ['anio' => 2026, 'periodo' => 'II_trimestre'],
            [
                'titulo'        => 'Autoevaluación SCI — II Trimestre 2026 — UGEL Huacaybamba',
                'fecha_inicio'  => '2026-06-01',
                'fecha_cierre'  => '2026-06-30',
                'estado'        => 'en_proceso',
                'elaborado_por' => $sci->id,
            ]
        );
    }

    // ─── Compromisos del Modelo de Integridad ────────────────────────────────

    private function seedIntegridadCompromisos(User $sci, User $dir, $usuarios): void
    {
        $compromisos = [
            // Pilar: Compromiso
            [
                'pilar'         => 'compromiso',
                'titulo'        => 'Constitución y operatividad del Comité de Integridad de la UGEL Huacaybamba',
                'descripcion'   => 'Garantizar que el Comité de Integridad esté formalmente constituido mediante Resolución Directoral, cuente con plan de trabajo aprobado y sesione bimestralmente durante el ejercicio 2026.',
                'avance'        => 80,
                'estado'        => 'en_proceso',
                'fecha_inicio'  => '2026-01-20',
                'fecha_fin'     => '2026-12-31',
                'responsable'   => 'director',
                'evidencia'     => 'RD N° 001-2026-UGEL-HCB de constitución del Comité. 3 actas de sesiones realizadas.',
            ],
            [
                'pilar'         => 'compromiso',
                'titulo'        => 'Aprobación y difusión de la Política de Integridad Institucional',
                'descripcion'   => 'Elaborar, aprobar mediante Resolución Directoral y difundir a todo el personal de la UGEL Huacaybamba la Política Institucional de Integridad, incluyendo docentes destacados en comisión.',
                'avance'        => 100,
                'estado'        => 'completado',
                'fecha_inicio'  => '2026-01-20',
                'fecha_fin'     => '2026-03-31',
                'responsable'   => 'sci',
                'evidencia'     => 'RD de aprobación. Lista de difusión firmada por 47 servidores. Publicación en portal web www.ugelhuacaybamba.edu.pe.',
            ],
            // Pilar: Cultura
            [
                'pilar'         => 'cultura',
                'titulo'        => 'Programa de capacitación en ética e integridad pública 2026',
                'descripcion'   => 'Ejecutar al menos 3 capacitaciones anuales en ética pública, prevención de la corrupción e integridad institucional, dirigidas a todo el personal administrativo y docentes destacados de la UGEL Huacaybamba.',
                'avance'        => 67,
                'estado'        => 'en_proceso',
                'fecha_inicio'  => '2026-02-01',
                'fecha_fin'     => '2026-11-30',
                'responsable'   => 'sci',
                'evidencia'     => 'Informes de 2 capacitaciones realizadas (feb y abr 2026). Listas de asistencia. 87 participantes acumulados.',
            ],
            [
                'pilar'         => 'cultura',
                'titulo'        => 'Implementación del canal de denuncias anónimas institucional',
                'descripcion'   => 'Habilitar un canal seguro y anónimo (correo institucional y buzón físico) para que ciudadanos y servidores puedan reportar irregularidades, conforme al DS N° 042-2011-PCM.',
                'avance'        => 20,
                'estado'        => 'en_proceso',
                'fecha_inicio'  => '2026-04-01',
                'fecha_fin'     => '2026-07-31',
                'responsable'   => 'director',
                'evidencia'     => null,
                'observaciones' => 'En gestión para habilitación del correo denuncias@ugelhuacaybamba.edu.pe. Buzón físico en proceso de instalación en mesa de partes.',
            ],
            [
                'pilar'         => 'cultura',
                'titulo'        => 'Difusión del Código de Ética a todo el personal administrativo',
                'descripcion'   => 'Distribuir el Código de Ética de la Función Pública y obtener cargo de recepción firmado de todos los servidores administrativos de la UGEL Huacaybamba.',
                'avance'        => 100,
                'estado'        => 'completado',
                'fecha_inicio'  => '2026-01-15',
                'fecha_fin'     => '2026-02-28',
                'responsable'   => 'sci',
                'evidencia'     => 'Cargos de recepción firmados por 47 servidores. Registro en legajos de personal. Publicación en intranet.',
            ],
            // Pilar: Regulación
            [
                'pilar'         => 'regulacion',
                'titulo'        => 'Actualización del Reglamento de Organización y Funciones (ROF)',
                'descripcion'   => 'Revisar y actualizar el ROF de la UGEL Huacaybamba para adecuarlo a la normativa vigente (DL N° 1440, DS N° 040-2014-PCM) e incorporar las funciones relacionadas al SCI y al Modelo de Integridad.',
                'avance'        => 45,
                'estado'        => 'en_proceso',
                'fecha_inicio'  => '2026-03-01',
                'fecha_fin'     => '2026-08-31',
                'responsable'   => 'director',
                'evidencia'     => 'Borrador del ROF revisado por Asesoría Jurídica. Observaciones incorporadas en segunda revisión.',
            ],
            [
                'pilar'         => 'regulacion',
                'titulo'        => 'Elaboración del Manual de Procedimientos Administrativos',
                'descripcion'   => 'Documentar los procedimientos administrativos clave de la UGEL Huacaybamba con sus controles internos, responsables y plazos, incluyendo procedimientos específicos para atención en zonas rurales.',
                'avance'        => 60,
                'estado'        => 'en_proceso',
                'fecha_inicio'  => '2026-02-15',
                'fecha_fin'     => '2026-09-30',
                'responsable'   => 'sci',
                'evidencia'     => '8 de 13 procedimientos documentados, revisados y aprobados mediante memorándum circular.',
            ],
            // Pilar: Control
            [
                'pilar'         => 'control',
                'titulo'        => 'Implementación de la Matriz de Riesgos institucional 2026',
                'descripcion'   => 'Identificar, evaluar y documentar los riesgos institucionales de la UGEL Huacaybamba en una Matriz de Riesgos actualizada, con énfasis en riesgos propios de una UGEL de zona rural andina (accesibilidad, rotación, conectividad).',
                'avance'        => 90,
                'estado'        => 'en_proceso',
                'fecha_inicio'  => '2026-01-15',
                'fecha_fin'     => '2026-06-30',
                'responsable'   => 'sci',
                'evidencia'     => 'Matriz de Riesgos aprobada (RD N° 142-2026-UGEL-HCB) con 8 riesgos identificados, niveles calculados y planes de tratamiento asignados.',
            ],
            [
                'pilar'         => 'control',
                'titulo'        => 'Ejecución del PACI 2026 al 70% al cierre del ejercicio',
                'descripcion'   => 'Ejecutar al menos el 70% de las 18 actividades del PACI 2026 con evidencias debidamente registradas en el sistema PULSO UGEL, superando el 78% alcanzado en el PACI 2025.',
                'avance'        => 38,
                'estado'        => 'en_proceso',
                'fecha_inicio'  => '2026-01-01',
                'fecha_fin'     => '2026-12-31',
                'responsable'   => 'sci',
                'evidencia'     => 'Reportes de avance del I Trimestre (38%). Evidencias de 7 de 18 actividades cargadas en el sistema.',
            ],
            [
                'pilar'         => 'control',
                'titulo'        => 'Levantamiento de las 4 observaciones del Informe OCI N° 012-2026',
                'descripcion'   => 'Atender y documentar el levantamiento de las 4 observaciones identificadas en el Informe de Auditoría N° 012-2026 emitido por la OCI de la DRE Huánuco, en el plazo de 60 días.',
                'avance'        => 50,
                'estado'        => 'en_proceso',
                'fecha_inicio'  => '2026-05-22',
                'fecha_fin'     => '2026-07-22',
                'responsable'   => 'director',
                'evidencia'     => '2 observaciones levantadas: (1) contratos de locadores regularizados, (2) delegación de funciones formalizada mediante RD. Pendientes: gestión documental y señalética.',
                'observaciones' => 'Observaciones pendientes: actualización de inventario documentario (Logística) y señalización de seguridad (plazo: 30/06/2026).',
            ],
        ];

        foreach ($compromisos as $datos) {
            $responsableUser = match($datos['responsable']) {
                'director' => $dir,
                'sci'      => $sci,
                default    => $sci,
            };

            IntegridadCompromiso::updateOrCreate(
                ['pilar' => $datos['pilar'], 'titulo' => $datos['titulo']],
                [
                    'descripcion'    => $datos['descripcion'],
                    'avance'         => $datos['avance'],
                    'estado'         => $datos['estado'],
                    'fecha_inicio'   => $datos['fecha_inicio'],
                    'fecha_fin'      => $datos['fecha_fin'],
                    'responsable_id' => $responsableUser->id,
                    'evidencia'      => $datos['evidencia'] ?? null,
                    'observaciones'  => $datos['observaciones'] ?? null,
                    'anio'           => 2026,
                ]
            );
        }
    }
}
