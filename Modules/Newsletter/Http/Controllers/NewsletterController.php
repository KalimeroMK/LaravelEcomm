<?php

namespace Modules\Newsletter\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Newsletter\Http\Requests\Store;
use Modules\Newsletter\Models\Newsletter;
use Modules\Newsletter\Service\NewsletterService;

class NewsletterController extends CoreController
{
    private NewsletterService $newsletter_service;

    public function __construct(NewsletterService $newsletter_service)
    {
        $this->newsletter_service = $newsletter_service;
    }

    public function index(): View
    {
        $newsletters = $this->newsletter_service->getAll();

        return view('newsletter::index', compact('newsletters'));
    }

    public function create(): View
    {
        return view('newsletter::create', ['newsletter' => new Newsletter()]);
    }

    public function store(Store $request): RedirectResponse
    {
        $this->newsletter_service->create($request->validated());

        return redirect()->route('newsletters.index')->with('status', 'Newsletter created successfully!');
    }

    public function edit(Newsletter $newsletter): View
    {
        return view('newsletter::edit', compact('newsletter'));
    }

    public function update(Store $request, Newsletter $newsletter): RedirectResponse
    {
        $this->newsletter_service->update($newsletter->id, $request->validated());

        return redirect()->route('newsletters.index')->with('status', 'Newsletter updated successfully!');
    }

    public function destroy(Newsletter $newsletter): RedirectResponse
    {
        $this->newsletter_service->delete($newsletter->id);

        return redirect()->route('newsletters.index')->with('status', 'Newsletter deleted successfully!');
    }
}
