<?php

declare(strict_types=1);

namespace Modules\Core\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait ApiFilterTrait
{
    /**
     * Apply filters to query builder
     */
    protected function applyFilters(Builder $query, Request $request, array $allowedFilters = []): Builder
    {
        $filters = $request->only($allowedFilters);

        foreach ($filters as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $this->applyFilter($query, $key, $value);
        }

        return $query;
    }

    /**
     * Apply individual filter
     */
    protected function applyFilter(Builder $query, string $key, mixed $value): void
    {
        // Handle different filter types
        if (is_array($value)) {
            $this->applyArrayFilter($query, $key, $value);
        } elseif (str_contains($value, ',')) {
            $this->applyCommaSeparatedFilter($query, $key, $value);
        } elseif (str_contains($value, ':')) {
            $this->applyRangeFilter($query, $key, $value);
        } else {
            $this->applySimpleFilter($query, $key, $value);
        }
    }

    /**
     * Apply array filter (for multiple values)
     */
    protected function applyArrayFilter(Builder $query, string $key, array $value): void
    {
        if (empty($value)) {
            return;
        }

        $query->whereIn($key, $value);
    }

    /**
     * Apply comma-separated filter
     */
    protected function applyCommaSeparatedFilter(Builder $query, string $key, string $value): void
    {
        $values = array_filter(explode(',', $value));
        if (!empty($values)) {
            $query->whereIn($key, $values);
        }
    }

    /**
     * Apply range filter (e.g., "min:max")
     */
    protected function applyRangeFilter(Builder $query, string $key, string $value): void
    {
        [$min, $max] = explode(':', $value, 2);

        if ($min !== '' && $min !== null) {
            $query->where($key, '>=', $min);
        }

        if ($max !== '' && $max !== null) {
            $query->where($key, '<=', $max);
        }
    }

    /**
     * Apply simple filter
     */
    protected function applySimpleFilter(Builder $query, string $key, mixed $value): void
    {
        // Handle boolean values
        if (in_array(strtolower($value), ['true', 'false', '1', '0'])) {
            $query->where($key, (bool) $value);
            return;
        }

        // Handle date values
        if (str_contains($key, 'date') || str_contains($key, 'created_at') || str_contains($key, 'updated_at')) {
            $query->whereDate($key, $value);
            return;
        }

        // Handle text search
        if (str_contains($key, 'name') || str_contains($key, 'title') || str_contains($key, 'description')) {
            $query->where($key, 'like', '%' . $value . '%');
            return;
        }

        // Default exact match
        $query->where($key, $value);
    }

    /**
     * Apply search to query
     */
    protected function applySearch(Builder $query, Request $request, array $searchableFields = []): Builder
    {
        $search = $request->get('search');

        if (!$search || empty($searchableFields)) {
            return $query;
        }

        $query->where(function (Builder $q) use ($search, $searchableFields) {
            foreach ($searchableFields as $field) {
                $q->orWhere($field, 'like', '%' . $search . '%');
            }
        });

        return $query;
    }

    /**
     * Apply sorting to query
     */
    protected function applySorting(Builder $query, Request $request, array $allowedSorts = [], string $defaultSort = 'id'): Builder
    {
        $sortBy = $request->get('sort_by', $defaultSort);
        $sortOrder = $request->get('sort_order', 'asc');

        // Validate sort field
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = $defaultSort;
        }

        // Validate sort order
        if (!in_array(strtolower($sortOrder), ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        return $query->orderBy($sortBy, $sortOrder);
    }

    /**
     * Apply pagination to query
     */
    protected function applyPagination(Builder $query, Request $request, int $defaultPerPage = 15, int $maxPerPage = 100): Builder
    {
        $perPage = (int) $request->get('per_page', $defaultPerPage);
        $page = (int) $request->get('page', 1);

        // Limit per page
        $perPage = min($perPage, $maxPerPage);
        $perPage = max($perPage, 1);

        // Limit page
        $page = max($page, 1);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get filter parameters from request
     */
    protected function getFilterParams(Request $request): array
    {
        return [
            'search' => $request->get('search'),
            'sort_by' => $request->get('sort_by'),
            'sort_order' => $request->get('sort_order'),
            'per_page' => (int) $request->get('per_page', 15),
            'page' => (int) $request->get('page', 1),
        ];
    }

    /**
     * Validate filter parameters
     */
    protected function validateFilterParams(array $params, array $allowedSorts = []): array
    {
        $validated = $params;

        // Validate per_page
        $validated['per_page'] = max(1, min($validated['per_page'], 100));

        // Validate page
        $validated['page'] = max(1, $validated['page']);

        // Validate sort_by
        if (!empty($allowedSorts) && !in_array($validated['sort_by'], $allowedSorts)) {
            $validated['sort_by'] = 'id';
        }

        // Validate sort_order
        if (!in_array(strtolower($validated['sort_order']), ['asc', 'desc'])) {
            $validated['sort_order'] = 'asc';
        }

        return $validated;
    }
}
