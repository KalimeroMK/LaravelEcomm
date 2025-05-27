<?php

declare(strict_types=1);

namespace Modules\Banner\Actions;

use Modules\Banner\Repository\BannerRepository;

readonly class DeleteBannerAction
{
    private BannerRepository $repository;

    public function __construct(BannerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): void
    {
        $this->repository->destroy($id);
    }
}
