<?php

declare(strict_types=1);

namespace Modules\Language\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Language\Actions\CreateLanguageAction;
use Modules\Language\Actions\DeleteLanguageAction;
use Modules\Language\Actions\UpdateLanguageAction;
use Modules\Language\DTOs\LanguageDTO;
use Modules\Language\Http\Requests\LanguageRequest;
use Modules\Language\Models\Language;

class LanguageController extends CoreController
{
    public function __construct(
        private readonly CreateLanguageAction $createAction,
        private readonly UpdateLanguageAction $updateAction,
        private readonly DeleteLanguageAction $deleteAction,
    ) {}

    public function index(): View
    {
        $languages = Language::ordered()->paginate(20);

        return view('language::index', compact('languages'));
    }

    public function create(): View
    {
        return view('language::create');
    }

    public function store(LanguageRequest $request): RedirectResponse
    {
        $dto = LanguageDTO::fromRequest($request->validated());
        
        $this->createAction->execute($dto);

        return redirect()->route('admin.languages.index')
            ->with('success', __('language::messages.created'));
    }

    public function edit(Language $language): View
    {
        return view('language::edit', compact('language'));
    }

    public function update(LanguageRequest $request, Language $language): RedirectResponse
    {
        $dto = LanguageDTO::fromRequest($request->validated());
        
        try {
            $this->updateAction->execute($language, $dto);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['is_active' => $e->getMessage()]);
        }

        return redirect()->route('admin.languages.index')
            ->with('success', __('language::messages.updated'));
    }

    public function destroy(Language $language): RedirectResponse
    {
        try {
            $this->deleteAction->execute($language);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('admin.languages.index')
            ->with('success', __('language::messages.deleted'));
    }

    public function switchLang(string $lang): RedirectResponse
    {
        if (! Language::isValidCode($lang)) {
            abort(404);
        }

        session()->put('locale', $lang);
        app()->setLocale($lang);

        // Store in user preferences if user is authenticated
        if (auth()->check()) {
            auth()->user()->update(['locale' => $lang]);
        }

        // Get the previous URL and replace locale
        $previousUrl = url()->previous();
        $newUrl = $this->replaceLocaleInUrl($previousUrl, $lang);

        return redirect()->to($newUrl);
    }

    /**
     * Replace locale in URL with new locale
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
            $newPath = preg_replace($localePattern, '/' . $newLocale . '$2', $path, 1);
        } else {
            // No locale in path, prepend it
            $newPath = $path === '/' ? '/' . $newLocale : '/' . $newLocale . $path;
        }
        
        // Rebuild URL
        $newUrl = ($parsedUrl['scheme'] ?? 'http') . '://' . $parsedUrl['host'];
        
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
