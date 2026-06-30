<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    /**
     * Get all users with optional filters and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(array $filters, int $perPage);

    /**
     * Find a user by their ID.
     *
     * @param int $id
     * @return \App\Models\User|null
     */
    public function findById(int $id);

    /**
     * Create a new user.
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function create(array $data);

    /**
     * Update an existing user by ID.
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\User
     */
    public function update(int $id, array $data);

    /**
     * Soft-delete a user by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id);

    /**
     * Restore a soft-deleted user by ID.
     *
     * @param int $id
     * @return \App\Models\User
     */
    public function restore(int $id);

    /**
     * Get all soft-deleted (trashed) users with pagination.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getTrashed(int $perPage);

    /**
     * Activate a user account by ID.
     *
     * @param int $id
     * @return \App\Models\User
     */
    public function activate(int $id);

    /**
     * Deactivate a user account by ID.
     *
     * @param int $id
     * @return \App\Models\User
     */
    public function deactivate(int $id);

    /**
     * Assign a role to a user.
     *
     * @param int $id
     * @param int $roleId
     * @return \App\Models\User
     */
    public function assignRole(int $id, int $roleId);

    /**
     * Find a user by their email address.
     *
     * @param string $email
     * @return \App\Models\User|null
     */
    public function findByEmail(string $email);
}
