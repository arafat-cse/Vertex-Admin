<?php

namespace App\Services;

use App\Events\UserCreatedEvent;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Create a new UserService instance.
     */
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    /**
     * Return a paginated list of users, optionally filtered.
     *
     * @param  array  $filters
     * @param  int    $perPage
     * @return LengthAwarePaginator  Items are UserResource instances.
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $paginator = $this->userRepository->getAll($filters, $perPage);

        // Wrap each item in a resource so controllers receive resource-shaped data.
        $paginator->getCollection()->transform(fn (User $user) => new UserResource($user->load('roles')));

        return $paginator;
    }

    /**
     * Find a single user by ID and return it as a resource.
     *
     * @param  int  $id
     * @return UserResource
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findById(int $id): UserResource
    {
        $user = $this->userRepository->findById($id);

        if (! $user) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("User #{$id} not found.");
        }

        return new UserResource($user->load('roles', 'permissions'));
    }

    /**
     * Create a new user, fire the creation event, and return the resource.
     *
     * @param  array  $data
     * @return UserResource
     */
    public function create(array $data): UserResource
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = $this->userRepository->create($data);

        // Assign role if provided.
        if (! empty($data['role_id'])) {
            $this->userRepository->assignRole($user->id, (int) $data['role_id']);
            $user->refresh();
        }

        event(new UserCreatedEvent($user));

        return new UserResource($user->load('roles', 'permissions'));
    }

    /**
     * Update an existing user and return the updated resource.
     *
     * @param  int    $id
     * @param  array  $data
     * @return UserResource
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, array $data): UserResource
    {
        if (isset($data['password']) && $data['password'] !== '') {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user = $this->userRepository->update($id, $data);

        return new UserResource($user->load('roles', 'permissions'));
    }

    /**
     * Soft-delete a user by ID.
     *
     * @param  int  $id
     * @return bool
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): bool
    {
        return $this->userRepository->delete($id);
    }

    /**
     * Restore a soft-deleted user and return the resource.
     *
     * @param  int  $id
     * @return UserResource
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function restore(int $id): UserResource
    {
        $user = $this->userRepository->restore($id);

        return new UserResource($user->load('roles', 'permissions'));
    }

    /**
     * Return a paginated list of soft-deleted users.
     *
     * @param  int  $perPage
     * @return LengthAwarePaginator  Items are UserResource instances.
     */
    public function getTrashed(int $perPage = 15): LengthAwarePaginator
    {
        $paginator = $this->userRepository->getTrashed($perPage);

        $paginator->getCollection()->transform(fn (User $user) => new UserResource($user->load('roles')));

        return $paginator;
    }

    /**
     * Activate a user account and return the updated resource.
     *
     * @param  int  $id
     * @return UserResource
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function activate(int $id): UserResource
    {
        $user = $this->userRepository->activate($id);

        return new UserResource($user->load('roles', 'permissions'));
    }

    /**
     * Deactivate a user account and return the updated resource.
     *
     * @param  int  $id
     * @return UserResource
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deactivate(int $id): UserResource
    {
        $user = $this->userRepository->deactivate($id);

        return new UserResource($user->load('roles', 'permissions'));
    }

    /**
     * Assign a role to a user and return the updated resource.
     *
     * @param  int  $id
     * @param  int  $roleId
     * @return UserResource
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function assignRole(int $id, int $roleId): UserResource
    {
        $user = $this->userRepository->assignRole($id, $roleId);

        return new UserResource($user->load('roles', 'permissions'));
    }
}
