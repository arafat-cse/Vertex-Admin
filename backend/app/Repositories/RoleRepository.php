<?php

namespace App\Repositories;

use App\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    /**
     * Get all roles with their associated permissions.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return Role::with('permissions')->orderBy('name')->get();
    }

    /**
     * Find a role by its ID (throws ModelNotFoundException if not found).
     *
     * @param  int  $id
     * @return Role
     *
     * @throws ModelNotFoundException
     */
    public function findById(int $id): Role
    {
        return Role::with('permissions')->findOrFail($id);
    }

    /**
     * Create a new Spatie role.
     *
     * Accepted data keys:
     *   name         string  (required)
     *   guard_name   string  (optional, defaults to 'web')
     *   permissions  array   (optional) — array of permission ids or names
     *
     * @param  array  $data
     * @return Role
     */
    public function create(array $data): Role
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $role = Role::create([
            'name'       => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);

        if (!empty($permissions)) {
            $permissionModels = $this->resolvePermissions($permissions);
            $role->syncPermissions($permissionModels);
        }

        return $role->load('permissions');
    }

    /**
     * Update an existing role by ID.
     *
     * Accepted data keys:
     *   name         string  (optional)
     *   guard_name   string  (optional)
     *   permissions  array   (optional) — array of permission ids or names; syncs when present
     *
     * @param  int    $id
     * @param  array  $data
     * @return Role
     *
     * @throws ModelNotFoundException
     */
    public function update(int $id, array $data): Role
    {
        $role        = Role::findOrFail($id);
        $permissions = $data['permissions'] ?? null;
        unset($data['permissions']);

        $updateData = array_filter([
            'name'       => $data['name']       ?? null,
            'guard_name' => $data['guard_name'] ?? null,
        ], fn ($v) => $v !== null);

        if (!empty($updateData)) {
            $role->update($updateData);
        }

        if ($permissions !== null) {
            $permissionModels = $this->resolvePermissions($permissions);
            $role->syncPermissions($permissionModels);
        }

        return $role->load('permissions');
    }

    /**
     * Delete a role by ID.
     *
     * @param  int  $id
     * @return bool
     *
     * @throws ModelNotFoundException
     */
    public function delete(int $id): bool
    {
        $role = Role::findOrFail($id);

        return (bool) $role->delete();
    }

    /**
     * Sync permissions on a role, replacing any previously assigned permissions.
     *
     * @param  int    $id
     * @param  array  $permissions  Array of permission ids or permission name strings
     * @return Role
     *
     * @throws ModelNotFoundException
     */
    public function assignPermissions(int $id, array $permissions): Role
    {
        $role             = Role::findOrFail($id);
        $permissionModels = $this->resolvePermissions($permissions);
        $role->syncPermissions($permissionModels);

        return $role->load('permissions');
    }

    /**
     * Get all permissions currently assigned to a role.
     *
     * @param  int  $id
     * @return Collection
     *
     * @throws ModelNotFoundException
     */
    public function getPermissions(int $id): Collection
    {
        $role = Role::findOrFail($id);

        return $role->permissions;
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    /**
     * Resolve a mixed array of permission ids and/or names into Permission models.
     *
     * @param  array  $permissions
     * @return Collection
     */
    protected function resolvePermissions(array $permissions): Collection
    {
        if (empty($permissions)) {
            return new Collection();
        }

        $ids   = array_filter($permissions, fn ($p) => is_int($p) || ctype_digit((string) $p));
        $names = array_filter($permissions, fn ($p) => is_string($p) && !ctype_digit($p));

        $query = Permission::query();

        if (!empty($ids) && !empty($names)) {
            $query->where(function ($q) use ($ids, $names) {
                $q->whereIn('id', array_values($ids))
                  ->orWhereIn('name', array_values($names));
            });
        } elseif (!empty($ids)) {
            $query->whereIn('id', array_values($ids));
        } else {
            $query->whereIn('name', array_values($names));
        }

        return $query->get();
    }
}
