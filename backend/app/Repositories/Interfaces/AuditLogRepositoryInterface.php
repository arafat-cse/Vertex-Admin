<?php

namespace App\Repositories\Interfaces;

interface AuditLogRepositoryInterface
{
    /**
     * Get all audit logs with optional filters and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(array $filters, int $perPage);

    /**
     * Find an audit log entry by its ID.
     *
     * @param int $id
     * @return \OwenIt\Auditing\Models\Audit|null
     */
    public function findById(int $id);

    /**
     * Clear (delete) all audit log entries.
     *
     * @return bool
     */
    public function clearAll();
}
