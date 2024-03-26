<?php

namespace Modules\Front\Http\ViewComposers;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Modules\Category\Models\Category;

class MenuViewComposer
{
    public function compose(View $view): void
    {
        $categories = Cache::remember('categories_with_children', 24 * 60, function () {
            return Category::whereNull('parent_id')->with(['childrenCategories.childrenCategories'])->get();
        });
        $view->with('categories', $categories);
    }
}
