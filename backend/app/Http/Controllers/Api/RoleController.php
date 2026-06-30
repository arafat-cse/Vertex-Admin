<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\AssignPermissionsRequest;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Services\RoleService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly RoleService $roleService
    ) {}

    /**
     * Return all roles.
     *
     * GET /api/roles
     */
    public function index(): JsonResponse
    {
        try {
            $roles = $this->roleService->getAll();

            return $this->successResponse($roles, 'Roles retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve roles.', 500, $e->getMessage());
        }
    }

    /**
     * Create a new role.
     *
     * POST /api/roles
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        try {
            $role = $this->roleService->create($request->validated());

            return $this->createdResponse($role, 'Role created successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to create role.', 500, $e->getMessage());
        }
    }

    /**
     * Return a single role by ID.
     *
     * GET /api/roles/{id}
     */
    public function show(int $id): JsonResponse
    {
        try {
            $role = $this->roleService->findById($id);

            return $this->successResponse($role, 'Role retrieved successfully.');
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse("Role #{$id} not found.");
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve role.', 500, $e->getMessage());
        }
    }

    /**
     * Update an existing role.
     *
     * PUT/PATCH /api/roles/{id}
     */
    public function update(UpdateRoleRequest $request, int $id): JsonResponse
    {
        try {
            $role = $this->roleService->update($id, $request->validated());

            return $this->successResponse($role, 'Role updated successfully.');
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse("Role #{$id} not found.");
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to update role.', 500, $e->getMessage());
        }
    }

    /**
     * Delete a role by ID.
     *
     * DELETE /api/roles/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->roleService->delete($id);

            return $this->successResponse(null, 'Role deleted successfully.');
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse("Role #{$id} not found.");
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to delete role.', 500, $e->getMessage());
        }
    }

    /**
     * Sync permissions on a role.
     *
     * POST /api/roles/{id}/assign-permissions
     */
    public function assignPermissions(AssignPermissionsRequest $request, int $id): JsonResponse
    {
        try {
            $role = $this->roleService->assignPermissions($id, $request->validated('permissions'));

            return $this->successResponse($role, 'Permissions assigned successfully.');
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse("Role #{$id} not found.");
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to assign permissions.', 500, $e->getMessage());
        }
    }

    /**
     * Return all permissions for a given role.
     *
     * GET /api/roles/{id}/permissions
     */
    public function permissions(int $id): JsonResponse
    {
        try {
            $permissions = $this->roleService->getPermissions($id);

            return $this->successResponse($permissions, 'Role permissions retrieved successfully.');
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse("Role #{$id} not found.");
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve role permissions.', 500, $e->getMessage());
        }
    }
}
