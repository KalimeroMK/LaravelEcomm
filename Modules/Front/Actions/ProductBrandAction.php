<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Database\Eloquent\Builder;
use Modules\Brand\Models\Brand;
use Modules\Product\Models\Product;

class ProductBrandAction
{
    public function __invoke(string $slug): array
    {
        $products = Product::whereHas('brand', function (Builder $query) use ($slug): void {
            $query->where('slug', $slug);
        })->with(['categories', 'brand', 'tags', 'attributeValues.attribute'])->paginate(9);
        $brands = Brand::where('status', 'active')
            ->orderBy('title')
            ->get();
        $recentProducts = Product::where('status', 'active')
            ->orderBy('id', 'DESC')
            ->with(['categories', 'brand', 'tags', 'attributeValues.attribute'])
            ->limit(3)
            ->get();

        return [
            'products' => $products,
            'brands' => $brands,
            'recentProducts' => $recentProducts,
        ];
    }
}
