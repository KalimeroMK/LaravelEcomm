<?php

declare(strict_types=1);

namespace Modules\Core\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiPaginationTrait
{
    /**
     * Paginate query results
     */
    protected function paginateQuery(Builder $query, Request $request, int $defaultPerPage = 15, int $maxPerPage = 100): LengthAwarePaginator
    {
        $perPage = $this->getPerPage($request, $defaultPerPage, $maxPerPage);
        $page = $this->getPage($request);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get per page parameter
     */
    protected function getPerPage(Request $request, int $defaultPerPage = 15, int $maxPerPage = 100): int
    {
        $perPage = (int) $request->get('per_page', $defaultPerPage);
        
        return max(1, min($perPage, $maxPerPage));
    }

    /**
     * Get page parameter
     */
    protected function getPage(Request $request): int
    {
        return max(1, (int) $request->get('page', 1));
    }

    /**
     * Get pagination metadata
     */
    protected function getPaginationMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'has_more_pages' => $paginator->hasMorePages(),
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ];
    }

    /**
     * Get pagination links
     */
    protected function getPaginationLinks(LengthAwarePaginator $paginator): array
    {
        $links = [];

        // Previous page
        if ($paginator->previousPageUrl()) {
            $links['prev'] = $paginator->previousPageUrl();
        }

        // Next page
        if ($paginator->nextPageUrl()) {
            $links['next'] = $paginator->nextPageUrl();
        }

        // First page
        if ($paginator->currentPage() > 1) {
            $links['first'] = $paginator->url(1);
        }

        // Last page
        if ($paginator->currentPage() < $paginator->lastPage()) {
            $links['last'] = $paginator->url($paginator->lastPage());
        }

        return $links;
    }

    /**
     * Get pagination summary
     */
    protected function getPaginationSummary(LengthAwarePaginator $paginator): string
    {
        $from = $paginator->firstItem();
        $to = $paginator->lastItem();
        $total = $paginator->total();

        if ($total === 0) {
            return 'No results found';
        }

        return "Showing {$from} to {$to} of {$total} results";
    }

    /**
     * Validate pagination parameters
     */
    protected function validatePaginationParams(Request $request, int $defaultPerPage = 15, int $maxPerPage = 100): array
    {
        $perPage = $this->getPerPage($request, $defaultPerPage, $maxPerPage);
        $page = $this->getPage($request);

        return [
            'per_page' => $perPage,
            'page' => $page,
        ];
    }

    /**
     * Get pagination options
     */
    protected function getPaginationOptions(): array
    {
        return [
            'per_page_options' => [10, 15, 25, 50, 100],
            'default_per_page' => 15,
            'max_per_page' => 100,
        ];
    }

    /**
     * Check if pagination is needed
     */
    protected function needsPagination(int $totalCount, int $perPage): bool
    {
        return $totalCount > $perPage;
    }

    /**
     * Get offset for manual pagination
     */
    protected function getOffset(int $page, int $perPage): int
    {
        return ($page - 1) * $perPage;
    }

    /**
     * Calculate total pages
     */
    protected function calculateTotalPages(int $totalCount, int $perPage): int
    {
        return (int) ceil($totalCount / $perPage);
    }

    /**
     * Get page range for pagination links
     */
    protected function getPageRange(LengthAwarePaginator $paginator, int $range = 5): array
    {
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        
        $start = max(1, $currentPage - $range);
        $end = min($lastPage, $currentPage + $range);
        
        return range($start, $end);
    }
}
