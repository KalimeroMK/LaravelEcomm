<?php

namespace Modules\Banner\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ImageUpload;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Banner\Http\Requests\Store;
use Modules\Banner\Http\Requests\Update;
use Modules\Banner\Models\Banner;
use Modules\Banner\Service\BannerService;

class BannerController extends Controller
{
    private BannerService $banner_service;
    
    public function __construct(BannerService $banner_service)
    {
        $this->middleware('permission:banner-list');
        $this->middleware('permission:banner-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:banner-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:banner-delete', ['only' => ['destroy']]);
        $this->banner_service = $banner_service;
    }
    
    use ImageUpload;
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): Factory|View|Application
    {
        $banners = $this->banner_service->index();
        
        return view('banner::index', compact('banners'));
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
        $this->banner_service->store($request);
        
        return redirect()->route('banners.index');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return $this->banner_service->create();
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  Banner  $banner
     *
     * @return Application|Factory|View
     */
    public function edit(Banner $banner): View|Factory|Application
    {
        return $this->banner_service->edit($banner);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Update  $request
     * @param  Banner  $banner
     *
     * @return RedirectResponse
     */
    public function update(Update $request, Banner $banner): RedirectResponse
    {
        return $this->banner_service->update($request, $banner);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  Banner  $banner
     *
     * @return RedirectResponse
     */
    public function destroy(Banner $banner): RedirectResponse
    {
        return $this->banner_service->destroy($banner);
    }
    
    /**
     * Make paths for storing images.
     *
     * @return object
     */
    public function makePaths(): object
    {
        return $this->banner_service->makePaths();
    }
}
