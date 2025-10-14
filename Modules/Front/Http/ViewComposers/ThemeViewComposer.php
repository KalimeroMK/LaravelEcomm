<?php

declare(strict_types=1);

namespace Modules\Front\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Modules\Settings\Models\Setting;

class ThemeViewComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $activeTheme = $this->getActiveTheme();
        
        $view->with('activeTheme', $activeTheme);
        $view->with('themePath', "front::themes.{$activeTheme}");
        $view->with('themeAsset', function ($path) use ($activeTheme) {
            return asset("frontend/themes/{$activeTheme}/{$path}");
        });
    }

    /**
     * Get the active theme from settings.
     */
    private function getActiveTheme(): string
    {
        try {
            $setting = \Modules\Settings\Models\Setting::first();
            return $setting->active_template ?? 'default';
        } catch (\Exception $e) {
            return 'default';
        }
    }
}
