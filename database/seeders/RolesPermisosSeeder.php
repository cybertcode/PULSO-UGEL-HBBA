<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permisos = [
            // Usuarios
            'usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar',
            // Control Interno
            'control-interno.ver', 'control-interno.crear', 'control-interno.editar',
            // Modelo de Integridad
            'integridad.ver', 'integridad.editar',
            // Evidencias
            'evidencias.ver', 'evidencias.subir', 'evidencias.validar',
            // Reportes
            'reportes.ver', 'reportes.exportar',
            // Reconocimientos
            'reconocimientos.ver', 'reconocimientos.editar',
            // Alertas
            'alertas.ver', 'alertas.configurar',
            // Configuración
            'configuracion.ver', 'configuracion.editar',
        ];

        foreach ($permisos as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // Administrador — acceso total
        $admin = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // Responsable de Unidad — gestiona su unidad
        $responsable = Role::firstOrCreate(['name' => 'Responsable de Unidad', 'guard_name' => 'web']);
        $responsable->syncPermissions([
            'control-interno.ver', 'control-interno.crear', 'control-interno.editar',
            'integridad.ver', 'integridad.editar',
            'evidencias.ver', 'evidencias.subir',
            'reportes.ver',
            'reconocimientos.ver',
            'alertas.ver',
        ]);

        // Operador — registra y sube evidencias
        $operador = Role::firstOrCreate(['name' => 'Operador', 'guard_name' => 'web']);
        $operador->syncPermissions([
            'control-interno.ver', 'control-interno.editar',
            'integridad.ver',
            'evidencias.ver', 'evidencias.subir',
            'reportes.ver',
            'alertas.ver',
        ]);

        // Visualizador — solo lectura
        $visualizador = Role::firstOrCreate(['name' => 'Visualizador', 'guard_name' => 'web']);
        $visualizador->syncPermissions([
            'control-interno.ver',
            'integridad.ver',
            'evidencias.ver',
            'reportes.ver',
            'reconocimientos.ver',
            'alertas.ver',
        ]);
    }
}
