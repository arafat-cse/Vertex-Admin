<?php

namespace App\Repositories\Interfaces;

interface ActivityLogRepositoryInterface
{
    /**
     * Get all activity log entries with optional filters and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(array $filters, int $perPage);

    /**
     * Clear (delete) all activity log entries.
     *
     * @return bool
     */
    public function clearAll();
}
