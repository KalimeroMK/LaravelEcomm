<?php

namespace Modules\Front\Http\ViewComposers;

use Illuminate\View\View;
use Modules\Settings\Models\Setting;

class SettingsViewComposer
{
    public function compose(View $view): void
    {
        $view->with('settings', Setting::get());
    }
}
