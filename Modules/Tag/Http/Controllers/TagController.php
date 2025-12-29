<?php

declare(strict_types=1);

namespace Modules\Tag\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Tag\Actions\CreateTagAction;
use Modules\Tag\Actions\DeleteTagAction;
use Modules\Tag\Actions\GetAllTagsAction;
use Modules\Tag\Actions\ShowTagAction;
use Modules\Tag\Actions\UpdateTagAction;
use Modules\Tag\DTOs\TagDto;
use Modules\Tag\Http\Requests\Api\Store;
use Modules\Tag\Models\Tag;

class TagController extends CoreController
{
    private CreateTagAction $createTagAction;

    private UpdateTagAction $updateTagAction;

    private DeleteTagAction $deleteTagAction;

    private GetAllTagsAction $getAllTagsAction;

    private ShowTagAction $showTagAction;

    public function __construct(
        CreateTagAction $createTagAction,
        UpdateTagAction $updateTagAction,
        DeleteTagAction $deleteTagAction,
        GetAllTagsAction $getAllTagsAction,
        ShowTagAction $showTagAction
    ) {
        $this->createTagAction = $createTagAction;
        $this->updateTagAction = $updateTagAction;
        $this->deleteTagAction = $deleteTagAction;
        $this->getAllTagsAction = $getAllTagsAction;
        $this->showTagAction = $showTagAction;
        $this->authorizeResource(Tag::class, 'tag');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): View|Factory
    {
        return view('tag::index', ['tags' => $this->getAllTagsAction->execute()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Store $request): RedirectResponse
    {
        $dto = TagDto::fromRequest($request);
        $this->createTagAction->execute($dto);

        return redirect()->route('post-tag.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory
    {
        return view('tag::create', ['tag' => new Tag]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return Application|Factory|View
     */
    public function edit(Tag $tag): View|Factory
    {
        return view('tag::edit', ['tag' => $this->showTagAction->execute($tag->id)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Store $request, Tag $tag): RedirectResponse
    {
        $dto = TagDto::fromRequest($request, $tag->id);
        $this->updateTagAction->execute($dto);

        return redirect()->route('post-tag.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $this->deleteTagAction->execute($tag->id);

        return redirect()->route('post-tag.index');
    }
}
