<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'dni'   => ['nullable', 'string', 'digits:8'],
            'cargo' => ['nullable', 'string', 'max:255'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if ($input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name'  => $input['name'],
                'email' => $input['email'],
                'dni'   => $input['dni'] ?? $user->dni,
                'cargo' => $input['cargo'] ?? $user->cargo,
            ])->save();
        }
    }

    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name'               => $input['name'],
            'email'              => $input['email'],
            'email_verified_at'  => null,
            'dni'                => $input['dni'] ?? $user->dni,
            'cargo'              => $input['cargo'] ?? $user->cargo,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
