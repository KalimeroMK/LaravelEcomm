<?php

declare(strict_types=1);

namespace Modules\Page\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Support\Media\MediaUploader;
use Modules\Page\Actions\CreatePageAction;
use Modules\Page\Actions\DeletePageAction;
use Modules\Page\Actions\FindPageAction;
use Modules\Page\Actions\GetAllPagesAction;
use Modules\Page\Actions\UpdatePageAction;
use Modules\Page\DTOs\PageDTO;
use Modules\Page\Http\Requests\Store;
use Modules\Page\Models\Page;

class PageController extends Controller
{
    public function __construct(
        private readonly GetAllPagesAction $getAllPagesAction,
        private readonly FindPageAction $findAction,
        private readonly CreatePageAction $createAction,
        private readonly UpdatePageAction $updateAction,
        private readonly DeletePageAction $deleteAction
    ) {
        $this->authorizeResource(Page::class, 'page');
    }

    public function index(): View
    {
        $pagesDto = $this->getAllPagesAction->execute();

        return view('page::index', ['pages' => $pagesDto->pages]);
    }

    public function create(): View
    {
        return view('page::create', ['page' => new Page]);
    }

    public function store(Store $request): RedirectResponse
    {
        $page = $this->createAction->execute(PageDTO::fromRequest($request));
        MediaUploader::uploadSingle($page, 'featured_image', 'featured_image');

        return redirect()->route('pages.index')->with('status', 'Page created successfully.');
    }

    public function edit(Page $page): View
    {
        $page = $this->findAction->execute($page->id);

        return view('page::edit', ['page' => $page]);
    }

    public function update(Store $request, Page $page): RedirectResponse
    {
        $dto = PageDTO::fromRequest($request, $page->id, $page);
        $this->updateAction->execute($dto);
        if ($request->hasFile('featured_image')) {
            MediaUploader::clearAndUpload($page, ['featured_image'], 'featured_image');
        }

        return redirect()->route('pages.edit', $page)->with('status', 'Page updated successfully.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $this->deleteAction->execute($page->id);

        return redirect()->route('pages.index')->with('status', 'Page deleted successfully.');
    }
}
