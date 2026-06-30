<?php

namespace App\Services;

use App\Http\Resources\ActivityLogResource;
use App\Repositories\Interfaces\ActivityLogRepositoryInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityLogService
{
    public function __construct(
        private readonly ActivityLogRepositoryInterface $activityLogRepository,
    ) {}

    /**
     * Return a paginated collection of activity log resources.
     *
     * @param  array<string, mixed>  $filters  Supported: user_id, method, from, to
     * @param  int                   $perPage
     * @return AnonymousResourceCollection
     */
    public function getAll(array $filters, int $perPage = 15): AnonymousResourceCollection
    {
        /** @var LengthAwarePaginator $paginator */
        $paginator = $this->activityLogRepository->getAll($filters, $perPage);

        return ActivityLogResource::collection($paginator);
    }

    /**
     * Delete all activity log entries from the database.
     *
     * @return bool
     */
    public function clearAll(): bool
    {
        return (bool) $this->activityLogRepository->clearAll();
    }
}
