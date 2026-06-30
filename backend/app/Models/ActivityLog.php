<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    /**
     * Disable the updated_at column — activity logs are append-only.
     */
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'method',
        'url',
        'ip_address',
        'user_agent',
        'status_code',
        'payload',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload'    => 'array',
            'created_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * The user who made the request (nullable — unauthenticated requests have no user).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Filter activity logs by user_id, HTTP method, and/or date range.
     *
     * Accepted keys in $filters:
     *   user_id  int|string
     *   method   string  (GET, POST, PUT, PATCH, DELETE, …)
     *   from     string  parseable by Carbon (inclusive)
     *   to       string  parseable by Carbon (inclusive, end of day)
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['method'])) {
            $query->where('method', strtoupper($filters['method']));
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
