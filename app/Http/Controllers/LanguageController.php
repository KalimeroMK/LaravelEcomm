<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Modules\Language\Models\Language;

class LanguageController extends Controller
{
    /**
     * Switch application language and redirect to same page with new locale
     */
    public function switchLang(Request $request, string $lang): RedirectResponse
    {
        // Validate language code using Language model
        if (! Language::isValidCode($lang)) {
            abort(404);
        }

        // Set locale in session
        Session::put('locale', $lang);
        app()->setLocale($lang);

        // Store in user preferences if user is authenticated
        if (auth()->check()) {
            auth()->user()->update(['locale' => $lang]);
        }

        // Get the previous URL (where user came from)
        $previousUrl = url()->previous();
        
        // Generate new URL with updated locale
        $newUrl = $this->replaceLocaleInUrl($previousUrl, $lang);

        return Redirect::to($newUrl);
    }

    /**
     * Replace locale in URL with new locale
     * 
     * Examples:
     * - /en/products -> /de/products
     * - /mk/blog/post-1 -> /en/blog/post-1
     * - /products (no locale) -> /de/products
     */
    private function replaceLocaleInUrl(string $url, string $newLocale): string
    {
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '/';
        
        // Get active language codes
        $activeLocales = Language::getActiveCodes();
        
        // Build pattern to match any active locale at the start of path
        $localePattern = '/^\/(' . implode('|', array_map('preg_quote', $activeLocales)) . ')(\/|$)/i';
        
        // Replace existing locale or prepend new locale
        if (preg_match($localePattern, $path, $matches)) {
            // Replace existing locale
            $newPath = preg_replace($localePattern, '/' . $newLocale . '$2', $path, 1);
        } else {
            // No locale in path, prepend it
            // Handle root path
            if ($path === '/') {
                $newPath = '/' . $newLocale;
            } else {
                $newPath = '/' . $newLocale . $path;
            }
        }
        
        // Rebuild URL
        $newUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        
        if (isset($parsedUrl['port'])) {
            $newUrl .= ':' . $parsedUrl['port'];
        }
        
        $newUrl .= $newPath;
        
        if (isset($parsedUrl['query'])) {
            $newUrl .= '?' . $parsedUrl['query'];
        }
        
        if (isset($parsedUrl['fragment'])) {
            $newUrl .= '#' . $parsedUrl['fragment'];
        }
        
        return $newUrl;
    }
}
