<?php

declare(strict_types=1);

namespace Modules\Category\Repository;

use Illuminate\Support\Collection;
use Modules\Category\Models\Category;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Repositories\EloquentRepository;

class CategoryRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Category::class);
    }

    /**
     * Find a category by slug with children eager-loaded.
     */
    public function findBySlug(string $slug): ?Category
    {
        return Category::whereSlug($slug)
            ->with(['children' => fn ($q) => $q->where('status', 1)->withCount('products')])
            ->withCount('products')
            ->first();
    }

    /**
     * Get IDs for a list of category slugs (used for product filtering).
     *
     * @param  array<string>  $slugs
     * @return array<int>
     */
    public function getIdsBySlugs(array $slugs): array
    {
        if ($slugs === [] || $slugs === ['']) {
            return [];
        }

        return Category::whereIn('slug', $slugs)
            ->where('status', 'active')
            ->pluck('id')
            ->toArray();
    }

    /**
     * Get active root categories with their sub-categories.
     */
    public function getActive(): Collection
    {
        return Category::where('status', 'active')
            ->whereNull('parent_id')
            ->with('childrenCategories')
            ->orderBy('title')
            ->get();
    }
}
