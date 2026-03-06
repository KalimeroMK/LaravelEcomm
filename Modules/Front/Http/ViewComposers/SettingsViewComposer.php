<?php

declare(strict_types=1);

namespace Modules\Front\Http\ViewComposers;

use Exception;
use Illuminate\View\View;

class SettingsViewComposer
{
    public function compose(View $view): void
    {
        // Use singleton from app container (cached)
        $settings = app('settings');

        // Views expect settings to be iterable (collection or array)
        // If it's a single model, wrap it in a collection
        if ($settings instanceof \Modules\Settings\Models\Setting) {
            $settings = collect([$settings]);
        } elseif (! is_iterable($settings)) {
            $settings = collect([]);
        }

        $view->with('settings', $settings);
    }
}
