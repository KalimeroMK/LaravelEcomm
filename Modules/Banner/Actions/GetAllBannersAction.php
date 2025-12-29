<?php

declare(strict_types=1);

namespace Modules\Banner\Actions;

use Illuminate\Support\Collection;
use Modules\Banner\Repository\BannerRepository;

readonly class GetAllBannersAction
{
    public function __construct(private BannerRepository $repository) {}

    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
