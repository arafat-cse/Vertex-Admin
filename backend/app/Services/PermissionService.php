<?php

namespace App\Services;

use App\Http\Resources\PermissionResource;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PermissionService
{
    /**
     * Create a new PermissionService instance.
     */
    public function __construct(
        private readonly PermissionRepositoryInterface $permissionRepository
    ) {}

    /**
     * Return all permissions as a resource collection.
     *
     * @return AnonymousResourceCollection
     */
    public function getAll(): AnonymousResourceCollection
    {
        $permissions = $this->permissionRepository->getAll();

        return PermissionResource::collection($permissions);
    }

    /**
     * Find a single permission by ID and return it as a resource.
     *
     * @param  int  $id
     * @return PermissionResource
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findById(int $id): PermissionResource
    {
        $permission = $this->permissionRepository->findById($id);

        if (! $permission) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Permission #{$id} not found.");
        }

        return new PermissionResource($permission);
    }

    /**
     * Create a new permission and return it as a resource.
     *
     * @param  array  $data  Keys: name (string), guard_name (string, optional)
     * @return PermissionResource
     */
    public function create(array $data): PermissionResource
    {
        $permission = $this->permissionRepository->create([
            'name'       => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);

        return new PermissionResource($permission);
    }

    /**
     * Update an existing permission by ID and return the updated resource.
     *
     * @param  int    $id
     * @param  array  $data  Keys: name (string, optional), guard_name (string, optional)
     * @return PermissionResource
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, array $data): PermissionResource
    {
        $permission = $this->permissionRepository->update($id, $data);

        return new PermissionResource($permission);
    }

    /**
     * Delete a permission by ID.
     *
     * @param  int  $id
     * @return bool
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): bool
    {
        $permission = $this->permissionRepository->findById($id);

        if (! $permission) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Permission #{$id} not found.");
        }

        return $this->permissionRepository->delete($id);
    }

    /**
     * Return all permissions grouped by their dot-notation prefix.
     *
     * Each group key is the prefix (e.g. "users", "roles") and the value is
     * an array of PermissionResource-shaped arrays for that group.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function getGroups(): array
    {
        $groups = $this->permissionRepository->getGroups();

        // $groups is expected to be a Collection keyed by group name,
        // each value being a Collection of permission models.
        return $groups->map(function ($permissions) {
            return PermissionResource::collection($permissions)->resolve();
        })->toArray();
    }
}
