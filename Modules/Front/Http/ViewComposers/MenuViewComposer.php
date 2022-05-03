<?php

    namespace Modules\Front\Http\ViewComposers;

    use Illuminate\View\View;
    use Modules\Category\Models\Category;

    class MenuViewComposer
    {
        public function compose(View $view)
        {
            $view->with('categories', Category::whereNull('parent_id')->with('childrenCategories')->get());
        }
    }
