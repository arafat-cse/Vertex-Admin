<?php

namespace App\Observers;

use App\Events\AuditEvent;
use App\Events\UserCreatedEvent;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class UserObserver
{
    /**
     * Sensitive fields that must never appear in audit log values.
     *
     * @var list<string>
     */
    private const HIDDEN_FIELDS = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    // -------------------------------------------------------------------------
    // Observer hooks
    // -------------------------------------------------------------------------

    /**
     * Handle the User "created" event.
     *
     * Dispatches UserCreatedEvent (triggers the welcome email listener) and
     * an AuditEvent that is picked up by LogAuditEventListener.
     */
    public function created(User $user): void
    {
        UserCreatedEvent::dispatch($user);

        AuditEvent::dispatch(
            $this->actingUser(),
            'created',
            User::class,
            $user->getKey(),
            null,
            $this->filterHidden($user->getAttributes()),
            Request::ip(),
            Request::userAgent(),
        );
    }

    /**
     * Handle the User "updated" event.
     *
     * Logs an audit entry only when there are actual dirty fields after
     * stripping sensitive attributes from both old and new value maps.
     */
    public function updated(User $user): void
    {
        $dirty = $user->getDirty();

        if (empty($dirty)) {
            return;
        }

        $oldValues = $this->filterHidden(
            array_intersect_key($user->getOriginal(), $dirty)
        );

        $newValues = $this->filterHidden(
            array_intersect_key($user->getAttributes(), $dirty)
        );

        // If all dirty fields were sensitive (e.g. password-only change),
        // there is nothing useful to store.
        if (empty($oldValues) && empty($newValues)) {
            return;
        }

        AuditEvent::dispatch(
            $this->actingUser(),
            'updated',
            User::class,
            $user->getKey(),
            $oldValues,
            $newValues,
            Request::ip(),
            Request::userAgent(),
        );
    }

    /**
     * Handle the User "deleted" (soft-delete) event.
     */
    public function deleted(User $user): void
    {
        AuditEvent::dispatch(
            $this->actingUser(),
            'deleted',
            User::class,
            $user->getKey(),
            $this->filterHidden($user->getAttributes()),
            null,
            Request::ip(),
            Request::userAgent(),
        );
    }

    /**
     * Handle the User "restored" (from soft-delete) event.
     */
    public function restored(User $user): void
    {
        AuditEvent::dispatch(
            $this->actingUser(),
            'restored',
            User::class,
            $user->getKey(),
            null,
            $this->filterHidden($user->getAttributes()),
            Request::ip(),
            Request::userAgent(),
        );
    }

    /**
     * Handle the User "force deleted" (permanent delete) event.
     */
    public function forceDeleted(User $user): void
    {
        AuditEvent::dispatch(
            $this->actingUser(),
            'force_deleted',
            User::class,
            $user->getKey(),
            $this->filterHidden($user->getAttributes()),
            null,
            Request::ip(),
            Request::userAgent(),
        );
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Return the currently authenticated User model, or null for system/CLI actions.
     */
    private function actingUser(): ?User
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        return $authUser instanceof User ? $authUser : null;
    }

    /**
     * Strip sensitive fields from an attribute map before it is stored in the
     * audit log.
     *
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function filterHidden(array $values): array
    {
        return array_diff_key($values, array_flip(self::HIDDEN_FIELDS));
    }
}
