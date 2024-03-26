<?php

namespace Modules\Front\Http\ViewComposers;

use Illuminate\View\View;

class SettingsViewComposer
{
    public function compose(View $view): void
    {
        $settings = app('settings');

        $view->with('settings', $settings);
    }
}
