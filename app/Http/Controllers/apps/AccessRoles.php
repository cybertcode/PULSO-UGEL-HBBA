<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccessRoles extends Controller
{
    public function index()
    {
        $this->authorize('configuracion.ver');

        $roles = Role::with('permissions')
            ->withCount('users')
            ->orderBy('name')
            ->get();

        $permisos = Permission::orderBy('name')->get()->groupBy(fn($p) => explode('.', $p->name)[0]);

        return view('content.apps.app-access-roles', compact('roles', 'permisos'));
    }

    public function store(Request $request)
    {
        $this->authorize('configuracion.editar');

        $data = $request->validate([
            'name'       => 'required|string|max:100|unique:roles,name',
            'permisos'   => 'nullable|array',
            'permisos.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        if (!empty($data['permisos'])) {
            $role->syncPermissions($data['permisos']);
        }

        return redirect()->route('adm-roles')->with('success', 'Rol creado correctamente.');
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('configuracion.editar');

        $data = $request->validate([
            'name'       => 'required|string|max:100|unique:roles,name,' . $role->id,
            'permisos'   => 'nullable|array',
            'permisos.*' => 'exists:permissions,name',
        ]);

        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permisos'] ?? []);

        return redirect()->route('adm-roles')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role)
    {
        $this->authorize('configuracion.editar');

        if ($role->users()->count() > 0) {
            return redirect()->route('adm-roles')->with('error', 'No se puede eliminar un rol con usuarios asignados.');
        }

        $role->delete();

        return redirect()->route('adm-roles')->with('success', 'Rol eliminado correctamente.');
    }
}
