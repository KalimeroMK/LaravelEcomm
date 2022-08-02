<?php

namespace Modules\Newsletter\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Modules\Newsletter\Http\Requests\Store;
use Modules\Newsletter\Http\Requests\Store as Update;
use Modules\Newsletter\Models\Newsletter;
use Modules\Newsletter\Service\NewsletterService;

class NewsletterController extends Controller
{
    private NewsletterService $newsletter_service;
    
    public function __construct(NewsletterService $newsletter_service)
    {
        $this->middleware('permission:newsletter-list');
        $this->middleware('permission:newsletter-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:newsletter-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:newsletter-delete', ['only' => ['destroy']]);
        $this->newsletter_service = $newsletter_service;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): Factory|View|Application
    {
        return view('newsletter::index', ['newsletters' => $this->newsletter_service->getAll()]);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return view('newsletter::create', ['newsletter' => new Newsletter()]);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  Store  $request
     *
     * @return RedirectResponse
     */
    public function store(Store $request): RedirectResponse
    {
        $this->newsletter_service->store($request->validated());
        
        return redirect()->route('newsletters.index');
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  Newsletter  $newsletter
     *
     * @return Application|Factory|View
     */
    public function edit(Newsletter $newsletter): View|Factory|Application
    {
        $newsletter = $this->newsletter_service->edit($newsletter->id);
        
        return view('newsletter::edit', compact('newsletter'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Update  $request
     * @param  Newsletter  $newsletter
     *
     * @return RedirectResponse
     */
    public function update(Update $request, Newsletter $newsletter): RedirectResponse
    {
        $this->newsletter_service->update($newsletter->id, $request->validated());
        
        return redirect()->route('newsletters.index');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  Newsletter  $newsletter
     *
     * @return RedirectResponse
     */
    public function destroy(Newsletter $newsletter): RedirectResponse
    {
        $this->newsletter_service->destroy($newsletter);
        
        return redirect()->route('newsletters.index');
    }
    
}
