<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Spatie\Permission\PermissionRegistrar;

class AccessRoles extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();

        // Count and load users per role via pivot to avoid guard_name resolution issues
        $userRows = DB::table('model_has_roles')
            ->where('model_type', User::class)
            ->selectRaw('role_id, COUNT(*) as total')
            ->groupBy('role_id')
            ->pluck('total', 'role_id');

        // Load a preview of users (max 4) per role for avatar display
        $usersByRole = DB::table('model_has_roles')
            ->where('model_type', User::class)
            ->join('users', 'users.id', '=', 'model_has_roles.model_id')
            ->select('model_has_roles.role_id', 'users.id', 'users.name')
            ->get()
            ->groupBy('role_id');

        $roles->each(function ($r) use ($userRows, $usersByRole) {
            $r->users_count = $userRows->get($r->id, 0);
            $r->usuarios    = collect($usersByRole->get($r->id, []));
        });

        $permisos = Permission::orderBy('name')->get()->groupBy(fn ($p) => explode('.', $p->name)[0]);

        $usuarios = User::with('roles')->orderBy('name')->get();

        return view('content.apps.app-access-roles', compact('roles', 'permisos', 'usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100|unique:roles,name',
            'permisos'   => 'nullable|array',
            'permisos.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        if (!empty($data['permisos'])) {
            $role->syncPermissions($data['permisos']);
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('adm-roles')->with('success', 'Rol creado correctamente.');
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100|unique:roles,name,' . $role->id,
            'permisos'   => 'nullable|array',
            'permisos.*' => 'exists:permissions,name',
        ]);

        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permisos'] ?? []);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('adm-roles')->with('success', 'Rol actualizado correctamente.');
    }

    public function cambiarRol(Request $request, User $usuario)
    {
        $data = $request->validate([
            'rol'    => ['required', Rule::exists('roles', 'name')->where('guard_name', 'web')],
            'accion' => 'nullable|in:agregar,quitar',
        ]);

        if ($usuario->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'No puedes cambiar tu propio rol.'], 422);
        }

        $accion = $data['accion'] ?? 'toggle';
        $tieneRol = $usuario->hasRole($data['rol']);

        if ($accion === 'quitar' || ($accion === 'toggle' && $tieneRol)) {
            // Evitar quitar el último rol
            if ($usuario->roles->count() <= 1) {
                return response()->json(['success' => false, 'message' => 'El usuario debe tener al menos un rol asignado.'], 422);
            }
            $usuario->removeRole($data['rol']);
            $mensaje = "Rol \"{$data['rol']}\" quitado a {$usuario->name}.";
        } else {
            $usuario->assignRole($data['rol']);
            $mensaje = "Rol \"{$data['rol']}\" asignado a {$usuario->name}.";
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $usuario->load('roles');

        return response()->json([
            'success' => true,
            'message' => $mensaje,
            'roles'   => $usuario->roles->pluck('name'),
        ]);
    }

    public function destroy(Role $role)
    {
        $count = DB::table('model_has_roles')
            ->where('model_type', User::class)
            ->where('role_id', $role->id)
            ->count();

        if ($count > 0) {
            return redirect()->route('adm-roles')->with('error', 'No se puede eliminar un rol con usuarios asignados.');
        }

        $role->delete();

        return redirect()->route('adm-roles')->with('success', 'Rol eliminado correctamente.');
    }
}
