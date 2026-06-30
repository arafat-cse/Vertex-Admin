<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    /**
     * Disable the updated_at column — audit logs are immutable.
     */
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * The user that triggered this audit entry (nullable — system actions have no user).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Filter audit logs by user_id, event, auditable_type, and/or date range.
     *
     * Accepted keys in $filters:
     *   user_id        int|string
     *   event          string
     *   auditable_type string  (short class name or FQCN)
     *   from           string  parseable by Carbon (inclusive)
     *   to             string  parseable by Carbon (inclusive, end of day)
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (!empty($filters['auditable_type'])) {
            $type = $filters['auditable_type'];

            // Support short class names like "User" in addition to FQCNs.
            if (!str_contains($type, '\\')) {
                $type = 'App\\Models\\' . $type;
            }

            $query->where('auditable_type', $type);
        }

        if (!empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }

        return $query;
    }
}
