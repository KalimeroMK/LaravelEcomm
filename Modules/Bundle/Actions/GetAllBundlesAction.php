<?php

declare(strict_types=1);

namespace Modules\Bundle\Actions;

use Modules\Bundle\Repository\BundleRepository;

readonly class GetAllBundlesAction
{
    private BundleRepository $repository;

    public function __construct(BundleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): \Illuminate\Support\Collection
    {
        return $this->repository->findAll();
    }
}
