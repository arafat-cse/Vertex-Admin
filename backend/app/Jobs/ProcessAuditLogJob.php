<?php

namespace App\Jobs;

use App\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAuditLogJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 30;

    /**
     * Create a new job instance.
     *
     * @param array<string, mixed> $logData
     */
    public function __construct(public array $logData)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        AuditLog::create([
            'user_id'        => $this->logData['user_id']        ?? null,
            'event'          => $this->logData['event']          ?? 'unknown',
            'auditable_type' => $this->logData['auditable_type'] ?? null,
            'auditable_id'   => $this->logData['auditable_id']   ?? null,
            'old_values'     => $this->logData['old_values']     ?? null,
            'new_values'     => $this->logData['new_values']     ?? null,
            'ip_address'     => $this->logData['ip_address']     ?? null,
            'user_agent'     => $this->logData['user_agent']     ?? null,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessAuditLogJob failed', [
            'log_data' => $this->logData,
            'error'    => $exception->getMessage(),
        ]);
    }
}
