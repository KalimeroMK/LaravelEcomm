<?php

namespace Modules\Category\Models\Observers;

use Illuminate\Support\Str;
use Modules\Category\Models\Category;

class CategoryObserver
{
    /**
     * Handle the category "created" event.
     *
     * @param  Category  $category
     */
    public function creating(Category $category): void
    {
        $slug = Str::slug($category->title);
        if (Category::whereSlug($slug)->count() > 0) {
            $category->slug = $slug;
        }
        $category->slug = $category->incrementSlug($slug);
    }
}
