<?php

namespace App\Listeners;

use App\Events\UserCreatedEvent;
use App\Jobs\SendWelcomeEmailJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmailListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 30;

    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the UserCreatedEvent by dispatching SendWelcomeEmailJob to the queue.
     *
     * Delegating to a dedicated job keeps the listener thin and allows the
     * welcome email to be retried independently with its own backoff policy.
     */
    public function handle(UserCreatedEvent $event): void
    {
        try {
            SendWelcomeEmailJob::dispatch($event->user);
        } catch (\Throwable $e) {
            Log::error('SendWelcomeEmailListener: failed to dispatch SendWelcomeEmailJob', [
                'user_id' => $event->user->getKey(),
                'email'   => $event->user->email,
                'error'   => $e->getMessage(),
            ]);

            $this->fail($e);
        }
    }

    /**
     * Handle a permanently failed listener job.
     */
    public function failed(UserCreatedEvent $event, \Throwable $exception): void
    {
        Log::critical('SendWelcomeEmailListener: job failed permanently', [
            'user_id' => $event->user->getKey(),
            'email'   => $event->user->email,
            'error'   => $exception->getMessage(),
        ]);
    }
}
