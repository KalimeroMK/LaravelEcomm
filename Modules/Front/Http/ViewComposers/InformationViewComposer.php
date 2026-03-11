<?php

declare(strict_types=1);

namespace Modules\Front\Http\ViewComposers;

use Illuminate\Database\QueryException;
use Illuminate\View\View;
use Modules\Page\Models\Page;

class InformationViewComposer
{
    public function compose(View $view): void
    {
        try {
            $pageList = Page::get(['title', 'slug']);
            $view->with('pageList', $pageList);
        } catch (QueryException $e) {
            // Database not available, use empty collection
            $view->with('pageList', collect());
        }
    }
}
