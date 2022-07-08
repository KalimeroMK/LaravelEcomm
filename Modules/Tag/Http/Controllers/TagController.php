<?php

namespace Modules\Tag\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Tag\Http\Requests\Store;
use Modules\Tag\Models\Tag;

class TagController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tags-list');
        $this->middleware('permission:tags-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:tags-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:tags-delete', ['only' => ['destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $tags = Tag::orderBy('id', 'DESC')->paginate(10);
        
        return view('tag::index', compact('tags'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $status = Tag::create($request->validated());
        if ($status) {
            request()->session()->flash('success', 'Post Tag Successfully added');
        } else {
            request()->session()->flash('error', 'Please try again!!');
        }
        
        return redirect()->route('post-tag.index');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('tag::create');
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  Tag  $tag
     *
     * @return Application|Factory|View
     */
    public function edit(Tag $tag)
    {
        return view('tag::edit', compact('tag'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Store  $request
     * @param  Tag  $postTag
     *
     * @return RedirectResponse
     */
    public function update(Store $request, Tag $postTag): RedirectResponse
    {
        $status = $postTag->update($request->validated());
        if ($status) {
            request()->session()->flash('success', 'Post Tag Successfully updated');
        } else {
            request()->session()->flash('error', 'Please try again!!');
        }
        
        return redirect()->route('post-tag.index');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  Tag  $postTag
     *
     * @return RedirectResponse
     */
    public function destroy(Tag $postTag): RedirectResponse
    {
        $status = $postTag->delete();
        
        if ($status) {
            request()->session()->flash('success', 'Post Tag successfully deleted');
        } else {
            request()->session()->flash('error', 'Error while deleting post tag');
        }
        
        return redirect()->route('tags.index');
    }
}
