<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly ActivityLogService $activityLogService
    ) {}

    /**
     * List all activity logs (paginated, filterable).
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'search',
                'causer_id',
                'causer_type',
                'subject_type',
                'log_name',
                'date_from',
                'date_to',
                'per_page',
                'page',
            ]);

            $logs = $this->activityLogService->getPaginated($filters);

            return $this->successResponse([
                'data' => $logs->items(),
                'meta' => [
                    'current_page' => $logs->currentPage(),
                    'last_page'    => $logs->lastPage(),
                    'per_page'     => $logs->perPage(),
                    'total'        => $logs->total(),
                ],
                'links' => [
                    'first' => $logs->url(1),
                    'last'  => $logs->url($logs->lastPage()),
                    'prev'  => $logs->previousPageUrl(),
                    'next'  => $logs->nextPageUrl(),
                ],
            ], 'Activity logs retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve activity logs.', 500, $e->getMessage());
        }
    }

    /**
     * Clear all activity logs.
     */
    public function clear(): JsonResponse
    {
        try {
            $this->activityLogService->clearAll();

            return $this->noContentResponse('Activity logs cleared successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to clear activity logs.', 500, $e->getMessage());
        }
    }
}
