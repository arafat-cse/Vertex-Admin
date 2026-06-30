<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateEmailSettingsRequest;
use App\Http\Requests\Settings\UpdateSettingsRequest;
use App\Services\SettingsService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly SettingsService $settingsService
    ) {}

    /**
     * Get all settings.
     */
    public function index(): JsonResponse
    {
        try {
            $settings = $this->settingsService->getAllSettings();

            return $this->successResponse($settings, 'Settings retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve settings.', 500, $e->getMessage());
        }
    }

    /**
     * Update general settings.
     */
    public function updateGeneral(UpdateSettingsRequest $request): JsonResponse
    {
        try {
            $settings = $this->settingsService->updateGeneralSettings($request->validated());

            return $this->successResponse($settings, 'General settings updated successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to update general settings.', 500, $e->getMessage());
        }
    }

    /**
     * Update email/mail settings.
     */
    public function updateEmail(UpdateEmailSettingsRequest $request): JsonResponse
    {
        try {
            $settings = $this->settingsService->updateEmailSettings($request->validated());

            return $this->successResponse($settings, 'Email settings updated successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to update email settings.', 500, $e->getMessage());
        }
    }

    /**
     * Upload company logo.
     */
    public function uploadLogo(Request $request): JsonResponse
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ]);

        try {
            $result = $this->settingsService->uploadLogo($request->file('logo'));

            return $this->successResponse($result, 'Logo uploaded successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to upload logo.', 500, $e->getMessage());
        }
    }

    /**
     * Upload company favicon.
     */
    public function uploadFavicon(Request $request): JsonResponse
    {
        $request->validate([
            'favicon' => ['required', 'image', 'mimes:ico,png,jpg,jpeg,gif,svg,webp', 'max:1024'],
        ]);

        try {
            $result = $this->settingsService->uploadFavicon($request->file('favicon'));

            return $this->successResponse($result, 'Favicon uploaded successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to upload favicon.', 500, $e->getMessage());
        }
    }
}
