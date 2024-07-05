<?php

namespace Modules\Tag\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Tag\Http\Requests\Api\Store;
use Modules\Tag\Models\Tag;
use Modules\Tag\Service\TagService;

class TagController extends CoreController
{
    private TagService $tag_service;

    public function __construct(TagService $tag_service)
    {
        $this->tag_service = $tag_service;
        $this->authorizeResource(Tag::class, 'tag');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('tag::index', ['tags' => $this->tag_service->getAll()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Store $request): RedirectResponse
    {
        $this->tag_service->create($request->validated());

        return redirect()->route('tags.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('tag::create', ['tag' => new Tag()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return Application|Factory|View
     */
    public function edit(Tag $tag)
    {
        return view('tag::edit', ['tag' => $this->tag_service->findById($tag->id)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Store $request, Tag $tag): RedirectResponse
    {
        $this->tag_service->update($tag->id, $request->validated());

        return redirect()->route('post-tag.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $this->tag_service->delete($tag->id);

        return redirect()->route('tags.index');
    }
}
