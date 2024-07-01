<?php

namespace Modules\Page\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Page\Http\Requests\Store;
use Modules\Page\Models\Page;
use Modules\Page\Service\PageService;

class PageController extends Controller
{
    protected PageService $page_service;

    public function __construct(PageService $page_service)
    {
        $this->page_service = $page_service;
    }

    public function index(): View
    {
        $pages = $this->page_service->getAll();

        return view('page::index', compact('pages'));
    }

    public function create(): View
    {
        return view('page::create', ['page' => new Page()]);
    }

    public function store(Store $request): RedirectResponse
    {
        $this->page_service->create($request->validated());

        return redirect()->route('pages.index')->with('status', 'Page created successfully.');
    }

    public function edit(Page $page): View
    {
        return view('page::edit', compact('page'));
    }

    public function update(Store $request, Page $page): RedirectResponse
    {
        $this->page_service->update($page->id, $request->validated());

        return redirect()->route('pages.edit', $page)->with('status', 'Page updated successfully.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $this->page_service->delete($page->id);

        return redirect()->route('pages.index')->with('status', 'Page deleted successfully.');
    }
}
