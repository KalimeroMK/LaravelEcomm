<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Page\Repository\PageRepository;

class PageDetailAction
{
    public function __construct(private readonly PageRepository $pageRepository) {}

    public function __invoke(string $slug): array
    {
        $page = Cache::remember("page_{$slug}", 86400, fn () => $this->pageRepository->findBySlug($slug));

        return ['page' => $page];
    }
}
