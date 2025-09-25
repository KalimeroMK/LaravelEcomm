<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Http\RedirectResponse;

class ProductFilterAction
{
    public function __invoke(array $data): RedirectResponse|string
    {
        $query = array_filter([
            'show' => $data['show'] ?? null,
            'sortBy' => $data['sortBy'] ?? null,
            'category' => empty($data['category']) ? null : implode(',', $data['category']),
            'brand' => empty($data['brand']) ? null : implode(',', $data['brand']),
            'price' => $data['price_range'] ?? null,
        ]);
        $appUrl = config('app.url');

        $routeSuffix = request()->is($appUrl.'/product-grids') ? 'product-grids' : 'product-lists';
        $routeName = 'front.'.$routeSuffix;
        $routeParameters = http_build_query($query);

        return redirect()->route($routeName, $routeParameters);
    }
}
