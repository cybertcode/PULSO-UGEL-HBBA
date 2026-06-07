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

        // ─── Mapa de permisos agrupados por módulo ────────────────────────────
        $permisos = [

            // Usuarios & Acceso
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',

            // Roles & Permisos
            'configuracion.ver',
            'configuracion.editar',

            // Componentes SCI
            'componentes.ver',
            'componentes.editar',

            // Control Interno (actividades SCI)
            'control-interno.ver',
            'control-interno.crear',
            'control-interno.editar',
            'control-interno.eliminar',

            // Modelo de Integridad
            'integridad.ver',
            'integridad.editar',

            // Evidencias
            'evidencias.ver',
            'evidencias.subir',
            'evidencias.validar',
            'evidencias.eliminar',

            // Reportes
            'reportes.ver',
            'reportes.exportar',

            // Reconocimientos
            'reconocimientos.ver',
            'reconocimientos.crear',
            'reconocimientos.editar',
            'reconocimientos.eliminar',

            // Alertas
            'alertas.ver',
            'alertas.crear',
            'alertas.configurar',
            'alertas.eliminar',
        ];

        foreach ($permisos as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // ─── Roles ────────────────────────────────────────────────────────────

        // Super Admin — bypasa todos los Gates, no necesita permisos explícitos
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        // ── Administrador: acceso total explícito ─────────────────────────────
        $admin = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // ── Coordinador SCI: gestión completa del sistema de control interno ──
        // Es el responsable técnico de coordinar, monitorear y reportar el SCI.
        $coordinador = Role::firstOrCreate(['name' => 'Coordinador SCI', 'guard_name' => 'web']);
        $coordinador->syncPermissions([
            'control-interno.ver',
            'control-interno.crear',
            'control-interno.editar',
            'control-interno.eliminar',
            'componentes.ver',
            'componentes.editar',
            'integridad.ver',
            'integridad.editar',
            'evidencias.ver',
            'evidencias.subir',
            'evidencias.validar',
            'evidencias.eliminar',
            'reportes.ver',
            'reportes.exportar',
            'reconocimientos.ver',
            'reconocimientos.crear',
            'reconocimientos.editar',
            'alertas.ver',
            'alertas.crear',
            'alertas.configurar',
        ]);

        // ── Responsable de Unidad: gestiona actividades de su unidad orgánica ─
        $responsable = Role::firstOrCreate(['name' => 'Responsable de Unidad', 'guard_name' => 'web']);
        $responsable->syncPermissions([
            'control-interno.ver',
            'control-interno.crear',
            'control-interno.editar',
            'componentes.ver',
            'integridad.ver',
            'integridad.editar',
            'evidencias.ver',
            'evidencias.subir',
            'reportes.ver',
            'reconocimientos.ver',
            'alertas.ver',
            'alertas.crear',
        ]);

        // ── Operador: registra avances y sube evidencias ──────────────────────
        $operador = Role::firstOrCreate(['name' => 'Operador', 'guard_name' => 'web']);
        $operador->syncPermissions([
            'control-interno.ver',
            'control-interno.editar',
            'componentes.ver',
            'integridad.ver',
            'evidencias.ver',
            'evidencias.subir',
            'reportes.ver',
            'alertas.ver',
        ]);

        // ── Visualizador: solo lectura, sin modificar nada ────────────────────
        $visualizador = Role::firstOrCreate(['name' => 'Visualizador', 'guard_name' => 'web']);
        $visualizador->syncPermissions([
            'control-interno.ver',
            'componentes.ver',
            'integridad.ver',
            'evidencias.ver',
            'reportes.ver',
            'reconocimientos.ver',
            'alertas.ver',
        ]);
    }
}
