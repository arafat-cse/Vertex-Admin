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
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AuditEvent $event): void
    {
        try {
            DB::table('audit_logs')->insert([
                'user_id'        => $event->user?->id,
                'event'          => $event->event,
                'auditable_type' => $event->auditableType,
                'auditable_id'   => $event->auditableId,
                'old_values'     => $event->oldValues !== null ? json_encode($event->oldValues) : null,
                'new_values'     => $event->newValues !== null ? json_encode($event->newValues) : null,
                'ip_address'     => $event->ipAddress,
                'user_agent'     => $event->userAgent,
                'created_at'     => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to log audit event', [
                'event'          => $event->event,
                'auditable_type' => $event->auditableType,
                'auditable_id'   => $event->auditableId,
                'user_id'        => $event->user?->id,
                'error'          => $e->getMessage(),
            ]);

            $this->fail($e);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(AuditEvent $event, \Throwable $exception): void
    {
        Log::critical('LogAuditEventListener failed permanently', [
            'event'          => $event->event,
            'auditable_type' => $event->auditableType,
            'auditable_id'   => $event->auditableId,
            'user_id'        => $event->user?->id,
            'error'          => $exception->getMessage(),
        ]);
    }
}
