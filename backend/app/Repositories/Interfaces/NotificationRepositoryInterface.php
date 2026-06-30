<?php

namespace App\Repositories\Interfaces;

interface NotificationRepositoryInterface
{
    /**
     * Get all notifications for a specific user with pagination.
     *
     * @param int|string $userId
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getForUser(int|string $userId, int $perPage);

    /**
     * Get the count of unread notifications for a specific user.
     *
     * @param int|string $userId
     * @return int
     */
    public function getUnreadCount(int|string $userId);

    /**
     * Mark a specific notification as read for a user.
     *
     * @param string $id  Notification UUID
     * @param int|string $userId
     * @return bool
     */
    public function markAsRead(string $id, int|string $userId);

    /**
     * Mark all notifications as read for a specific user.
     *
     * @param int|string $userId
     * @return bool
     */
    public function markAllAsRead(int|string $userId);

    /**
     * Delete a specific notification for a user.
     *
     * @param string $id  Notification UUID
     * @param int|string $userId
     * @return bool
     */
    public function delete(string $id, int|string $userId);
}
