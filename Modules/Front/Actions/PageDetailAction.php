<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Page\Models\Page;

class PageDetailAction
{
    public function __invoke(string $slug): array
    {
        $cacheKey = 'page_'.$slug;

        return Cache::remember($cacheKey, 86400, function () use ($slug) {
            $page = Page::where('slug', $slug)->first();

            return [
                'page' => $page,
            ];
        });
    }
}
