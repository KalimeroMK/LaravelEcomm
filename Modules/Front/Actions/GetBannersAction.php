<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Modules\Banner\Repository\BannerRepository;

class GetBannersAction
{
    public function __construct(private readonly BannerRepository $bannerRepository) {}

    /**
     * Get active banners, optionally filtered by category ID.
     */
    public function __invoke(?int $categoryId = null): array
    {
        if ($categoryId) {
            // Category-filtered banners are not cached — narrow query, low traffic
            $banners = \Modules\Banner\Models\Banner::active()
                ->whereHas('categories', fn ($q) => $q->where('categories.id', $categoryId))
                ->with(['categories', 'media'])
                ->get();
        } else {
            $banners = $this->bannerRepository->getActive();
        }

        return ['banners' => $banners];
    }
}
