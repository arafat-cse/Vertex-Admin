<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AssignRoleRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\UserService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * Return a paginated list of users, optionally filtered.
     *
     * GET /api/users
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'search',
                'role',
                'status',
                'date_from',
                'date_to',
                'sort_by',
                'sort_order',
            ]);

            $perPage = (int) ($request->query('per_page', 15));

            $paginator = $this->userService->getAll($filters, $perPage);

            return $this->successResponse([
                'data'  => $paginator->items(),
                'meta'  => [
                    'current_page' => $paginator->currentPage(),
                    'last_page'    => $paginator->lastPage(),
                    'per_page'     => $paginator->perPage(),
                    'total'        => $paginator->total(),
                ],
                'links' => [
                    'first' => $paginator->url(1),
                    'last'  => $paginator->url($paginator->lastPage()),
                    'prev'  => $paginator->previousPageUrl(),
                    'next'  => $paginator->nextPageUrl(),
                ],
            ], 'Users retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve users.', 500, $e->getMessage());
        }
    }

    /**
     * Create a new user.
     *
     * POST /api/users
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->create($request->validated());

            return $this->createdResponse($user, 'User created successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to create user.', 500, $e->getMessage());
        }
    }

    /**
     * Return a single user by ID.
     *
     * GET /api/users/{id}
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = $this->userService->findById($id);

            return $this->successResponse($user, 'User retrieved successfully.');
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse("User #{$id} not found.");
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve user.', 500, $e->getMessage());
        }
    }

    /**
     * Update an existing user.
     *
     * PUT/PATCH /api/users/{id}
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        try {
            $user = $this->userService->update($id, $request->validated());

            return $this->successResponse($user, 'User updated successfully.');
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse("User #{$id} not found.");
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to update user.', 500, $e->getMessage());
        }
    }

    /**
     * Soft-delete a user.
     *
     * DELETE /api/users/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->userService->delete($id);

            return $this->successResponse(null, 'User deleted successfully.');
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse("User #{$id} not found.");
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to delete user.', 500, $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted user.
     *
     * POST /api/users/{id}/restore
     */
    public function restore(int $id): JsonResponse
    {
        try {
            $user = $this->userService->restore($id);

            return $this->successResponse($user, 'User restored successfully.');
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse("User #{$id} not found.");
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to restore user.', 500, $e->getMessage());
        }
    }

    /**
     * Return a paginated list of soft-deleted users.
     *
     * GET /api/users/trashed
     */
    public function trashed(Request $request): JsonResponse
    {
        try {
            $perPage = (int) ($request->query('per_page', 15));

            $paginator = $this->userService->getTrashed($perPage);

            return $this->successResponse([
                'data'  => $paginator->items(),
                'meta'  => [
                    'current_page' => $paginator->currentPage(),
                    'last_page'    => $paginator->lastPage(),
                    'per_page'     => $paginator->perPage(),
                    'total'        => $paginator->total(),
                ],
                'links' => [
                    'first' => $paginator->url(1),
                    'last'  => $paginator->url($paginator->lastPage()),
                    'prev'  => $paginator->previousPageUrl(),
                    'next'  => $paginator->nextPageUrl(),
                ],
            ], 'Trashed users retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve trashed users.', 500, $e->getMessage());
        }
    }

    /**
     * Activate a user account.
     *
     * POST /api/users/{id}/activate
     */
    public function activate(int $id): JsonResponse
    {
        try {
            $user = $this->userService->activate($id);

            return $this->successResponse($user, 'User activated successfully.');
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse("User #{$id} not found.");
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to activate user.', 500, $e->getMessage());
        }
    }

    /**
     * Deactivate a user account.
     *
     * POST /api/users/{id}/deactivate
     */
    public function deactivate(int $id): JsonResponse
    {
        try {
            $user = $this->userService->deactivate($id);

            return $this->successResponse($user, 'User deactivated successfully.');
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse("User #{$id} not found.");
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to deactivate user.', 500, $e->getMessage());
        }
    }

    /**
     * Assign a role to a user.
     *
     * POST /api/users/{id}/assign-role
     */
    public function assignRole(AssignRoleRequest $request, int $id): JsonResponse
    {
        try {
            $user = $this->userService->assignRole($id, (int) $request->validated('role_id'));

            return $this->successResponse($user, 'Role assigned successfully.');
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse("User #{$id} not found.");
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to assign role.', 500, $e->getMessage());
        }
    }
}
