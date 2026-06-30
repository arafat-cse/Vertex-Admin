<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Attempt to authenticate a user and issue a Sanctum token.
     *
     * @param  array{email: string, password: string}  $credentials
     * @return array{user: UserResource, token: string}
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(array $credentials): array
    {
        if (! Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        // Update last login timestamp.
        $user->update(['last_login_at' => now()]);

        // Revoke all previous tokens so only one active session exists (optional — remove if multi-device is needed).
        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        $user->load('roles', 'permissions');

        return [
            'user'  => new UserResource($user),
            'token' => $token,
        ];
    }

    /**
     * Revoke the current access token of the given user.
     *
     * @param  User  $user
     * @return void
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Return the authenticated user wrapped in a resource.
     *
     * @param  User  $user
     * @return UserResource
     */
    public function me(User $user): UserResource
    {
        $user->load('roles', 'permissions');

        return new UserResource($user);
    }

    /**
     * Send a password-reset link to the given email address.
     *
     * @param  string  $email
     * @return string  The Password broker status constant.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function forgotPassword(string $email): string
    {
        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return $status;
    }

    /**
     * Reset the user's password using the provided token.
     *
     * @param  array{token: string, email: string, password: string, password_confirmation: string}  $data
     * @return string  The Password broker status constant.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function resetPassword(array $data): string
    {
        $status = Password::reset(
            [
                'token'                 => $data['token'],
                'email'                 => $data['email'],
                'password'              => $data['password'],
                'password_confirmation' => $data['password_confirmation'],
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return $status;
    }
}
