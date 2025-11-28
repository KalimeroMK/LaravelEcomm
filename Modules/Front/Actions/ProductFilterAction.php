<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

class ProductFilterAction
{
    public function execute(array $data, string $currentRoute = 'product-grids'): string
    {
        $query = array_filter([
            'show' => $data['show'] ?? null,
            'sortBy' => $data['sortBy'] ?? null,
            'category' => empty($data['category']) ? null : implode(',', $data['category']),
            'brand' => empty($data['brand']) ? null : implode(',', $data['brand']),
            'price' => $data['price_range'] ?? null,
        ]);

        $routeSuffix = ($currentRoute === 'product-grids') ? 'product-grids' : 'product-lists';
        $routeName = 'front.'.$routeSuffix;
        $routeParameters = http_build_query($query);

        return route($routeName).($routeParameters ? '?'.$routeParameters : '');
    }
}
