<?php

declare(strict_types=1);

namespace Modules\Front\Http\ViewComposers;

use Exception;
use Illuminate\View\View;

class SettingsViewComposer
{
    public function compose(View $view): void
    {
        try {
            $settings = app('settings');
            // If settings is null, try to get from database
            if ($settings === null) {
                $settings = \Modules\Settings\Models\Setting::first();
            }
        } catch (Exception $e) {
            // If settings service is not available, try to get from database
            try {
                $settings = \Modules\Settings\Models\Setting::first();
            } catch (Exception $e2) {
                // If database is not available, create empty collection
                $settings = collect([]);
            }
        }

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
