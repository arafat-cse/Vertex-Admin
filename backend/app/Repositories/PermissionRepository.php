<?php

namespace App\Repositories;

use App\Repositories\Interfaces\PermissionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection as SupportCollection;
use Spatie\Permission\Models\Permission;

class PermissionRepository implements PermissionRepositoryInterface
{
    /**
     * Get all permissions ordered by name.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return Permission::orderBy('name')->get();
    }

    /**
     * Find a permission by its ID (throws ModelNotFoundException if not found).
     *
     * @param  int  $id
     * @return Permission
     *
     * @throws ModelNotFoundException
     */
    public function findById(int $id): Permission
    {
        return Permission::findOrFail($id);
    }

    /**
     * Create a new Spatie permission.
     *
     * Accepted data keys:
     *   name        string  (required)
     *   guard_name  string  (optional, defaults to 'web')
     *
     * @param  array  $data
     * @return Permission
     */
    public function create(array $data): Permission
    {
        return Permission::create([
            'name'       => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);
    }

    /**
     * Update an existing permission by ID.
     *
     * @param  int    $id
     * @param  array  $data
     * @return Permission
     *
     * @throws ModelNotFoundException
     */
    public function update(int $id, array $data): Permission
    {
        $permission = Permission::findOrFail($id);

        $updateData = array_filter([
            'name'       => $data['name']       ?? null,
            'guard_name' => $data['guard_name'] ?? null,
        ], fn ($v) => $v !== null);

        if (!empty($updateData)) {
            $permission->update($updateData);
        }

        return $permission->fresh();
    }

    /**
     * Delete a permission by ID.
     *
     * @param  int  $id
     * @return bool
     *
     * @throws ModelNotFoundException
     */
    public function delete(int $id): bool
    {
        $permission = Permission::findOrFail($id);

        return (bool) $permission->delete();
    }

    /**
     * Get all permissions grouped by their prefix (the word before the first '.').
     *
     * Example: 'users.view', 'users.create' → grouped under 'users'.
     *          'dashboard.view'              → grouped under 'dashboard'.
     *          'settings'                    → grouped under 'settings'.
     *
     * Returns a Collection keyed by group name, each value being a Collection
     * of Permission models belonging to that group.
     *
     * Structure:
     *   [
     *     'users'      => Collection<Permission>,
     *     'roles'      => Collection<Permission>,
     *     'dashboard'  => Collection<Permission>,
     *     …
     *   ]
     *
     * @return SupportCollection<string, Collection<Permission>>
     */
    public function getGroups(): SupportCollection
    {
        return Permission::orderBy('name')
            ->get()
            ->groupBy(function (Permission $permission) {
                $parts = explode('.', $permission->name, 2);

                return $parts[0];
            });
    }
}
