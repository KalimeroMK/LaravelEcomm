<?php

declare(strict_types=1);

namespace Modules\Banner\Actions;

use Modules\Banner\Models\Banner;
use Modules\Banner\Repository\BannerRepository;

readonly class FindBannerAction
{
    public function __construct(private BannerRepository $repository) {}

    public function execute(int $id): Banner
    {
        return $this->repository->findById($id);
    }
}
