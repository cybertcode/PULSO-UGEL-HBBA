<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\UnidadOrganica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserList extends Controller
{
    public function index()
    {
        $stats = [
            'total'      => User::count(),
            'activos'    => User::where('estado', 'activo')->count(),
            'inactivos'  => User::where('estado', 'inactivo')->count(),
            'pendientes' => User::where('estado', 'pendiente')->count(),
        ];

        $roles    = Role::orderBy('name')->get();
        $unidades = UnidadOrganica::where('activo', true)->orderBy('nombre')->get();

        return view('content.apps.app-user-list', compact('stats', 'roles', 'unidades'));
    }

    public function data(Request $request)
    {
        $colorMap = [
            'Super Admin'           => 'danger',
            'Administrador'         => 'primary',
            'Responsable de Unidad' => 'success',
            'Operador'              => 'info',
            'Visualizador'          => 'secondary',
        ];

        $query = User::with(['roles', 'unidadOrganica', 'cargo'])->select('users.*');

        if ($request->filled('rol')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->rol));
        }
        if ($request->filled('unidad_id')) {
            $query->where('unidad_organica_id', $request->unidad_id);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%")
                ->orWhere('dni', 'like', "%$s%")
            );
        }

        // Server-side ordering
        $orderCol = match((int) $request->input('order.0.column', 7)) {
            2 => 'name',
            6 => 'estado',
            default => 'created_at',
        };
        $orderDir = $request->input('order.0.dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($orderCol, $orderDir);

        $total    = $query->count();
        $start    = (int) $request->input('start', 0);
        $length   = (int) $request->input('length', 10);
        $filtered = $total;

        $usuarios = $query->offset($start)->limit($length)->get()->map(function ($u) use ($colorMap) {
            $rolNombre = $u->roles->first()->name ?? '—';
            $initials  = collect(explode(' ', $u->name))
                ->filter()->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');

            return [
                'id'        => $u->id,
                'name'      => $u->name,
                'email'     => $u->email,
                'dni'       => $u->dni ?? '—',
                'cargo'     => $u->cargo?->nombre ?? '—',
                'cargo_id'  => $u->cargo_id ?? '',
                'unidad'    => $u->unidadOrganica?->sigla ?? '—',
                'unidad_id' => $u->unidad_organica_id ?? '',
                'rol'       => $rolNombre,
                'estado'    => $u->estado ?? 'pendiente',
                'initials'  => $initials ?: strtoupper(substr($u->name, 0, 1)),
                'created_ts'=> $u->created_at?->timestamp ?? 0,
            ];
        });

        return response()->json([
            'draw'            => (int) $request->input('draw', 1),
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $usuarios,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'required|email|unique:users,email',
            'password'           => ['required', Password::min(8)->mixedCase()->numbers()],
            'dni'                => 'nullable|digits:8',
            'cargo_id'           => 'nullable|exists:cargos,id',
            'unidad_organica_id' => 'nullable|exists:unidades_organicas,id',
            'estado'             => 'required|in:activo,inactivo,pendiente',
            'rol'                => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'               => $data['name'],
            'email'              => $data['email'],
            'password'           => Hash::make($data['password']),
            'dni'                => $data['dni'] ?? null,
            'cargo_id'           => $data['cargo_id'] ?? null,
            'unidad_organica_id' => $data['unidad_organica_id'] ?? null,
            'estado'             => $data['estado'],
            'email_verified_at'  => now(),
        ]);

        $user->assignRole($data['rol']);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Usuario creado correctamente.']);
        }

        return redirect()->route('adm-usuarios')->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'required|email|unique:users,email,' . $usuario->id,
            'password'           => ['nullable', Password::min(8)->mixedCase()->numbers()],
            'dni'                => 'nullable|digits:8',
            'cargo_id'           => 'nullable|exists:cargos,id',
            'unidad_organica_id' => 'nullable|exists:unidades_organicas,id',
            'estado'             => 'required|in:activo,inactivo,pendiente',
            'rol'                => 'required|exists:roles,name',
        ]);

        $usuario->update([
            'name'               => $data['name'],
            'email'              => $data['email'],
            'dni'                => $data['dni'] ?? null,
            'cargo_id'           => $data['cargo_id'] ?? null,
            'unidad_organica_id' => $data['unidad_organica_id'] ?? null,
            'estado'             => $data['estado'],
        ]);

        if (!empty($data['password'])) {
            $usuario->update(['password' => Hash::make($data['password'])]);
        }

        $usuario->syncRoles([$data['rol']]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Usuario actualizado correctamente.']);
        }

        return redirect()->route('adm-usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'No puedes eliminar tu propia cuenta.'], 422);
            }
            return redirect()->route('adm-usuarios')->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente.']);
        }

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
