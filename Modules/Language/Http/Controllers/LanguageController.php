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

        return redirect()->back();
    }
}
