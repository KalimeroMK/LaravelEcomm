<?php

declare(strict_types=1);

use Modules\Language\Models\Language;

if (! function_exists('current_locale')) {
    /**
     * Get current locale
     */
    function current_locale(): string
    {
        return app()->getLocale();
    }
}

if (! function_exists('default_locale')) {
    /**
     * Get default locale
     */
    function default_locale(): string
    {
        return Language::getDefaultCode();
    }
}

if (! function_exists('is_default_locale')) {
    /**
     * Check if current locale is default
     */
    function is_default_locale(?string $locale = null): bool
    {
        $locale = $locale ?? current_locale();
        return $locale === default_locale();
    }
}

if (! function_exists('localized_url')) {
    /**
     * Generate URL with locale prefix
     */
    function localized_url(string $path, ?string $locale = null): string
    {
        $locale = $locale ?? current_locale();
        
        // Remove leading slash if present
        $path = ltrim($path, '/');
        
        return url("/{$locale}/{$path}");
    }
}

if (! function_exists('localized_route')) {
    /**
     * Generate route with locale parameter
     */
    function localized_route(string $name, array $parameters = [], ?string $locale = null): string
    {
        $locale = $locale ?? current_locale();
        
        // Prepend locale to parameters
        $parameters = array_merge(['locale' => $locale], $parameters);
        
        return route($name, $parameters);
    }
}

if (! function_exists('switch_locale_url')) {
    /**
     * Generate URL for switching to a different locale
     * Maintains the current path but changes the locale
     */
    function switch_locale_url(string $targetLocale): string
    {
        $currentPath = request()->path();
        $segments = explode('/', $currentPath);
        
        // Replace or add locale prefix
        if (count($segments) > 0 && strlen($segments[0]) === 2) {
            $segments[0] = $targetLocale;
        } else {
            array_unshift($segments, $targetLocale);
        }
        
        $newPath = implode('/', $segments);
        $query = request()->getQueryString();
        
        return url($newPath . ($query ? '?' . $query : ''));
    }
}

if (! function_exists('active_locales')) {
    /**
     * Get all active locales
     *
     * @return array<string>
     */
    function active_locales(): array
    {
        return Language::getActiveCodes();
    }
}

if (! function_exists('language_name')) {
    /**
     * Get language name by code
     */
    function language_name(string $code): ?string
    {
        $languages = Language::getActiveList();
        return $languages[$code] ?? null;
    }
}

if (! function_exists('language_direction')) {
    /**
     * Get language direction (ltr/rtl)
     */
    function language_direction(?string $locale = null): string
    {
        $locale = $locale ?? current_locale();
        
        $language = Language::where('code', $locale)->first();
        
        return $language?->direction ?? 'ltr';
    }
}

if (! function_exists('is_rtl')) {
    /**
     * Check if current language is RTL
     */
    function is_rtl(?string $locale = null): bool
    {
        return language_direction($locale) === 'rtl';
    }
}
