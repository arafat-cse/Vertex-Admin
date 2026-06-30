<?php

namespace App\Repositories;

use App\Models\ActivityLog;
use App\Repositories\Interfaces\ActivityLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ActivityLogRepository implements ActivityLogRepositoryInterface
{
    /**
     * Get all activity log entries with optional filters and pagination.
     *
     * Accepted filter keys (all optional):
     *   user_id   int|string  — filter by the authenticated user
     *   method    string      — HTTP method (GET, POST, PUT, PATCH, DELETE, …)
     *   from_date string      — inclusive start date (parseable by Carbon / whereDate)
     *   to_date   string      — inclusive end date
     *
     * Results are ordered newest-first.
     *
     * @param  array  $filters
     * @param  int    $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = ActivityLog::query()->with(['user']);

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['method'])) {
            $query->where('method', strtoupper($filters['method']));
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Delete all activity log entries permanently (truncate).
     *
     * @return bool
     */
    public function clearAll(): bool
    {
        ActivityLog::truncate();

        return true;
    }
}
