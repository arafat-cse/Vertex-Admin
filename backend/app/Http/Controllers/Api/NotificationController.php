<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    /**
     * List all notifications for the authenticated user (paginated).
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['read', 'per_page', 'page']);

            $notifications = $this->notificationService->getPaginated(
                $request->user(),
                $filters
            );

            return $this->successResponse([
                'data' => $notifications->items(),
                'meta' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page'    => $notifications->lastPage(),
                    'per_page'     => $notifications->perPage(),
                    'total'        => $notifications->total(),
                ],
                'links' => [
                    'first' => $notifications->url(1),
                    'last'  => $notifications->url($notifications->lastPage()),
                    'prev'  => $notifications->previousPageUrl(),
                    'next'  => $notifications->nextPageUrl(),
                ],
            ], 'Notifications retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve notifications.', 500, $e->getMessage());
        }
    }

    /**
     * Get the count of unread notifications for the authenticated user.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        try {
            $count = $this->notificationService->getUnreadCount($request->user());

            return $this->successResponse(['unread_count' => $count], 'Unread notification count retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve unread count.', 500, $e->getMessage());
        }
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        try {
            $notification = $this->notificationService->markAsRead($request->user(), $id);

            if (!$notification) {
                return $this->notFoundResponse('Notification not found.');
            }

            return $this->successResponse($notification, 'Notification marked as read.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to mark notification as read.', 500, $e->getMessage());
        }
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        try {
            $this->notificationService->markAllAsRead($request->user());

            return $this->successResponse(null, 'All notifications marked as read.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to mark all notifications as read.', 500, $e->getMessage());
        }
    }

    /**
     * Delete a specific notification.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $deleted = $this->notificationService->delete($request->user(), $id);

            if (!$deleted) {
                return $this->notFoundResponse('Notification not found.');
            }

            return $this->noContentResponse('Notification deleted successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to delete notification.', 500, $e->getMessage());
        }
    }
}
