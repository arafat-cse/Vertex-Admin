<?php

namespace App\Repositories;

use App\Models\AuditLog;
use App\Repositories\Interfaces\AuditLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuditLogRepository implements AuditLogRepositoryInterface
{
    /**
     * Get all audit log entries with optional filters and pagination.
     *
     * Accepted filter keys (all optional):
     *   user_id         int|string  — filter by the user who triggered the event
     *   event           string      — exact event name (e.g. 'created', 'updated', 'deleted')
     *   auditable_type  string      — short class name (e.g. 'User') or FQCN
     *   from_date       string      — inclusive start date (parseable by Carbon / whereDate)
     *   to_date         string      — inclusive end date
     *
     * Results are ordered newest-first.
     *
     * @param  array  $filters
     * @param  int    $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = AuditLog::query()->with(['user']);

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (!empty($filters['auditable_type'])) {
            $type = $filters['auditable_type'];

            if (!str_contains($type, '\\')) {
                $type = 'App\\Models\\' . $type;
            }

            $query->where('auditable_type', $type);
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
     * Find an audit log entry by its ID.
     *
     * @param  int  $id
     * @return AuditLog
     *
     * @throws ModelNotFoundException
     */
    public function findById(int $id): AuditLog
    {
        return AuditLog::with(['user'])->findOrFail($id);
    }

    /**
     * Delete all audit log entries permanently (truncate).
     *
     * @return bool
     */
    public function clearAll(): bool
    {
        AuditLog::truncate();

        return true;
    }
}
