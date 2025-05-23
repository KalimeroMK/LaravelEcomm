<?php

declare(strict_types=1);

namespace Modules\Page\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Page\Actions\CreatePageAction;
use Modules\Page\Actions\DeletePageAction;
use Modules\Page\Actions\GetAllPagesAction;
use Modules\Page\Actions\UpdatePageAction;
use Modules\Page\Http\Requests\Store;
use Modules\Page\Models\Page;

class PageController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Page::class, 'page');
    }

    public function index(): View
    {
        $pagesDto = (new GetAllPagesAction())->execute();

        return view('page::index', ['pages' => $pagesDto->pages]);
    }

    public function create(): View
    {
        return view('page::create', ['page' => new Page]);
    }

    public function store(Store $request): RedirectResponse
    {
        (new CreatePageAction())->execute($request->validated());

        return redirect()->route('pages.index')->with('status', 'Page created successfully.');
    }

    public function edit(Page $page): View
    {
        return view('page::edit', ['page' => $page]);
    }

    public function update(Store $request, Page $page): RedirectResponse
    {
        (new UpdatePageAction())->execute($page->id, $request->validated());

        return redirect()->route('pages.edit', $page)->with('status', 'Page updated successfully.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        (new DeletePageAction())->execute($page->id);

        return redirect()->route('pages.index')->with('status', 'Page deleted successfully.');
    }
}
