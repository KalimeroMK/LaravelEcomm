<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Category\Repository\CategoryRepository;

class GetCategoriesAction
{
    public function __construct(private readonly CategoryRepository $categoryRepository) {}

    public function __invoke(): array
    {
        $categories = Cache::remember('front_categories_page', 3600, fn () => $this->categoryRepository->getActive());

        return ['categories' => $categories];
    }
}
