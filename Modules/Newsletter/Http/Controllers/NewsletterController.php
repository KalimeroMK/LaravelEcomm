<?php

declare(strict_types=1);

namespace Modules\Newsletter\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Newsletter\Actions\CreateNewsletterAction;
use Modules\Newsletter\Actions\DeleteNewsletterAction;
use Modules\Newsletter\Actions\GetAllNewslettersAction;
use Modules\Newsletter\Actions\UpdateNewsletterAction;
use Modules\Newsletter\Http\Requests\Store;
use Modules\Newsletter\Models\Newsletter;

class NewsletterController extends CoreController
{
    public function __construct() {}

    public function index(): View
    {
        $newslettersDto = (new GetAllNewslettersAction())->execute();

        return view('newsletter::index', ['newsletters' => $newslettersDto->newsletters]);
    }

    public function create(): View
    {
        return view('newsletter::create', ['newsletter' => new Newsletter]);
    }

    public function store(Store $request): RedirectResponse
    {
        (new CreateNewsletterAction())->execute($request->validated());

        return redirect()->route('newsletters.index')->with('status', 'Newsletter created successfully!');
    }

    public function edit(Newsletter $newsletter): View
    {
        return view('newsletter::edit', ['newsletter' => $newsletter]);
    }

    public function update(Store $request, Newsletter $newsletter): RedirectResponse
    {
        (new UpdateNewsletterAction())->execute($newsletter->id, $request->validated());

        return redirect()->route('newsletters.index')->with('status', 'Newsletter updated successfully!');
    }

    public function destroy(Newsletter $newsletter): RedirectResponse
    {
        (new DeleteNewsletterAction())->execute($newsletter->id);

        return redirect()->route('newsletters.index')->with('status', 'Newsletter deleted successfully!');
    }
}
