<?php

namespace App\Services;

use App\Http\Resources\NotificationResource;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationService
{
    public function __construct(
        private readonly NotificationRepositoryInterface $notificationRepository,
    ) {}

    /**
     * Return paginated notifications belonging to the given user.
     *
     * @param  int|string  $userId
     * @param  int         $perPage
     * @return AnonymousResourceCollection
     */
    public function getForUser(int|string $userId, int $perPage = 15): AnonymousResourceCollection
    {
        /** @var LengthAwarePaginator $paginator */
        $paginator = $this->notificationRepository->getForUser($userId, $perPage);

        return NotificationResource::collection($paginator);
    }

    /**
     * Return the count of unread notifications for the given user.
     *
     * @param  int|string  $userId
     * @return int
     */
    public function getUnreadCount(int|string $userId): int
    {
        return (int) $this->notificationRepository->getUnreadCount($userId);
    }

    /**
     * Mark a single notification as read, enforcing ownership by $userId.
     *
     * @param  string      $id      Notification primary key
     * @param  int|string  $userId
     * @return NotificationResource
     *
     * @throws ModelNotFoundException  When the notification does not exist or does not belong to the user
     */
    public function markAsRead(string $id, int|string $userId): NotificationResource
    {
        $notification = $this->notificationRepository->markAsRead($id, $userId);

        if ($notification === null || $notification === false) {
            throw new ModelNotFoundException(
                "Notification with ID {$id} not found for the current user."
            );
        }

        // If the repository returns a bool (already read), fetch the record.
        if (is_bool($notification)) {
            $notification = $this->notificationRepository->markAsRead($id, $userId);
        }

        return new NotificationResource($notification);
    }

    /**
     * Mark every notification as read for the given user.
     *
     * @param  int|string  $userId
     * @return bool
     */
    public function markAllAsRead(int|string $userId): bool
    {
        $this->notificationRepository->markAllAsRead($userId);

        return true;
    }

    /**
     * Delete a single notification, enforcing ownership by $userId.
     *
     * @param  string      $id
     * @param  int|string  $userId
     * @return bool
     *
     * @throws ModelNotFoundException  When the notification does not exist or does not belong to the user
     */
    public function delete(string $id, int|string $userId): bool
    {
        $deleted = $this->notificationRepository->delete($id, $userId);

        if (!$deleted) {
            throw new ModelNotFoundException(
                "Notification with ID {$id} not found for the current user."
            );
        }

        return true;
    }
}
