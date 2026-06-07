<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{
    public function show()
    {
        return view('profile.show', ['user' => Auth::user()]);
    }

    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'dni'   => ['nullable', 'string', 'digits:8'],
            'cargo' => ['nullable', 'string', 'max:255'],
            'foto'  => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('foto')->store('profile-photos', 'public');
            $user->forceFill(['profile_photo_path' => $path])->save();
        }

        if ($request->boolean('remove_foto') && $user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->forceFill(['profile_photo_path' => null])->save();
        }

        $user->forceFill([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'dni'   => $validated['dni'] ?? $user->dni,
            'cargo' => $validated['cargo'] ?? $user->cargo,
        ])->save();

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => ['required'],
            'password'              => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }

        $user->forceFill(['password' => Hash::make($request->password)])->save();

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }

    public function deletePhoto()
    {
        $user = Auth::user();
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->forceFill(['profile_photo_path' => null])->save();
        }
        return back()->with('success', 'Foto de perfil eliminada.');
    }
}
