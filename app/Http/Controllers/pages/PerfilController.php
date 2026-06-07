<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{
    public function show()
    {
        return view('profile.show', ['user' => Auth::user()]);
    }

    public function updateInfo(Request $request, ImageService $images)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'dni'   => ['nullable', 'string', 'digits:8'],
            'cargo' => ['nullable', 'string', 'max:255'],
            'foto'  => ['nullable', 'image', 'mimes:' . ImageService::ALLOWED_MIMES, 'max:' . ImageService::MAX_SIZE_KB],
        ]);

        if ($request->hasFile('foto')) {
            $images->delete($user->profile_photo_path);
            $path = $images->store($request->file('foto'), 'profile-photos');
            $user->forceFill(['profile_photo_path' => $path])->save();
        }

        if ($request->boolean('remove_foto') && $user->profile_photo_path) {
            $images->delete($user->profile_photo_path);
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

    public function deletePhoto(ImageService $images)
    {
        $user = Auth::user();
        if ($user->profile_photo_path) {
            $images->delete($user->profile_photo_path);
            $user->forceFill(['profile_photo_path' => null])->save();
        }
        return back()->with('success', 'Foto de perfil eliminada.');
    }
}
