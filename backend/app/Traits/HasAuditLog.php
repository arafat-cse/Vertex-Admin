<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait HasAuditLog
{
    /**
     * Boot the trait and register model event observers.
     */
    public static function bootHasAuditLog(): void
    {
        static::created(function (Model $model) {
            $model->logAudit('created', null, $model->getAttributes());
        });

        static::updated(function (Model $model) {
            $model->logAudit('updated', $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function (Model $model) {
            $model->logAudit('deleted', $model->getAttributes(), null);
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function (Model $model) {
                $model->logAudit('restored', null, $model->getAttributes());
            });
        }
    }

    /**
     * Create an AuditLog record for the given event.
     *
     * @param  string      $event  One of: created, updated, deleted, restored
     * @param  array|null  $old    Old attribute values (before the change)
     * @param  array|null  $new    New attribute values (after the change)
     */
    public function logAudit(string $event, ?array $old = null, ?array $new = null): void
    {
        try {
            AuditLog::create([
                'user_id'        => auth()->id(),
                'event'          => $event,
                'auditable_type' => static::class,
                'auditable_id'   => $this->getKey(),
                'old_values'     => $old !== null ? $this->filterAuditableValues($old) : null,
                'new_values'     => $new !== null ? $this->filterAuditableValues($new) : null,
                'ip_address'     => request()?->ip(),
                'user_agent'     => request()?->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Do not let audit logging failures bubble up and break the main operation.
            logger()->error('HasAuditLog: failed to write audit log', [
                'event'  => $event,
                'model'  => static::class,
                'id'     => $this->getKey(),
                'error'  => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove sensitive fields from values before persisting them in the audit log.
     *
     * The trait respects the model's $hidden array plus any additional keys
     * declared in $auditHidden on the model.
     */
    protected function filterAuditableValues(array $values): array
    {
        $hidden = array_merge(
            $this->getHidden(),
            ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'],
            property_exists($this, 'auditHidden') ? $this->auditHidden : []
        );

        return array_diff_key($values, array_flip(array_unique($hidden)));
    }

    /**
     * Retrieve all audit log entries for this model instance.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function auditLogs(): \Illuminate\Database\Eloquent\Collection
    {
        return AuditLog::where('auditable_type', static::class)
            ->where('auditable_id', $this->getKey())
            ->latest()
            ->get();
    }
}
