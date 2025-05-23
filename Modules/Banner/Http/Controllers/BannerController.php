<?php

declare(strict_types=1);

namespace Modules\Banner\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Banner\Actions\CreateBannerAction;
use Modules\Banner\Actions\DeleteBannerAction;
use Modules\Banner\Actions\UpdateBannerAction;
use Modules\Banner\DTOs\BannerDTO;
use Modules\Banner\Http\Requests\Store;
use Modules\Banner\Http\Requests\Update;
use Modules\Banner\Models\Banner;
use Modules\Banner\Repository\BannerRepository;
use Modules\Core\Http\Controllers\CoreController;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class BannerController extends CoreController
{
    private BannerRepository $banner_repository;

    private CreateBannerAction $createBannerAction;

    private UpdateBannerAction $updateBannerAction;

    private DeleteBannerAction $deleteBannerAction;

    public function __construct(
        BannerRepository $banner_repository,
        CreateBannerAction $createBannerAction,
        UpdateBannerAction $updateBannerAction,
        DeleteBannerAction $deleteBannerAction
    ) {
        $this->authorizeResource(Banner::class, 'banner');
        $this->banner_repository = $banner_repository;
        $this->createBannerAction = $createBannerAction;
        $this->updateBannerAction = $updateBannerAction;
        $this->deleteBannerAction = $deleteBannerAction;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Factory|View|Application
    {
        return view('banner::index', ['banners' => $this->banner_repository->all()]);
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
        $dto = BannerDTO::fromRequest($request);
        $this->createBannerAction->execute($dto);

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
        $dto = BannerDTO::fromRequest($request);
        // Ensure DTO has the correct id for update
        $dtoWithId = new BannerDTO(
            $banner->id,
            $dto->title,
            $dto->slug,
            $dto->description,
            $dto->status,
            $dto->images
        );
        $this->updateBannerAction->execute($dtoWithId);

        return redirect()->route('banners.edit', $banner);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner): RedirectResponse
    {
        $this->deleteBannerAction->execute($banner->id);

        return redirect()->route('banners.index');
    }
}
