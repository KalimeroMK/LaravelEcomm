<?php

declare(strict_types=1);

namespace Modules\Newsletter\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Newsletter\Actions\CreateNewsletterAction;
use Modules\Newsletter\Actions\DeleteNewsletterAction;
use Modules\Newsletter\Actions\UpdateNewsletterAction;
use Modules\Newsletter\DTOs\NewsletterDTO;
use Modules\Newsletter\Http\Requests\Store;
use Modules\Newsletter\Models\Newsletter;
use Modules\Newsletter\Repository\NewsletterRepository;

class NewsletterController extends CoreController
{
    private readonly NewsletterRepository $repository;

    private readonly CreateNewsletterAction $createAction;

    private readonly UpdateNewsletterAction $updateAction;

    private readonly DeleteNewsletterAction $deleteAction;

    public function __construct(
        CreateNewsletterAction $createAction,
        UpdateNewsletterAction $updateAction,
        DeleteNewsletterAction $deleteAction,
        NewsletterRepository $repository
    ) {
        $this->createAction = $createAction;
        $this->updateAction = $updateAction;
        $this->deleteAction = $deleteAction;
        $this->repository = $repository;
        $this->authorizeResource(Newsletter::class, 'newsletter');
    }

    public function index(): View
    {
        $newslettersDto = $this->repository->findAll();

        return view('newsletter::index', ['newsletters' => $newslettersDto]);
    }

    public function create(): View
    {
        return view('newsletter::create', ['newsletter' => new Newsletter]);
    }

    public function store(Store $request): RedirectResponse
    {
        $dto = NewsletterDTO::fromRequest($request);
        $this->createAction->execute($dto);

        return redirect()->route('newsletters.index')->with('status', 'Newsletter created successfully!');
    }

    public function edit(Newsletter $newsletter): View
    {
        return view('newsletter::edit', ['newsletter' => $newsletter]);
    }

    public function update(Store $request, Newsletter $newsletter): RedirectResponse
    {
        $dto = NewsletterDTO::fromRequest($request, $newsletter->id);
        $this->updateAction->execute($dto);

        return redirect()->route('newsletters.index')->with('status', 'Newsletter updated successfully!');
    }

    public function destroy(Newsletter $newsletter): RedirectResponse
    {
        $this->deleteAction->execute($newsletter->id);

        return redirect()->route('newsletters.index')->with('status', 'Newsletter deleted successfully!');
    }
}
