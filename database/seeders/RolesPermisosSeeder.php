<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Catálogo completo de permisos ─────────────────────────────────────
        $permisos = [
            // Perfil propio — todos los roles
            'perfil.ver', 'perfil.editar',

            // Mis actividades — todos los roles
            'mis-actividades.ver',

            // Ayuda — todos los roles
            'ayuda.ver',

            // ── Administración del sistema (exclusivo Super Admin o Administrador) ──
            // Gestión de usuarios y cargos
            'usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar',

            // Gestión de roles del sistema (SOLO Super Admin vía bypass)
            'roles.ver', 'roles.crear', 'roles.editar', 'roles.eliminar',

            // Configuración institucional
            'configuracion.ver', 'configuracion.editar',

            // Unidades orgánicas
            'unidades.ver', 'unidades.crear', 'unidades.editar', 'unidades.eliminar',

            // Landing — Slider
            'slider.ver', 'slider.crear', 'slider.editar', 'slider.eliminar',

            // Landing — Instituciones vinculadas
            'instituciones.ver', 'instituciones.crear', 'instituciones.editar', 'instituciones.eliminar',

            // ── Operación SCI e Integridad ────────────────────────────────────
            // Estructura SCI (ejes, componentes, preguntas)
            'componentes.ver', 'componentes.crear', 'componentes.editar', 'componentes.eliminar',

            // Actividades de Control Interno SCI
            'control-interno.ver', 'control-interno.crear', 'control-interno.editar', 'control-interno.eliminar',

            // Modelo de Integridad
            'integridad.ver', 'integridad.crear', 'integridad.editar', 'integridad.eliminar',

            // Evidencias
            'evidencias.ver', 'evidencias.crear', 'evidencias.validar', 'evidencias.eliminar',

            // Semáforo de avance
            'semaforo.ver',

            // Alertas
            'alertas.ver', 'alertas.crear', 'alertas.eliminar',

            // Cumplimiento SCI
            'cumplimiento.ver', 'cumplimiento.exportar',

            // Reportes y ranking
            'reportes.ver', 'reportes.exportar',

            // Reconocimientos mensuales
            'reconocimientos.ver', 'reconocimientos.crear', 'reconocimientos.editar', 'reconocimientos.eliminar',

            // Buenas prácticas
            'buenas-practicas.ver', 'buenas-practicas.crear', 'buenas-practicas.editar', 'buenas-practicas.eliminar',

            // Recomendaciones
            'recomendaciones.ver', 'recomendaciones.crear', 'recomendaciones.editar', 'recomendaciones.eliminar',

            // Normativas
            'normativas.ver', 'normativas.crear', 'normativas.editar', 'normativas.eliminar',

            // Encuestas
            'encuesta.ver', 'encuesta.crear', 'encuesta.editar', 'encuesta.eliminar',
            'encuesta.publicar', 'encuesta.responder', 'encuesta.resultados', 'encuesta.exportar',
        ];

        foreach ($permisos as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // ── Grupo base: todos los roles autenticados lo tienen ────────────────
        $base = [
            'perfil.ver', 'perfil.editar',
            'mis-actividades.ver',
            'ayuda.ver',
            'alertas.ver',
        ];

        // ════════════════════════════════════════════════════════════════════
        // ROL 1: Super Admin
        // Bypasa TODOS los Gates vía Gate::before() en AppServiceProvider.
        // No se le asignan permisos en BD — la lógica lo ignora directamente.
        // Solo para cuentas de desarrollo y emergencias técnicas.
        // ════════════════════════════════════════════════════════════════════
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        // ════════════════════════════════════════════════════════════════════
        // ROL 2: Administrador
        // Gestión institucional completa: usuarios, configuración, todas las
        // operaciones SCI/Integridad/Encuestas.
        // DIFERENCIA con Super Admin: NO gestiona roles del sistema (roles.*).
        // DIFERENCIA con Coordinador SCI: SÍ gestiona usuarios y configuración.
        // ════════════════════════════════════════════════════════════════════
        $admin = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $admin->syncPermissions(array_merge($base, [
            // Sistema — sin roles.*
            'usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar',
            'configuracion.ver', 'configuracion.editar',
            'unidades.ver', 'unidades.crear', 'unidades.editar', 'unidades.eliminar',
            'slider.ver', 'slider.crear', 'slider.editar', 'slider.eliminar',
            'instituciones.ver', 'instituciones.crear', 'instituciones.editar', 'instituciones.eliminar',
            // SCI e Integridad — completo
            'componentes.ver', 'componentes.crear', 'componentes.editar', 'componentes.eliminar',
            'control-interno.ver', 'control-interno.crear', 'control-interno.editar', 'control-interno.eliminar',
            'integridad.ver', 'integridad.crear', 'integridad.editar', 'integridad.eliminar',
            'evidencias.ver', 'evidencias.crear', 'evidencias.validar', 'evidencias.eliminar',
            'semaforo.ver',
            'alertas.ver', 'alertas.crear', 'alertas.eliminar',
            'cumplimiento.ver', 'cumplimiento.exportar',
            'reportes.ver', 'reportes.exportar',
            'reconocimientos.ver', 'reconocimientos.crear', 'reconocimientos.editar', 'reconocimientos.eliminar',
            'buenas-practicas.ver', 'buenas-practicas.crear', 'buenas-practicas.editar', 'buenas-practicas.eliminar',
            'recomendaciones.ver', 'recomendaciones.crear', 'recomendaciones.editar', 'recomendaciones.eliminar',
            'normativas.ver', 'normativas.crear', 'normativas.editar', 'normativas.eliminar',
            'encuesta.ver', 'encuesta.crear', 'encuesta.editar', 'encuesta.eliminar',
            'encuesta.publicar', 'encuesta.resultados', 'encuesta.exportar', 'encuesta.responder',
        ]));

        // ════════════════════════════════════════════════════════════════════
        // ROL 3: Coordinador SCI
        // Gestión operativa completa de SCI e Integridad en toda la institución.
        // DIFERENCIA con Administrador: NO gestiona usuarios, configuración,
        //   unidades, slider, instituciones. Solo opera el contenido.
        // DIFERENCIA con Responsable de Unidad: SÍ puede eliminar actividades,
        //   validar/eliminar evidencias, crear alertas, exportar reportes,
        //   gestionar encuestas completas, estructura de componentes.
        // ════════════════════════════════════════════════════════════════════
        $coordinador = Role::firstOrCreate(['name' => 'Coordinador SCI', 'guard_name' => 'web']);
        $coordinador->syncPermissions(array_merge($base, [
            // Estructura SCI — gestión completa
            'componentes.ver', 'componentes.crear', 'componentes.editar', 'componentes.eliminar',
            // SCI — puede eliminar
            'control-interno.ver', 'control-interno.crear', 'control-interno.editar', 'control-interno.eliminar',
            // Integridad — puede eliminar
            'integridad.ver', 'integridad.crear', 'integridad.editar', 'integridad.eliminar',
            // Evidencias — puede validar y eliminar
            'evidencias.ver', 'evidencias.crear', 'evidencias.validar', 'evidencias.eliminar',
            'semaforo.ver',
            // Alertas — puede crear y eliminar
            'alertas.ver', 'alertas.crear', 'alertas.eliminar',
            // Reportes — puede exportar
            'cumplimiento.ver', 'cumplimiento.exportar',
            'reportes.ver', 'reportes.exportar',
            // Reconocimientos — gestión completa
            'reconocimientos.ver', 'reconocimientos.crear', 'reconocimientos.editar', 'reconocimientos.eliminar',
            // Buenas prácticas y recomendaciones — gestión completa
            'buenas-practicas.ver', 'buenas-practicas.crear', 'buenas-practicas.editar', 'buenas-practicas.eliminar',
            'recomendaciones.ver', 'recomendaciones.crear', 'recomendaciones.editar', 'recomendaciones.eliminar',
            // Normativas — gestión completa
            'normativas.ver', 'normativas.crear', 'normativas.editar', 'normativas.eliminar',
            // Encuestas — gestión y publicación completa
            'encuesta.ver', 'encuesta.crear', 'encuesta.editar', 'encuesta.eliminar',
            'encuesta.publicar', 'encuesta.resultados', 'encuesta.exportar', 'encuesta.responder',
        ]));

        // ════════════════════════════════════════════════════════════════════
        // ROL 4: Responsable de Unidad
        // Gestiona solo las actividades de su propia unidad orgánica.
        // DIFERENCIA con Coordinador SCI: NO puede eliminar actividades ni
        //   evidencias, NO valida evidencias, NO exporta reportes, NO crea
        //   alertas de sistema, NO gestiona encuestas (solo responde).
        // DIFERENCIA con Operador: SÍ puede crear actividades, crear alertas
        //   de su unidad, editar buenas prácticas y recomendaciones.
        // ════════════════════════════════════════════════════════════════════
        $responsable = Role::firstOrCreate(['name' => 'Responsable de Unidad', 'guard_name' => 'web']);
        $responsable->syncPermissions(array_merge($base, [
            // Estructura SCI — solo lectura
            'componentes.ver',
            // SCI — puede crear y editar (NO eliminar)
            'control-interno.ver', 'control-interno.crear', 'control-interno.editar',
            // Integridad — puede crear y editar (NO eliminar)
            'integridad.ver', 'integridad.crear', 'integridad.editar',
            // Evidencias — puede crear (NO validar, NO eliminar)
            'evidencias.ver', 'evidencias.crear',
            'semaforo.ver',
            // Alertas — puede crear para su unidad (NO eliminar)
            'alertas.ver', 'alertas.crear',
            // Reportes — solo lectura, sin exportar
            'cumplimiento.ver',
            'reportes.ver',
            // Reconocimientos — solo lectura
            'reconocimientos.ver',
            // Buenas prácticas — puede crear y editar (NO eliminar)
            'buenas-practicas.ver', 'buenas-practicas.crear', 'buenas-practicas.editar',
            // Recomendaciones — puede crear y editar (NO eliminar)
            'recomendaciones.ver', 'recomendaciones.crear', 'recomendaciones.editar',
            // Normativas — solo lectura
            'normativas.ver',
            // Encuestas — responde y puede ver resultados de su unidad
            'encuesta.ver', 'encuesta.responder', 'encuesta.resultados',
        ]));

        // ════════════════════════════════════════════════════════════════════
        // ROL 5: Operador
        // Registra avances y sube evidencias en actividades asignadas.
        // DIFERENCIA con Responsable de Unidad: NO crea actividades, NO crea
        //   alertas, NO accede a cumplimiento, NO gestiona buenas prácticas
        //   ni recomendaciones, NO ve resultados de encuestas.
        // DIFERENCIA con Visualizador: SÍ puede editar avances y subir
        //   evidencias en actividades donde es responsable.
        // ════════════════════════════════════════════════════════════════════
        $operador = Role::firstOrCreate(['name' => 'Operador', 'guard_name' => 'web']);
        $operador->syncPermissions(array_merge($base, [
            // Estructura SCI — solo lectura
            'componentes.ver',
            // SCI — puede editar (actualizar avance) pero NO crear ni eliminar
            'control-interno.ver', 'control-interno.editar',
            // Integridad — puede editar (actualizar avance) pero NO crear ni eliminar
            'integridad.ver', 'integridad.editar',
            // Evidencias — puede subir (NO validar, NO eliminar)
            'evidencias.ver', 'evidencias.crear',
            'semaforo.ver',
            // Reportes — solo lectura básica
            'reportes.ver',
            // Buenas prácticas y recomendaciones — solo lectura
            'buenas-practicas.ver',
            'recomendaciones.ver',
            // Normativas — solo lectura
            'normativas.ver',
            // Encuestas — solo responde
            'encuesta.ver', 'encuesta.responder',
        ]));

        // ════════════════════════════════════════════════════════════════════
        // ROL 6: Visualizador
        // Acceso de solo lectura completo. No puede crear, editar ni eliminar
        // ningún registro. Incluye cumplimiento y reconocimientos a diferencia
        // del Operador, pero sin ningún permiso de escritura.
        // ════════════════════════════════════════════════════════════════════
        $visualizador = Role::firstOrCreate(['name' => 'Visualizador', 'guard_name' => 'web']);
        $visualizador->syncPermissions(array_merge($base, [
            'componentes.ver',
            'control-interno.ver',
            'integridad.ver',
            'evidencias.ver',
            'semaforo.ver',
            // Cumplimiento y reportes — solo lectura (diferencia con Operador)
            'cumplimiento.ver',
            'reportes.ver',
            // Reconocimientos — solo lectura (diferencia con Operador)
            'reconocimientos.ver',
            'buenas-practicas.ver',
            'recomendaciones.ver',
            'normativas.ver',
            // Encuestas — puede ver y responder, pero no ver resultados
            'encuesta.ver', 'encuesta.responder',
        ]));
    }
}
