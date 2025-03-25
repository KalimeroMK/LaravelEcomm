<?php

declare(strict_types=1);

namespace Modules\Banner\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Banner\Http\Requests\Store;
use Modules\Banner\Http\Requests\Update;
use Modules\Banner\Models\Banner;
use Modules\Banner\Service\BannerService;
use Modules\Core\Http\Controllers\CoreController;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class BannerController extends CoreController
{
    private BannerService $banner_service;

    public function __construct(BannerService $banner_service)
    {
        $this->authorizeResource(Banner::class, 'banner');
        $this->banner_service = $banner_service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Factory|View|Application
    {
        return view('banner::index', ['banners' => $this->banner_service->getAll()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory|Application
    {
        return view('banner::create', ['banner' => new Banner]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws Exception
     */
    public function store(Store $request): RedirectResponse
    {
        $this->banner_service->create($request->validated());

        return redirect()->route('banners.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner): View|Factory|Application
    {
        return view('banner::edit', ['banner' => $banner]);
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(Update $request, Banner $banner): RedirectResponse
    {
        $this->banner_service->update($banner->id, $request->validated());

        return redirect()->route('banners.edit', $banner);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner): RedirectResponse
    {
        $this->banner_service->delete($banner->id);

        return redirect()->route('banners.index');
    }
}
