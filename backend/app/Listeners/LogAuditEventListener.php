<?php

namespace App\Listeners;

use App\Events\AuditEvent;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogAuditEventListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 10;

    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the AuditEvent by persisting a new AuditLog record.
     *
     * The AuditLog Eloquent model is used (rather than a raw DB insert) so that
     * the array casting on old_values / new_values is applied consistently and
     * the fillable guard is respected.
     */
    public function handle(AuditEvent $event): void
    {
        try {
            AuditLog::create([
                'user_id'        => $event->user?->getKey(),
                'event'          => $event->event,
                'auditable_type' => $event->auditableType,
                'auditable_id'   => $event->auditableId,
                'old_values'     => $event->oldValues,
                'new_values'     => $event->newValues,
                'ip_address'     => $event->ipAddress,
                'user_agent'     => $event->userAgent,
            ]);
        } catch (\Throwable $e) {
            Log::error('LogAuditEventListener: failed to persist audit log', [
                'event'          => $event->event,
                'auditable_type' => $event->auditableType,
                'auditable_id'   => $event->auditableId,
                'user_id'        => $event->user?->getKey(),
                'error'          => $e->getMessage(),
            ]);

            $this->fail($e);
        }
    }

    /**
     * Handle a permanently failed listener job.
     */
    public function failed(AuditEvent $event, \Throwable $exception): void
    {
        Log::critical('LogAuditEventListener: job failed permanently', [
            'event'          => $event->event,
            'auditable_type' => $event->auditableType,
            'auditable_id'   => $event->auditableId,
            'user_id'        => $event->user?->getKey(),
            'error'          => $exception->getMessage(),
        ]);
    }
}
