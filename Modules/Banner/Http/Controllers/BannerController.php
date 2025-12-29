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
use Modules\Banner\Actions\FindBannerAction;
use Modules\Banner\Actions\GetAllBannersAction;
use Modules\Banner\Actions\UpdateBannerAction;
use Modules\Banner\DTOs\BannerDTO;
use Modules\Banner\Http\Requests\Store;
use Modules\Banner\Http\Requests\Update;
use Modules\Banner\Models\Banner;
use Modules\Banner\Repository\BannerRepository;
use Modules\Category\Actions\GetAllCategoriesAction;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Core\Support\Media\MediaUploader;

class BannerController extends CoreController
{
    public function __construct(
        private readonly BannerRepository $repository,
        private readonly GetAllCategoriesAction $getAllCategoriesAction,
        private readonly GetAllBannersAction $getAllBannersAction,
        private readonly FindBannerAction $findBannerAction,
        private readonly CreateBannerAction $createAction,
        private readonly UpdateBannerAction $updateAction,
        private readonly DeleteBannerAction $deleteAction
    ) {
        $this->authorizeResource(Banner::class, 'banner');
    }

    public function index(): View|Factory|Application
    {
        return view('banner::index', [
            'banners' => $this->getAllBannersAction->execute(),
        ]);
    }

    public function create(): View|Factory|Application
    {
        return view('banner::create', [
            'banner' => new Banner,
            'categories' => $this->getAllCategoriesAction->execute(),
        ]);
    }

    /**
     * @throws Exception
     */
    public function store(Store $request): RedirectResponse
    {
        $banner = $this->createAction->execute(BannerDTO::fromRequest($request));
        MediaUploader::uploadMultiple($banner, ['images'], 'banner');

        return redirect()->route('banners.index')
            ->with('success', __('Banner created successfully.'));
    }

    public function edit(Banner $banner): View|Factory|Application
    {
        return view('banner::edit', [
            'banner' => $this->findBannerAction->execute($banner->id),
            'categories' => $this->getAllCategoriesAction->execute(),
        ]);
    }

    public function update(Update $request, Banner $banner): RedirectResponse
    {
        $this->updateAction->execute(BannerDTO::fromRequest($request, $banner->id, $banner));
        MediaUploader::clearAndUpload($banner, ['images'], 'banner');

        return redirect()->route('banners.edit', $banner)
            ->with('success', __('Banner updated successfully.'));
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $this->authorize('delete', $banner);

        $this->deleteAction->execute($banner->id);

        return redirect()->route('banners.index')
            ->with('success', __('Banner deleted successfully.'));
    }
}
