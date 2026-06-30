<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Permission\Models\Role;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Get all users with optional filters and pagination.
     *
     * Accepted filter keys:
     *   search   string  — partial match on name or email
     *   status   string  — 'active' | 'inactive' | 'pending'
     *   role_id  int     — filter by Spatie role id
     *   sort     string  — column to order by (default: created_at)
     *   order    string  — 'asc' | 'desc'          (default: desc)
     *
     * @param  array  $filters
     * @param  int    $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query()->with(['roles', 'permissions']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['role_id'])) {
            $roleId = $filters['role_id'];
            $query->whereHas('roles', function ($q) use ($roleId) {
                $q->where('id', $roleId);
            });
        }

        $sortColumn = in_array($filters['sort'] ?? '', ['name', 'email', 'status', 'created_at', 'last_login_at'], true)
            ? $filters['sort']
            : 'created_at';

        $sortOrder = strtolower($filters['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortColumn, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Find a user by their ID (throws ModelNotFoundException if not found).
     *
     * @param  int  $id
     * @return User
     *
     * @throws ModelNotFoundException
     */
    public function findById(int $id): User
    {
        return User::with(['roles', 'permissions'])->findOrFail($id);
    }

    /**
     * Create a new user and optionally assign a role.
     *
     * @param  array  $data
     * @return User
     */
    public function create(array $data): User
    {
        $roleId = $data['role_id'] ?? null;
        unset($data['role_id']);

        $user = User::create($data);

        if ($roleId) {
            $role = Role::findById((int) $roleId);
            $user->syncRoles([$role]);
        }

        return $user->load(['roles', 'permissions']);
    }

    /**
     * Update an existing user and reassign their role if role_id changed.
     *
     * @param  int    $id
     * @param  array  $data
     * @return User
     *
     * @throws ModelNotFoundException
     */
    public function update(int $id, array $data): User
    {
        $user   = User::findOrFail($id);
        $roleId = $data['role_id'] ?? null;
        unset($data['role_id']);

        $user->update($data);

        if ($roleId !== null) {
            $role = Role::findById((int) $roleId);
            $user->syncRoles([$role]);
        }

        return $user->load(['roles', 'permissions']);
    }

    /**
     * Soft-delete a user by ID.
     *
     * @param  int  $id
     * @return bool
     *
     * @throws ModelNotFoundException
     */
    public function delete(int $id): bool
    {
        $user = User::findOrFail($id);

        return (bool) $user->delete();
    }

    /**
     * Restore a soft-deleted user by ID.
     *
     * @param  int  $id
     * @return User
     *
     * @throws ModelNotFoundException
     */
    public function restore(int $id): User
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return $user->load(['roles', 'permissions']);
    }

    /**
     * Get all soft-deleted (trashed) users with pagination.
     *
     * @param  int  $perPage
     * @return LengthAwarePaginator
     */
    public function getTrashed(int $perPage = 15): LengthAwarePaginator
    {
        return User::onlyTrashed()
            ->with(['roles', 'permissions'])
            ->orderBy('deleted_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Activate a user account.
     *
     * @param  int  $id
     * @return User
     *
     * @throws ModelNotFoundException
     */
    public function activate(int $id): User
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);

        return $user;
    }

    /**
     * Deactivate a user account.
     *
     * @param  int  $id
     * @return User
     *
     * @throws ModelNotFoundException
     */
    public function deactivate(int $id): User
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'inactive']);

        return $user;
    }

    /**
     * Sync a single role to a user (replaces any previously assigned roles).
     *
     * @param  int  $id
     * @param  int  $roleId
     * @return User
     *
     * @throws ModelNotFoundException
     */
    public function assignRole(int $id, int $roleId): User
    {
        $user = User::findOrFail($id);
        $role = Role::findById($roleId);
        $user->syncRoles([$role]);

        return $user->load(['roles', 'permissions']);
    }

    /**
     * Find a user by their email address.
     *
     * @param  string  $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
