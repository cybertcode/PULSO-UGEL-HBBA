<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\UnidadOrganica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserList extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('usuarios.ver');

        $stats = [
            'total'        => User::count(),
            'admins'       => User::whereHas('roles', fn($q) => $q->where('name', 'Administrador'))->count(),
            'responsables' => User::whereHas('roles', fn($q) => $q->where('name', 'Responsable de Unidad'))->count(),
            'pendientes'   => User::whereNull('email_verified_at')->orWhere('estado', 'pendiente')->count(),
        ];

        $usuarios = User::with(['roles', 'unidadOrganica'])
            ->when($request->rol,    fn($q) => $q->whereHas('roles', fn($r) => $r->where('name', $request->rol)))
            ->when($request->unidad, fn($q) => $q->where('unidad_organica_id', $request->unidad))
            ->when($request->estado, fn($q) => $q->where('estado', $request->estado))
            ->latest()->paginate(20)->withQueryString();

        $roles    = Role::orderBy('name')->get();
        $unidades = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();

        return view('content.apps.app-user-list', compact('stats', 'usuarios', 'roles', 'unidades'));
    }

    public function store(Request $request)
    {
        $this->authorize('usuarios.crear');

        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'required|email|unique:users,email',
            'password'           => ['required', Password::min(8)->mixedCase()->numbers()],
            'dni'                => 'nullable|string|size:8',
            'cargo'              => 'nullable|string|max:150',
            'unidad_organica_id' => 'nullable|exists:unidades_organicas,id',
            'estado'             => 'required|in:activo,inactivo,pendiente',
            'rol'                => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'               => $data['name'],
            'email'              => $data['email'],
            'password'           => Hash::make($data['password']),
            'dni'                => $data['dni'] ?? null,
            'cargo'              => $data['cargo'] ?? null,
            'unidad_organica_id' => $data['unidad_organica_id'] ?? null,
            'estado'             => $data['estado'],
            'email_verified_at'  => now(),
        ]);

        $user->assignRole($data['rol']);

        return redirect()->route('adm-usuarios')->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $usuario)
    {
        $this->authorize('usuarios.editar');

        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'required|email|unique:users,email,' . $usuario->id,
            'password'           => ['nullable', Password::min(8)->mixedCase()->numbers()],
            'dni'                => 'nullable|string|size:8',
            'cargo'              => 'nullable|string|max:150',
            'unidad_organica_id' => 'nullable|exists:unidades_organicas,id',
            'estado'             => 'required|in:activo,inactivo,pendiente',
            'rol'                => 'required|exists:roles,name',
        ]);

        $usuario->update([
            'name'               => $data['name'],
            'email'              => $data['email'],
            'dni'                => $data['dni'] ?? null,
            'cargo'              => $data['cargo'] ?? null,
            'unidad_organica_id' => $data['unidad_organica_id'] ?? null,
            'estado'             => $data['estado'],
        ]);

        if (!empty($data['password'])) {
            $usuario->update(['password' => Hash::make($data['password'])]);
        }

        $usuario->syncRoles([$data['rol']]);

        return redirect()->route('adm-usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        $this->authorize('usuarios.eliminar');

        if ($usuario->id === auth()->id()) {
            return redirect()->route('adm-usuarios')->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('adm-usuarios')->with('success', 'Usuario eliminado correctamente.');
    }

    public function toggleEstado(User $usuario)
    {
        $this->authorize('usuarios.editar');

        $usuario->update([
            'estado' => $usuario->estado === 'activo' ? 'inactivo' : 'activo',
        ]);

        return response()->json(['estado' => $usuario->estado]);
    }
}
