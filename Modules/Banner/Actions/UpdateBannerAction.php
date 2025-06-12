<?php

declare(strict_types=1);

namespace Modules\Banner\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Banner\DTOs\BannerDTO;
use Modules\Banner\Repository\BannerRepository;

readonly class UpdateBannerAction
{
    public function __construct(private BannerRepository $repository) {}

    public function execute(BannerDTO $dto): Model
    {
        $banner = $this->repository->findById($dto->id);

        $banner->update([
            'title' => $dto->title,
            'slug' => $dto->slug ?? $banner->slug,
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
