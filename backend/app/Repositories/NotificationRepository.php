<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NotificationRepository implements NotificationRepositoryInterface
{
    /**
     * Get all notifications for a specific user with pagination.
     *
     * Results are ordered newest-first.
     *
     * @param  int|string  $userId
     * @param  int         $perPage
     * @return LengthAwarePaginator
     */
    public function getForUser(int|string $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get the count of unread notifications for a specific user.
     *
     * @param  int|string  $userId
     * @return int
     */
    public function getUnreadCount(int|string $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark a specific notification as read.
     *
     * Verifies ownership by requiring the notification to belong to $userId
     * before updating. Returns false if the notification does not exist or
     * does not belong to the given user.
     *
     * @param  string      $id      Notification primary key
     * @param  int|string  $userId
     * @return bool
     */
    public function markAsRead(string $id, int|string $userId): bool
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($notification === null) {
            return false;
        }

        if ($notification->is_read) {
            return true;
        }

        return $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark all notifications as read for a specific user.
     *
     * Only updates notifications that are currently unread to avoid
     * unnecessary writes.
     *
     * @param  int|string  $userId
     * @return bool
     */
    public function markAllAsRead(int|string $userId): bool
    {
        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return true;
    }

    /**
     * Delete a specific notification for a user.
     *
     * Verifies ownership by requiring the notification to belong to $userId
     * before deleting. Returns false if the notification does not exist or
     * does not belong to the given user.
     *
     * @param  string      $id      Notification primary key
     * @param  int|string  $userId
     * @return bool
     */
    public function delete(string $id, int|string $userId): bool
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($notification === null) {
            return false;
        }

        return (bool) $notification->delete();
    }
}
