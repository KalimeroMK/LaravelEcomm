<?php

namespace Modules\Brand\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Brand\Http\Requests\Api\Search;
use Modules\Brand\Http\Requests\Store;
use Modules\Brand\Models\Brand;
use Modules\Brand\Service\BrandService;
use Modules\Core\Http\Controllers\CoreController;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class BrandController extends CoreController
{
    protected BrandService $brand_service;

    public function __construct(BrandService $brand_service)
    {
        $this->brand_service = $brand_service;
        $this->authorizeResource(Brand::class, 'brand');
    }

    public function index(Search $request): View
    {
        $brands = $this->brand_service->getAll();

        return view('brand::index', compact('brands'));
    }

    public function create(): View
    {
        return view('brand::create', ['brand' => new Brand()]);
    }

    /**
     * @param  Store  $request
     * @return RedirectResponse
     */
    public function store(Store $request): RedirectResponse
    {
        $brand = $this->brand_service->create($request->validated());
        if ($request->hasFile('images')) {
            $brand->addMultipleMediaFromRequest(['images'])
                ->each(function ($fileAdder) {
                    $fileAdder->preservingOriginal()->toMediaCollection('brand');
                });
        }
        return redirect()->route('brands.index')->with('status', 'Brand created successfully.');
    }

    public function edit(Brand $brand): View
    {
        return view('brand::edit', compact('brand'));
    }

    /**
     * @param  Store  $request
     * @param  Brand  $brand
     * @return RedirectResponse
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(Store $request, Brand $brand): RedirectResponse
    {
        $this->brand_service->update($brand->id, $request->validated());
        if ($request->hasFile('images')) {
            $brand->clearMediaCollection('brand');
            $brand->addMultipleMediaFromRequest(['images'])
                ->each(function ($fileAdder) {
                    $fileAdder->preservingOriginal()->toMediaCollection('brand');
                });
        }
        return redirect()->route('brands.edit', $brand)->with('status', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $this->brand_service->delete($brand->id);

        return redirect()->route('brands.index')->with('status', 'Brand deleted successfully.');
    }
}
