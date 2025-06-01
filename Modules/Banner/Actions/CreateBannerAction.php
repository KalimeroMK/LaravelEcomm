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
        return $this->repository->create([
            'title' => $dto->title,
            'slug' => $dto->slug,
            'description' => $dto->description,
            'status' => $dto->status,
        ]);
    }
}
