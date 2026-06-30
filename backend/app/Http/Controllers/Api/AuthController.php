<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Authenticate a user and issue a Sanctum API token.
     *
     * POST /api/auth/login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());

            return $this->successResponse($result, 'Login successful.');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Throwable $e) {
            return $this->errorResponse('Authentication failed.', 500, $e->getMessage());
        }
    }

    /**
     * Revoke the authenticated user's current token.
     *
     * POST /api/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request->user());

            return $this->successResponse(null, 'Logged out successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Logout failed.', 500, $e->getMessage());
        }
    }

    /**
     * Return the authenticated user's profile.
     *
     * GET /api/auth/me
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $this->authService->me($request->user());

            return $this->successResponse($user, 'Authenticated user retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve user profile.', 500, $e->getMessage());
        }
    }

    /**
     * Send a password-reset link to the given email address.
     *
     * POST /api/auth/forgot-password
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $status = $this->authService->forgotPassword($request->validated('email'));

            return $this->successResponse(['status' => $status], 'Password reset link sent successfully.');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to send password reset link.', 500, $e->getMessage());
        }
    }

    /**
     * Reset the user's password using the provided token.
     *
     * POST /api/auth/reset-password
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $status = $this->authService->resetPassword($request->validated());

            return $this->successResponse(['status' => $status], 'Password reset successfully.');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to reset password.', 500, $e->getMessage());
        }
    }
}
