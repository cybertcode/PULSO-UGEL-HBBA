<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Permisos válidos en el sistema refactorizado (deben coincidir con RolesPermisosSeeder)
    private array $permisosValidos = [
        'usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar',
        'configuracion.ver', 'configuracion.editar',
        'componentes.ver', 'componentes.editar',
        'control-interno.ver', 'control-interno.crear', 'control-interno.editar', 'control-interno.eliminar',
        'integridad.ver', 'integridad.crear', 'integridad.editar', 'integridad.eliminar',
        'evidencias.ver', 'evidencias.subir', 'evidencias.validar', 'evidencias.eliminar',
        'semaforo.ver',
        'alertas.ver', 'alertas.crear', 'alertas.configurar', 'alertas.eliminar',
        'cumplimiento.ver',
        'reportes.ver', 'reportes.exportar',
        'reconocimientos.ver', 'reconocimientos.crear', 'reconocimientos.editar', 'reconocimientos.eliminar',
        'buenas-practicas.ver', 'buenas-practicas.crear', 'buenas-practicas.editar', 'buenas-practicas.eliminar',
        'recomendaciones.ver', 'recomendaciones.crear', 'recomendaciones.editar', 'recomendaciones.eliminar',
        'normativas.ver', 'normativas.gestionar',
        'encuesta.ver', 'encuesta.crear', 'encuesta.editar', 'encuesta.eliminar',
        'encuesta.publicar', 'encuesta.responder', 'encuesta.resultados', 'encuesta.exportar',
    ];

    public function up(): void
    {
        // 1. Eliminar permisos huérfanos (módulos ya eliminados: paci, riesgos, actas, autoevaluacion, etc.)
        $idsHuerfanos = DB::table('permissions')
            ->where('guard_name', 'web')
            ->whereNotIn('name', $this->permisosValidos)
            ->pluck('id');

        if ($idsHuerfanos->isNotEmpty()) {
            DB::table('role_has_permissions')->whereIn('permission_id', $idsHuerfanos)->delete();
            DB::table('model_has_permissions')->whereIn('permission_id', $idsHuerfanos)->delete();
            DB::table('permissions')->whereIn('id', $idsHuerfanos)->delete();
        }

        // 2. Eliminar rol de prueba "testeando" solo si no tiene usuarios asignados
        $rolPrueba = DB::table('roles')->where('name', 'testeando')->first();
        if ($rolPrueba) {
            $tieneUsuarios = DB::table('model_has_roles')
                ->where('role_id', $rolPrueba->id)
                ->exists();

            if (!$tieneUsuarios) {
                DB::table('role_has_permissions')->where('role_id', $rolPrueba->id)->delete();
                DB::table('roles')->where('id', $rolPrueba->id)->delete();
            }
        }

        // 3. Limpiar caché de permisos de Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        // No reversible — los permisos se recrean con: php artisan db:seed --class=RolesPermisosSeeder
    }
};
