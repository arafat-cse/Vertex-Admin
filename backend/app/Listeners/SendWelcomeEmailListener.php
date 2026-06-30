<?php

namespace App\Listeners;

use App\Events\UserCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreatedEvent $event): void
    {
        $user = $event->user;

        try {
            Mail::send(
                'emails.welcome',
                ['user' => $user],
                function ($message) use ($user) {
                    $message->to($user->email, $user->name)
                        ->subject('Welcome to ' . config('app.name', 'Vertex-Admin'));
                }
            );
        } catch (\Throwable $e) {
            Log::error('Failed to send welcome email', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'error'   => $e->getMessage(),
            ]);

            $this->fail($e);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(UserCreatedEvent $event, \Throwable $exception): void
    {
        Log::error('SendWelcomeEmailListener failed permanently', [
            'user_id' => $event->user->id,
            'email'   => $event->user->email,
            'error'   => $exception->getMessage(),
        ]);
    }
}
