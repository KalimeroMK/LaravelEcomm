<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;

class ProductListsAction
{
    public function __invoke(): array
    {
        $query = Product::query()
            ->with(['categories', 'brand', 'condition', 'tags', 'sizes'])
            ->where('status', 'active');

        // Filter by category
        if (! empty($_GET['category'])) {
            $catSlugs = explode(',', $_GET['category']);
            $catIds = Category::whereIn('slug', $catSlugs)->pluck('id')->toArray();
            $query->whereIn('cat_id', $catIds);
        }
        // Filter by brand
        if (! empty($_GET['brand'])) {
            $brandSlugs = explode(',', $_GET['brand']);
            $brandIds = Brand::whereIn('slug', $brandSlugs)->pluck('id')->toArray();
            $query->whereIn('brand_id', $brandIds);
        }
        // Sort by
        if (! empty($_GET['sortBy'])) {
            $sortColumn = $_GET['sortBy'];
            $sortOrder = ($sortColumn === 'title') ? 'asc' : 'desc';
            if ($sortColumn === 'price') {
                $sortOrder = 'asc';
            }
            $query->orderBy($sortColumn, $sortOrder);
        }
        // Filter by price range
        if (! empty($_GET['price'])) {
            $priceRange = explode('-', $_GET['price']);
            $minPrice = $priceRange[0] ?? 0;
            $maxPrice = $priceRange[1] ?? PHP_INT_MAX;
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        }

        $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();
        $products = $query->paginate(isset($_GET['show']) ? (int) $_GET['show'] : 6);
        $brands = Brand::where('status', 'active')->withCount('products')->get();

        return [
            'recent_products' => $recent_products,
            'products' => $products,
            'brands' => $brands,
        ];
    }
}
