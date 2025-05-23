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
            'category' => ! empty($data['category']) ? implode(',', $data['category']) : null,
            'brand' => ! empty($data['brand']) ? implode(',', $data['brand']) : null,
            'price' => $data['price_range'] ?? null,
        ]);
        $appUrl = config('app.url');

        $routeSuffix = request()->is($appUrl.'/product-grids') ? 'product-grids' : 'product-lists';
        $routeName = 'front.'.$routeSuffix;
        $routeParameters = http_build_query($query);

        return redirect()->route($routeName, $routeParameters);
    }
}
