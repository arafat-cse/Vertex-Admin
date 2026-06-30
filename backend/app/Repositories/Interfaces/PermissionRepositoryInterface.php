<?php

namespace App\Repositories\Interfaces;

interface PermissionRepositoryInterface
{
    /**
     * Get all permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll();

    /**
     * Find a permission by its ID.
     *
     * @param int $id
     * @return \Spatie\Permission\Models\Permission|null
     */
    public function findById(int $id);

    /**
     * Create a new permission.
     *
     * @param array $data
     * @return \Spatie\Permission\Models\Permission
     */
    public function create(array $data);

    /**
     * Update an existing permission by ID.
     *
     * @param int $id
     * @param array $data
     * @return \Spatie\Permission\Models\Permission
     */
    public function update(int $id, array $data);

    /**
     * Delete a permission by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id);

    /**
     * Get all permissions grouped by their prefix/group.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getGroups();
}
