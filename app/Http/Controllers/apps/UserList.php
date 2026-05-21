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
        $stats = [
            'total'        => User::count(),
            'activos'      => User::where('estado', 'activo')->count(),
            'inactivos'    => User::where('estado', 'inactivo')->count(),
            'pendientes'   => User::where('estado', 'pendiente')->orWhereNull('email_verified_at')->count(),
        ];

        $roles    = Role::orderBy('name')->get();
        $unidades = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();

        return view('content.apps.app-user-list', compact('stats', 'roles', 'unidades'));
    }

    public function data(Request $request)
    {
        $query = User::with(['roles', 'unidadOrganica'])->select('users.*');

        if ($request->filled('rol')) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $request->rol));
        }
        if ($request->filled('unidad')) {
            $query->where('unidad_organica_id', $request->unidad);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $colorMap = [
            'Super Admin'          => 'danger',
            'Administrador'        => 'primary',
            'Responsable de Unidad'=> 'success',
            'Operador'             => 'info',
            'Visualizador'         => 'secondary',
        ];

        $usuarios = $query->latest()->get()->map(function ($u) use ($colorMap) {
            $rolNombre = $u->roles->first()->name ?? '—';
            $rolColor  = $colorMap[$rolNombre] ?? 'secondary';
            $estadoColor = match($u->estado) {
                'activo'   => 'success',
                'inactivo' => 'secondary',
                default    => 'warning',
            };
            $initials = strtoupper(substr($u->name, 0, 1));

            return [
                'id'      => $u->id,
                'name'    => $u->name,
                'email'   => $u->email,
                'dni'     => $u->dni ?? '—',
                'cargo'   => $u->cargo ?? '—',
                'unidad'  => $u->unidadOrganica->sigla ?? '—',
                'rol'     => $rolNombre,
                'rol_color' => $rolColor,
                'estado'  => $u->estado ?? 'pendiente',
                'estado_color' => $estadoColor,
                'initials'=> $initials,
                'avatar'  => $u->profile_photo_url ?? null,
            ];
        });

        return response()->json(['data' => $usuarios]);
    }

    public function store(Request $request)
    {
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
        if ($usuario->id === auth()->id()) {
            return redirect()->route('adm-usuarios')->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('adm-usuarios')->with('success', 'Usuario eliminado correctamente.');
    }

    public function toggleEstado(User $usuario)
    {
        $usuario->update([
            'estado' => $usuario->estado === 'activo' ? 'inactivo' : 'activo',
        ]);

        return response()->json(['estado' => $usuario->estado]);
    }
}
