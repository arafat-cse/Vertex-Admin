<?php

namespace App\Providers;

use App\Events\AuditEvent;
use App\Events\UserCreatedEvent;
use App\Listeners\LogAuditEventListener;
use App\Listeners\SendWelcomeEmailListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UserCreatedEvent::class => [
            SendWelcomeEmailListener::class,
        ],
        AuditEvent::class => [
            LogAuditEventListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be auto-discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
