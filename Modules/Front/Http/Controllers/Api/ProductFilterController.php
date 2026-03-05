<?php

declare(strict_types=1);

namespace Modules\Front\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Services\LayeredNavigationService;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;
use Modules\Product\Repository\ProductRepository;

final class ProductFilterController extends Controller
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly LayeredNavigationService $layeredNavigationService,
    ) {}

    /**
     * AJAX endpoint for product filtering
     */
    public function filter(Request $request): JsonResponse
    {
        $category = $this->getCategory($request);

        // Build base query
        $query = Product::query()
            ->active()
            ->with(['attributeValues.attribute', 'categories']);

        // Apply category filter
        if ($category) {
            $query->whereHas('categories', fn ($q) => $q->where('categories.id', $category->id));
        }

        // Apply attribute filters
        $attributeFilters = $this->parseAttributeFilters($request);
        foreach ($attributeFilters as $attributeCode => $values) {
            $this->applyAttributeFilter($query, $attributeCode, $values);
        }

        // Apply price filter
        if ($request->has('price_min')) {
            $query->where('price', '>=', $request->input('price_min'));
        }
        if ($request->has('price_max')) {
            $query->where('price', '<=', $request->input('price_max'));
        }

        // Apply sorting
        $this->applySorting($query, $request->input('sort', 'newest'));

        // Paginate
        $products = $query->paginate($request->input('per_page', 12));

        // Get updated filter counts
        $filterCounts = $this->getFilterCounts($category, $attributeFilters);

        return response()->json([
            'success' => true,
            'products' => $this->renderProductGrid($products),
            'pagination' => $products->links()->toHtml(),
            'total' => $products->total(),
            'filter_counts' => $filterCounts,
        ]);
    }

    /**
     * Get available filters for a category
     */
    public function getFilters(Request $request): JsonResponse
    {
        $category = $this->getCategory($request);

        if (! $category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        $filters = $this->layeredNavigationService->getAvailableFilters($category);

        return response()->json([
            'success' => true,
            'filters' => $filters,
        ]);
    }

    /**
     * Get price range for category
     */
    public function getPriceRange(Request $request): JsonResponse
    {
        $category = $this->getCategory($request);

        $query = Product::query()->active();

        if ($category) {
            $query->whereHas('categories', fn ($q) => $q->where('categories.id', $category->id));
        }

        $minPrice = $query->min('price') ?? 0;
        $maxPrice = $query->max('price') ?? 1000;

        return response()->json([
            'success' => true,
            'min' => $minPrice,
            'max' => $maxPrice,
        ]);
    }

    /**
     * Parse attribute filters from request
     */
    private function parseAttributeFilters(Request $request): array
    {
        $filters = [];

        foreach ($request->all() as $key => $value) {
            // Skip non-attribute params
            if (in_array($key, ['page', 'sort', 'category', 'price_min', 'price_max', 'per_page'])) {
                continue;
            }

            $filters[$key] = is_string($value) ? explode(',', $value) : $value;
        }

        return $filters;
    }

    /**
     * Apply attribute filter to query
     */
    private function applyAttributeFilter($query, string $attributeCode, array $values): void
    {
        $query->whereHas('attributeValues', function ($q) use ($attributeCode, $values) {
            $q->whereHas('attribute', fn ($aq) => $aq->where('code', $attributeCode))
                ->whereIn('text_value', $values);
        });
    }

    /**
     * Apply sorting to query
     */
    private function applySorting($query, string $sort): void
    {
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'popularity' => $query->orderBy('views_count', 'desc'),
            default => $query->orderBy('created_at', 'desc'), // newest
        };
    }

    /**
     * Get category from request
     */
    private function getCategory(Request $request): ?Category
    {
        if ($request->has('category')) {
            return Category::find($request->input('category'));
        }

        return null;
    }

    /**
     * Get updated filter counts based on current selection
     */
    private function getFilterCounts(?Category $category, array $activeFilters): array
    {
        $baseQuery = Product::query()->active();

        if ($category) {
            $baseQuery->whereHas('categories', fn ($q) => $q->where('categories.id', $category->id));
        }

        // Exclude the current attribute's filter when counting other attributes
        $counts = [];

        // Get all filterable attributes for category
        $attributes = $category
            ? $this->layeredNavigationService->getFilterableAttributes($category)
            : Attribute::where('is_filterable', true)->get();

        foreach ($attributes as $attribute) {
            $counts[$attribute->code] = [];

            foreach ($attribute->options as $option) {
                $countQuery = clone $baseQuery;

                // Apply all other active filters
                foreach ($activeFilters as $code => $values) {
                    if ($code !== $attribute->code) {
                        $this->applyAttributeFilter($countQuery, $code, $values);
                    }
                }

                // Apply this specific option
                $countQuery->whereHas('attributeValues', function ($q) use ($attribute, $option) {
                    $q->where('attribute_id', $attribute->id)
                        ->where('text_value', $option->value);
                });

                $counts[$attribute->code][$option->value] = $countQuery->count();
            }
        }

        return $counts;
    }

    /**
     * Render product grid HTML
     */
    private function renderProductGrid($products): string
    {
        if ($products->isEmpty()) {
            return '<div class="col-12 text-center py-5">'.
                   '<h4>No products found</h4>'.
                   '<p>Try adjusting your filters</p>'.
                   '</div>';
        }

        return view('front::partials.product_grid_items', [
            'products' => $products,
        ])->render();
    }
}
