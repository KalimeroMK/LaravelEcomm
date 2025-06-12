<?php

declare(strict_types=1);

namespace Modules\Banner\Actions;

use Modules\Banner\DTOs\BannerDTO;
use Modules\Banner\Models\Banner;
use Modules\Banner\Repository\BannerRepository;

readonly class CreateBannerAction
{
    public function __construct(private BannerRepository $repository) {}

    public function execute(BannerDTO $dto): Banner
    {
        $banner = $this->repository->create([
            'title' => $dto->title,
            'slug' => $dto->slug,
            'description' => $dto->description,
            'status' => $dto->status,
            'active_from' => $dto->active_from,
            'active_to' => $dto->active_to,
            'max_clicks' => $dto->max_clicks,
            'max_impressions' => $dto->max_impressions,
        ]);
        if (! empty($dto->categories)) {
            $banner->categories()->sync($dto->categories);
        }

        return $banner;
    }
}
