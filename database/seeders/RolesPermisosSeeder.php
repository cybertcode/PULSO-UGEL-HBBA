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

        // ─── Permisos del sistema ─────────────────────────────────────────────
        // Patrón: modulo.accion — solo se crean permisos con rutas activas reales
        $permisos = [

            // Usuarios & Acceso (rutas: /usuarios, /cargos)
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',

            // Configuración: Roles, Permisos, Unidades Orgánicas, Estructura, Config
            'configuracion.ver',
            'configuracion.editar',

            // Estructura SCI (rutas: /administracion/sci)
            'componentes.ver',
            'componentes.editar',

            // Control Interno — Actividades SCI (rutas: /control-interno)
            'control-interno.ver',
            'control-interno.crear',
            'control-interno.editar',
            'control-interno.eliminar',

            // Modelo de Integridad (rutas: /modelo-integridad)
            'integridad.ver',
            'integridad.crear',
            'integridad.editar',
            'integridad.eliminar',

            // Evidencias (rutas: /evidencias)
            'evidencias.ver',
            'evidencias.subir',
            'evidencias.validar',
            'evidencias.eliminar',

            // Semáforo (ruta: /semaforo)
            'semaforo.ver',

            // Alertas (rutas: /alertas)
            'alertas.ver',
            'alertas.crear',
            'alertas.configurar',
            'alertas.eliminar',

            // Seguimiento / Cumplimiento SCI (rutas: /cumplimiento/*)
            'cumplimiento.ver',

            // Reportes y análisis (rutas: /reportes, /avance-unidades, /ranking-unidades)
            'reportes.ver',
            'reportes.exportar',

            // Reconocimientos (rutas: /reconocimientos)
            'reconocimientos.ver',
            'reconocimientos.crear',
            'reconocimientos.editar',
            'reconocimientos.eliminar',

            // Buenas Prácticas (rutas: /buenas-practicas)
            'buenas-practicas.ver',
            'buenas-practicas.crear',
            'buenas-practicas.editar',
            'buenas-practicas.eliminar',

            // Recomendaciones (rutas: /recomendaciones)
            'recomendaciones.ver',
            'recomendaciones.crear',
            'recomendaciones.editar',
            'recomendaciones.eliminar',

            // Normativas (rutas: /normativas)
            'normativas.ver',
            'normativas.gestionar',

            // Encuestas (rutas: /encuestas/*)
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

        // ─── Roles ────────────────────────────────────────────────────────────

        // Super Admin — bypasa todos los Gates vía Gate::before(), no necesita permisos
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        // Administrador — acceso total explícito a todos los permisos del sistema
        $admin = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::where('guard_name', 'web')->pluck('name'));

        // Coordinador SCI — gestión completa del SCI e Integridad, sin admin de usuarios/config
        $coordinador = Role::firstOrCreate(['name' => 'Coordinador SCI', 'guard_name' => 'web']);
        $coordinador->syncPermissions([
            // SCI
            'control-interno.ver',
            'control-interno.crear',
            'control-interno.editar',
            'control-interno.eliminar',
            'componentes.ver',
            'componentes.editar',
            // Integridad
            'integridad.ver',
            'integridad.crear',
            'integridad.editar',
            'integridad.eliminar',
            // Evidencias
            'evidencias.ver',
            'evidencias.subir',
            'evidencias.validar',
            'evidencias.eliminar',
            // Monitoreo
            'semaforo.ver',
            'alertas.ver',
            'alertas.crear',
            'alertas.configurar',
            'alertas.eliminar',
            // Seguimiento
            'cumplimiento.ver',
            // Reportes
            'reportes.ver',
            'reportes.exportar',
            // Reconocimientos
            'reconocimientos.ver',
            'reconocimientos.crear',
            'reconocimientos.editar',
            'reconocimientos.eliminar',
            // Buenas Prácticas
            'buenas-practicas.ver',
            'buenas-practicas.crear',
            'buenas-practicas.editar',
            'buenas-practicas.eliminar',
            // Recomendaciones
            'recomendaciones.ver',
            'recomendaciones.crear',
            'recomendaciones.editar',
            'recomendaciones.eliminar',
            // Normativas
            'normativas.ver',
            'normativas.gestionar',
            // Encuestas
            'encuesta.ver',
            'encuesta.crear',
            'encuesta.editar',
            'encuesta.eliminar',
            'encuesta.publicar',
            'encuesta.resultados',
            'encuesta.exportar',
        ]);

        // Responsable de Unidad — gestiona actividades de su unidad, no puede eliminar ni configurar
        $responsable = Role::firstOrCreate(['name' => 'Responsable de Unidad', 'guard_name' => 'web']);
        $responsable->syncPermissions([
            // SCI
            'control-interno.ver',
            'control-interno.crear',
            'control-interno.editar',
            'componentes.ver',
            // Integridad
            'integridad.ver',
            'integridad.crear',
            'integridad.editar',
            // Evidencias
            'evidencias.ver',
            'evidencias.subir',
            // Monitoreo
            'semaforo.ver',
            'alertas.ver',
            'alertas.crear',
            // Seguimiento
            'cumplimiento.ver',
            // Reportes
            'reportes.ver',
            // Reconocimientos
            'reconocimientos.ver',
            // Buenas Prácticas
            'buenas-practicas.ver',
            'buenas-practicas.crear',
            'buenas-practicas.editar',
            // Recomendaciones
            'recomendaciones.ver',
            'recomendaciones.crear',
            'recomendaciones.editar',
            // Normativas
            'normativas.ver',
            // Encuestas
            'encuesta.ver',
            'encuesta.responder',
            'encuesta.resultados',
        ]);

        // Operador — registra avances y sube evidencias, solo lectura en análisis
        $operador = Role::firstOrCreate(['name' => 'Operador', 'guard_name' => 'web']);
        $operador->syncPermissions([
            // SCI
            'control-interno.ver',
            'control-interno.editar',
            'componentes.ver',
            // Integridad
            'integridad.ver',
            'integridad.editar',
            // Evidencias
            'evidencias.ver',
            'evidencias.subir',
            // Monitoreo
            'semaforo.ver',
            'alertas.ver',
            // Reportes
            'reportes.ver',
            // Buenas Prácticas
            'buenas-practicas.ver',
            // Recomendaciones
            'recomendaciones.ver',
            // Normativas
            'normativas.ver',
            // Encuestas
            'encuesta.ver',
            'encuesta.responder',
        ]);

        // Visualizador — solo lectura total, sin modificar nada
        $visualizador = Role::firstOrCreate(['name' => 'Visualizador', 'guard_name' => 'web']);
        $visualizador->syncPermissions([
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
            'encuesta.ver',
            'encuesta.responder',
        ]);
    }
}
