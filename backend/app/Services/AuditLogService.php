<?php

namespace App\Services;

use App\Http\Resources\AuditLogResource;
use App\Repositories\Interfaces\AuditLogRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditLogService
{
    public function __construct(
        private readonly AuditLogRepositoryInterface $auditLogRepository,
    ) {}

    /**
     * Return a paginated collection of audit log resources.
     *
     * @param  array<string, mixed>  $filters  Supported: user_id, event, auditable_type, from, to
     * @param  int                   $perPage
     * @return AnonymousResourceCollection
     */
    public function getAll(array $filters, int $perPage = 15): AnonymousResourceCollection
    {
        /** @var LengthAwarePaginator $paginator */
        $paginator = $this->auditLogRepository->getAll($filters, $perPage);

        return AuditLogResource::collection($paginator);
    }

    /**
     * Return a single audit log entry by its primary key.
     *
     * @param  int  $id
     * @return AuditLogResource
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findById(int $id): AuditLogResource
    {
        $log = $this->auditLogRepository->findById($id);

        if ($log === null) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                "Audit log entry with ID {$id} not found."
            );
        }

        return new AuditLogResource($log);
    }

    /**
     * Delete all audit log entries from the database.
     *
     * @return bool
     */
    public function clearAll(): bool
    {
        return (bool) $this->auditLogRepository->clearAll();
    }
}
