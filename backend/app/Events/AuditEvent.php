<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuditEvent implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\User|null  $user           The authenticated user who triggered the action.
     * @param  string                 $event          Audit event name: created, updated, deleted, restored.
     * @param  string                 $auditableType  Fully-qualified class name of the audited model.
     * @param  int|string             $auditableId    Primary key of the audited model.
     * @param  array|null             $oldValues      Attribute values before the change.
     * @param  array|null             $newValues      Attribute values after the change.
     * @param  string|null            $ipAddress      IP address from the originating HTTP request.
     * @param  string|null            $userAgent      User-Agent header from the originating HTTP request.
     */
    public function __construct(
        public readonly ?User $user,
        public readonly string $event,
        public readonly string $auditableType,
        public readonly int|string $auditableId,
        public readonly ?array $oldValues,
        public readonly ?array $newValues,
        public readonly ?string $ipAddress,
        public readonly ?string $userAgent,
    ) {}
}
