<?php

namespace App\Repositories\Interfaces;

interface RoleRepositoryInterface
{
    /**
     * Get all roles.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll();

    /**
     * Find a role by its ID.
     *
     * @param int $id
     * @return \Spatie\Permission\Models\Role|null
     */
    public function findById(int $id);

    /**
     * Create a new role.
     *
     * @param array $data
     * @return \Spatie\Permission\Models\Role
     */
    public function create(array $data);

    /**
     * Update an existing role by ID.
     *
     * @param int $id
     * @param array $data
     * @return \Spatie\Permission\Models\Role
     */
    public function update(int $id, array $data);

    /**
     * Delete a role by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id);

    /**
     * Assign permissions to a role.
     *
     * @param int $id
     * @param array $permissions
     * @return \Spatie\Permission\Models\Role
     */
    public function assignPermissions(int $id, array $permissions);

    /**
     * Get all permissions assigned to a role.
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPermissions(int $id);
}
