<?php

namespace Modules\Front\Http\ViewComposers;

use Illuminate\View\View;
use Modules\Page\Models\Page;

class InformationViewComposer
{
    public function compose(View $view): void
    {
        $pageList = Page::get(['title', 'slug']);
        $view->with('pageList', $pageList);
    }
}
