<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch application language
     */
    public function switchLang(Request $request, string $lang): RedirectResponse
    {
        $locales = config('app.locales', []);

        if (array_key_exists($lang, $locales)) {
            Session::put('locale', $lang);
            app()->setLocale($lang);

            // Store in user preferences if user is authenticated
            if (auth()->check()) {
                auth()->user()->update(['locale' => $lang]);
            }
        }

        return Redirect::back();
    }
}
