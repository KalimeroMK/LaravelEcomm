<?php

declare(strict_types=1);

namespace Modules\Page\Models\Observers;

use Illuminate\Support\Facades\Auth;
use Modules\Page\Models\Page;

class PageObserver
{
    public function creating(Page $page): void
    {
        if (Auth::id()) {
            $page->user_id = Auth::id();
        }
    }

    public function updating(Page $page): void
    {
        if (Auth::id()) {
            $page->user_id = Auth::id();
        }
    }
}
