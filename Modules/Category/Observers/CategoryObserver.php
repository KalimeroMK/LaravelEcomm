<?php

namespace Modules\Category\Observers;

use Illuminate\Support\Str;
use Modules\Category\Models\Category;

class CategoryObserver
{
    /**
     * Handle the category "created" event.
     *
     * @param  Category  $category
     */
    public function creating(Category $category)
    {
        $slug = Str::slug($category->title);
        if (Category::whereSlug($slug)->count() > 0) {
            $category->slug = $slug;
        }
        $category->slug = $category->incrementSlug($slug);
    }
    
    /**
     * Handle the category "updated" event.
     *
     * @param  Category  $category
     */
    public function updated(Category $category)
    {
        //
    }
    
    /**
     * Handle the category "deleted" event.
     *
     * @param  Category  $category
     */
    public function deleted(Category $category)
    {
        //
    }
    
    /**
     * Handle the category "restored" event.
     *
     * @param  Category  $category
     */
    public function restored(Category $category)
    {
        //
    }
    
    /**
     * Handle the category "force deleted" event.
     *
     * @param  Category  $category
     */
    public function forceDeleted(Category $category)
    {
        //
    }
}
