<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Services\PermissionService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly PermissionService $permissionService
    ) {}

    /**
     * Return all permissions.
     *
     * GET /api/permissions
     */
    public function index(): JsonResponse
    {
        try {
            $permissions = $this->permissionService->getAll();

            return $this->successResponse($permissions, 'Permissions retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve permissions.', 500, $e->getMessage());
        }
    }

    /**
     * Create a new permission.
     *
     * POST /api/permissions
     */
    public function store(StorePermissionRequest $request): JsonResponse
    {
        try {
            $permission = $this->permissionService->create($request->validated());

            return $this->createdResponse($permission, 'Permission created successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to create permission.', 500, $e->getMessage());
        }
    }

    /**
     * Return a single permission by ID.
     *
     * GET /api/permissions/{id}
     */
    public function show(int $id): JsonResponse
    {
        try {
            $permission = $this->permissionService->findById($id);

            return $this->successResponse($permission, 'Permission retrieved successfully.');
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse("Permission #{$id} not found.");
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve permission.', 500, $e->getMessage());
        }
    }

    /**
     * Update an existing permission.
     *
     * PUT/PATCH /api/permissions/{id}
     */
    public function update(UpdatePermissionRequest $request, int $id): JsonResponse
    {
        try {
            $permission = $this->permissionService->update($id, $request->validated());

            return $this->successResponse($permission, 'Permission updated successfully.');
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse("Permission #{$id} not found.");
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to update permission.', 500, $e->getMessage());
        }
    }

    /**
     * Delete a permission by ID.
     *
     * DELETE /api/permissions/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->permissionService->delete($id);

            return $this->successResponse(null, 'Permission deleted successfully.');
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse("Permission #{$id} not found.");
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to delete permission.', 500, $e->getMessage());
        }
    }

    /**
     * Return all permissions grouped by their dot-notation prefix.
     *
     * GET /api/permissions/groups
     */
    public function groups(): JsonResponse
    {
        try {
            $groups = $this->permissionService->getGroups();

            return $this->successResponse($groups, 'Permission groups retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve permission groups.', 500, $e->getMessage());
        }
    }
}
