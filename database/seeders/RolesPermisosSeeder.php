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

        // ─── Permisos granulados — patrón modulo.accion ───────────────────────
        $permisos = [

            // Perfil propio (/user/profile) — todos los roles deben tenerlo
            'perfil.ver',
            'perfil.editar',

            // Mis Actividades (/mis-actividades) — todos los roles
            'mis-actividades.ver',

            // Ayuda (/ayuda) — todos los roles
            'ayuda.ver',

            // Usuarios (/usuarios, /cargos)
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',

            // Roles (/roles)
            'roles.ver',
            'roles.crear',
            'roles.editar',
            'roles.eliminar',

            // Configuración institucional (/configuracion)
            'configuracion.ver',
            'configuracion.editar',

            // Unidades Orgánicas (/unidades-organicas)
            'unidades.ver',
            'unidades.crear',
            'unidades.editar',
            'unidades.eliminar',

            // Landing — Slider (/slider-landing)
            'slider.ver',
            'slider.crear',
            'slider.editar',
            'slider.eliminar',

            // Landing — Instituciones Vinculadas (/instituciones-vinculadas)
            'instituciones.ver',
            'instituciones.crear',
            'instituciones.editar',
            'instituciones.eliminar',

            // Estructura SCI (/administracion/sci)
            'componentes.ver',
            'componentes.crear',
            'componentes.editar',
            'componentes.eliminar',

            // Control Interno — Actividades SCI (/control-interno)
            'control-interno.ver',
            'control-interno.crear',
            'control-interno.editar',
            'control-interno.eliminar',

            // Modelo de Integridad (/modelo-integridad, /administracion/integridad)
            'integridad.ver',
            'integridad.crear',
            'integridad.editar',
            'integridad.eliminar',

            // Evidencias (/evidencias)
            'evidencias.ver',
            'evidencias.crear',
            'evidencias.validar',
            'evidencias.eliminar',

            // Semáforo (/semaforo)
            'semaforo.ver',

            // Alertas (/alertas)
            'alertas.ver',
            'alertas.crear',
            'alertas.eliminar',

            // Cumplimiento SCI (/cumplimiento/*)
            'cumplimiento.ver',
            'cumplimiento.exportar',

            // Reportes (/reportes, /ranking-unidades, /avance-unidades)
            'reportes.ver',
            'reportes.exportar',

            // Reconocimientos (/reconocimientos)
            'reconocimientos.ver',
            'reconocimientos.crear',
            'reconocimientos.editar',
            'reconocimientos.eliminar',

            // Buenas Prácticas (/buenas-practicas)
            'buenas-practicas.ver',
            'buenas-practicas.crear',
            'buenas-practicas.editar',
            'buenas-practicas.eliminar',

            // Recomendaciones (/recomendaciones)
            'recomendaciones.ver',
            'recomendaciones.crear',
            'recomendaciones.editar',
            'recomendaciones.eliminar',

            // Normativas (/normativas)
            'normativas.ver',
            'normativas.crear',
            'normativas.editar',
            'normativas.eliminar',

            // Encuestas (/encuestas/*)
            'encuesta.ver',
            'encuesta.crear',
            'encuesta.editar',
            'encuesta.eliminar',
            'encuesta.publicar',
            'encuesta.responder',
            'encuesta.resultados',
            'encuesta.exportar',
        ];

        foreach ($permisos as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // Permisos base que todo usuario autenticado debe tener sin excepción
        $permisosBase = [
            'perfil.ver',
            'perfil.editar',
            'mis-actividades.ver',
            'ayuda.ver',
            'alertas.ver',  // notificaciones básicas del sistema
        ];

        // ─── Roles ────────────────────────────────────────────────────────────

        // Super Admin — bypasa todos los Gates vía Gate::before()
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        // Administrador — todos los permisos
        $admin = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::where('guard_name', 'web')->pluck('name'));

        // Coordinador SCI — gestión completa SCI/Integridad, sin administración de sistema
        $coordinador = Role::firstOrCreate(['name' => 'Coordinador SCI', 'guard_name' => 'web']);
        $coordinador->syncPermissions(array_merge($permisosBase, [
            'control-interno.ver', 'control-interno.crear', 'control-interno.editar', 'control-interno.eliminar',
            'componentes.ver', 'componentes.crear', 'componentes.editar', 'componentes.eliminar',
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

        // Responsable de Unidad — gestiona actividades de su unidad, sin eliminar ni configurar sistema
        $responsable = Role::firstOrCreate(['name' => 'Responsable de Unidad', 'guard_name' => 'web']);
        $responsable->syncPermissions(array_merge($permisosBase, [
            'control-interno.ver', 'control-interno.crear', 'control-interno.editar',
            'componentes.ver',
            'integridad.ver', 'integridad.crear', 'integridad.editar',
            'evidencias.ver', 'evidencias.crear',
            'semaforo.ver',
            'alertas.ver', 'alertas.crear',
            'cumplimiento.ver',
            'reportes.ver',
            'reconocimientos.ver',
            'buenas-practicas.ver', 'buenas-practicas.crear', 'buenas-practicas.editar',
            'recomendaciones.ver', 'recomendaciones.crear', 'recomendaciones.editar',
            'normativas.ver',
            'encuesta.ver', 'encuesta.responder', 'encuesta.resultados',
        ]));

        // Operador — registra avances y sube evidencias, solo lectura en análisis
        $operador = Role::firstOrCreate(['name' => 'Operador', 'guard_name' => 'web']);
        $operador->syncPermissions(array_merge($permisosBase, [
            'control-interno.ver', 'control-interno.editar',
            'componentes.ver',
            'integridad.ver', 'integridad.editar',
            'evidencias.ver', 'evidencias.crear',
            'semaforo.ver',
            'alertas.ver',
            'reportes.ver',
            'buenas-practicas.ver',
            'recomendaciones.ver',
            'normativas.ver',
            'encuesta.ver', 'encuesta.responder',
        ]));

        // Visualizador — solo lectura total
        $visualizador = Role::firstOrCreate(['name' => 'Visualizador', 'guard_name' => 'web']);
        $visualizador->syncPermissions(array_merge($permisosBase, [
            'control-interno.ver',
            'componentes.ver',
            'integridad.ver',
            'evidencias.ver',
            'semaforo.ver',
            'alertas.ver',
            'cumplimiento.ver',
            'reportes.ver',
            'reconocimientos.ver',
            'buenas-practicas.ver',
            'recomendaciones.ver',
            'normativas.ver',
            'encuesta.ver', 'encuesta.responder',
        ]));
    }
}
