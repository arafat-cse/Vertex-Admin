<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Services\ProfileService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly ProfileService $profileService
    ) {}

    /**
     * Get the authenticated user's profile.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $profile = $this->profileService->getProfile($request->user());

            return $this->successResponse($profile, 'Profile retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve profile.', 500, $e->getMessage());
        }
    }

    /**
     * Update the authenticated user's profile information.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $profile = $this->profileService->updateProfile($request->user(), $request->validated());

            return $this->successResponse($profile, 'Profile updated successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to update profile.', 500, $e->getMessage());
        }
    }

    /**
     * Upload a new avatar for the authenticated user.
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        try {
            $result = $this->profileService->uploadAvatar($request->user(), $request->file('avatar'));

            return $this->successResponse($result, 'Avatar uploaded successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to upload avatar.', 500, $e->getMessage());
        }
    }

    /**
     * Change the authenticated user's password.
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $this->profileService->changePassword($request->user(), $request->validated());

            return $this->successResponse(null, 'Password changed successfully.');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to change password.', 500, $e->getMessage());
        }
    }
}
