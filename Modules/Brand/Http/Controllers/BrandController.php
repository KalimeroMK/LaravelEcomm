<?php

namespace Modules\Brand\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Brand\Exceptions\SearchException;
use Modules\Brand\Http\Requests\Api\Search;
use Modules\Brand\Http\Requests\Store;
use Modules\Brand\Models\Brand;
use Modules\Brand\Service\BrandService;
use Modules\Core\Http\Controllers\CoreController;

class BrandController extends CoreController
{
    private BrandService $brand_service;
    
    public function __construct(BrandService $brand_service)
    {
        $this->brand_service = $brand_service;
        $this->authorizeResource(Brand::class);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     * @throws SearchException
     */
    public function index(Search $request)
    {
        return view('brand::index', ['brands' => $this->brand_service->getAll($request->validated())]);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('brand::create', ['brand' => new Brand()]);
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
        $this->brand_service->store($request->validated());
        
        return redirect()->route('brands.index');
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  Brand  $brand
     *
     * @return Application|Factory|View
     */
    public function edit(Brand $brand)
    {
        $brand = $this->brand_service->edit($brand->id);
        
        return view('brand::edit', compact('brand'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Store  $request
     * @param  Brand  $brand
     *
     * @return RedirectResponse
     */
    public function update(Store $request, Brand $brand): RedirectResponse
    {
        $brand = $this->brand_service->update($brand->id, $request->validated());
        
        return redirect()->route('brands.edit', $brand);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  Brand  $brand
     *
     * @return RedirectResponse
     */
    public function destroy(Brand $brand): RedirectResponse
    {
        $this->brand_service->destroy($brand->id);
        
        return redirect()->route('brands.index');
    }
}
