<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {}

    /**
     * List all audit logs (paginated, filterable).
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'search',
                'user_id',
                'event',
                'auditable_type',
                'date_from',
                'date_to',
                'per_page',
                'page',
            ]);

            $logs = $this->auditLogService->getPaginated($filters);

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
            ], 'Audit logs retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve audit logs.', 500, $e->getMessage());
        }
    }

    /**
     * Show a single audit log entry.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $log = $this->auditLogService->findById($id);

            if (!$log) {
                return $this->notFoundResponse('Audit log not found.');
            }

            return $this->successResponse($log, 'Audit log retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to retrieve audit log.', 500, $e->getMessage());
        }
    }

    /**
     * Clear all audit logs.
     */
    public function clear(): JsonResponse
    {
        try {
            $this->auditLogService->clearAll();

            return $this->noContentResponse('Audit logs cleared successfully.');
        } catch (\Throwable $e) {
            return $this->errorResponse('Failed to clear audit logs.', 500, $e->getMessage());
        }
    }
}
