<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class ProfileService
{
    public function __construct(
        private readonly FileUploadService $fileUploadService,
    ) {}

    /**
     * Return the authenticated user's profile as a resource.
     *
     * @param  User  $user
     * @return UserResource
     */
    public function getProfile(User $user): UserResource
    {
        // Eager-load roles and their permissions so they appear in the resource.
        $user->loadMissing(['roles', 'roles.permissions']);

        return new UserResource($user);
    }

    /**
     * Update the authenticated user's name and/or email.
     *
     * When the email address changes the email_verified_at timestamp is cleared
     * so the user must re-verify their new address.
     *
     * @param  User                  $user
     * @param  array<string, mixed>  $data  Accepted: name, email
     * @return UserResource
     */
    public function updateProfile(User $user, array $data): UserResource
    {
        $updatePayload = [];

        if (isset($data['name']) && $data['name'] !== $user->name) {
            $updatePayload['name'] = $data['name'];
        }

        if (isset($data['email']) && $data['email'] !== $user->email) {
            $updatePayload['email']             = $data['email'];
            $updatePayload['email_verified_at'] = null;
        }

        if (!empty($updatePayload)) {
            $user->update($updatePayload);
            $user->refresh();
        }

        $user->loadMissing(['roles', 'roles.permissions']);

        return new UserResource($user);
    }

    /**
     * Upload a new avatar for the authenticated user.
     *
     * The previous avatar file is removed from storage before the new one is
     * stored, preventing orphaned files from accumulating.
     *
     * @param  User          $user
     * @param  UploadedFile  $file
     * @return UserResource
     *
     * @throws InvalidArgumentException  Propagated from FileUploadService on validation failure
     */
    public function uploadAvatar(User $user, UploadedFile $file): UserResource
    {
        // Validate and store the new avatar.
        $newPath = $this->fileUploadService->upload(
            $file,
            'avatars',
            ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            2048
        );

        // Delete the old avatar if one existed.
        if (!empty($user->avatar)) {
            $this->fileUploadService->delete($user->avatar);
        }

        $user->update(['avatar' => $newPath]);
        $user->refresh();

        $user->loadMissing(['roles', 'roles.permissions']);

        return new UserResource($user);
    }

    /**
     * Change the authenticated user's password after verifying the current one.
     *
     * @param  User                  $user
     * @param  array<string, mixed>  $data  Required keys: current_password, password (new)
     * @return bool
     *
     * @throws InvalidArgumentException  When the current password does not match
     */
    public function changePassword(User $user, array $data): bool
    {
        if (!Hash::check($data['current_password'], $user->password)) {
            throw new InvalidArgumentException(
                'The current password you entered is incorrect.'
            );
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return true;
    }
}
