<?php

declare(strict_types=1);

namespace Modules\Core\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class LanguageSwitcher extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        $locales = config('app.locales', []);
        $currentLocale = app()->getLocale();
        
        return view('core::components.language-switcher', [
            'locales' => $locales,
            'currentLocale' => $currentLocale,
        ]);
    }
}
