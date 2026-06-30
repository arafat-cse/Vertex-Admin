<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    /**
     * Scope a query to search across all columns listed in the model's $searchable array.
     *
     * Usage on the model:
     *
     *   protected array $searchable = ['name', 'email', 'phone'];
     *
     * Usage in a query:
     *
     *   User::search($request->input('q'))->paginate(15);
     *
     * @param  Builder      $query  The Eloquent query builder instance.
     * @param  string|null  $term   The search term supplied by the caller.
     * @return Builder
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if ($term === null || trim($term) === '') {
            return $query;
        }

        $columns = property_exists($this, 'searchable') ? $this->searchable : [];

        if (empty($columns)) {
            return $query;
        }

        $sanitized = trim($term);

        return $query->where(function (Builder $q) use ($sanitized, $columns) {
            foreach ($columns as $column) {
                $q->orWhere($column, 'LIKE', '%' . $sanitized . '%');
            }
        });
    }
}
