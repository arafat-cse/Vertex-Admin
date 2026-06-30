<?php

namespace App\Services;

use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoleService
{
    /**
     * Create a new RoleService instance.
     */
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository
    ) {}

    /**
     * Return all roles as a resource collection.
     *
     * @return AnonymousResourceCollection
     */
    public function getAll(): AnonymousResourceCollection
    {
        $roles = $this->roleRepository->getAll();

        return RoleResource::collection($roles);
    }

    /**
     * Find a single role by ID and return it as a resource.
     *
     * @param  int  $id
     * @return RoleResource
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findById(int $id): RoleResource
    {
        $role = $this->roleRepository->findById($id);

        if (! $role) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Role #{$id} not found.");
        }

        return new RoleResource($role->load('permissions'));
    }

    /**
     * Create a new role, optionally assigning permissions, and return the resource.
     *
     * @param  array  $data  Keys: name (string), guard_name (string, optional), permissions (array of names/IDs, optional)
     * @return RoleResource
     */
    public function create(array $data): RoleResource
    {
        $role = $this->roleRepository->create([
            'name'       => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);

        if (! empty($data['permissions'])) {
            $role = $this->roleRepository->assignPermissions($role->id, $data['permissions']);
        }

        return new RoleResource($role->load('permissions'));
    }

    /**
     * Update an existing role, sync its permissions, and return the resource.
     *
     * @param  int    $id
     * @param  array  $data  Keys: name (string, optional), guard_name (string, optional), permissions (array, optional)
     * @return RoleResource
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, array $data): RoleResource
    {
        $updatePayload = array_filter(
            ['name' => $data['name'] ?? null, 'guard_name' => $data['guard_name'] ?? null],
            fn ($v) => $v !== null
        );

        $role = $this->roleRepository->update($id, $updatePayload);

        // Always sync permissions when the key is present, even if the array is empty (removes all).
        if (array_key_exists('permissions', $data)) {
            $role = $this->roleRepository->assignPermissions($id, $data['permissions'] ?? []);
        }

        return new RoleResource($role->load('permissions'));
    }

    /**
     * Delete a role by ID, guarding against roles that still have users.
     *
     * @param  int  $id
     * @return bool
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \RuntimeException  When users are still assigned to this role.
     */
    public function delete(int $id): bool
    {
        $role = $this->roleRepository->findById($id);

        if (! $role) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Role #{$id} not found.");
        }

        if ($this->roleRepository->hasUsers($id)) {
            throw new \RuntimeException('Cannot delete a role that still has users assigned to it.');
        }

        return $this->roleRepository->delete($id);
    }

    /**
     * Sync the given permissions on a role and return the updated resource.
     *
     * @param  int    $id
     * @param  array  $permissions  Permission names or IDs.
     * @return RoleResource
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function assignPermissions(int $id, array $permissions): RoleResource
    {
        $role = $this->roleRepository->findById($id);

        if (! $role) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Role #{$id} not found.");
        }

        $role = $this->roleRepository->assignPermissions($id, $permissions);

        return new RoleResource($role->load('permissions'));
    }

    /**
     * Return all permissions assigned to the given role.
     *
     * @param  int  $id
     * @return AnonymousResourceCollection
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getPermissions(int $id): AnonymousResourceCollection
    {
        $role = $this->roleRepository->findById($id);

        if (! $role) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Role #{$id} not found.");
        }

        $permissions = $this->roleRepository->getPermissions($id);

        return PermissionResource::collection($permissions);
    }
}
