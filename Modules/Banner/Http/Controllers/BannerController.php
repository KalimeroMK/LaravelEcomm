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
use Modules\Brand\Service\BrandService;

class BannerController extends Controller
{
    private BrandService $banner_service;
    
    public function __construct(BrandService $banner_service)
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
        return view('banner::index', ['banners' => $this->banner_service->getAll()]);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return view('banner::create', ['banner' => new Banner()]);
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
        $this->banner_service->store($request->validated());
        
        return redirect()->route('banners.index');
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
        $banner = $this->banner_service->edit($banner->id);
        
        return view('banner::edit', compact('banner'));
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
        $banner = $this->banner_service->update($banner->id, $request->validated());
        
        return redirect()->route('banners.edit', $banner);
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
        $this->banner_service->destroy($banner);
        
        return redirect()->route('banners.index');
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
