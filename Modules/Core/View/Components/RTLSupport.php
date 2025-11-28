<?php

declare(strict_types=1);

namespace Modules\Core\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class RTLSupport extends Component
{
    public string $currentLocale;

    public bool $isRTL;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->currentLocale = app()->getLocale();
        $this->isRTL = $this->isRTLLocale($this->currentLocale);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('core::components.rtl-support', [
            'isRTL' => $this->isRTL,
            'currentLocale' => $this->currentLocale,
        ]);
    }

    /**
     * Check if locale is RTL
     */
    private function isRTLLocale(string $locale): bool
    {
        $locales = config('app.locales', []);

        return $locales[$locale]['rtl'] ?? false;
    }
}
