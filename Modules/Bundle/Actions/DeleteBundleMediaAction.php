<?php

declare(strict_types=1);

namespace Modules\Bundle\Actions;

use Modules\Bundle\Repository\BundleRepository;

readonly class DeleteBundleMediaAction
{
    private BundleRepository $repository;

    public function __construct(BundleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $bundleId, int $mediaId): void
    {
        $bundle = $this->repository->findById($bundleId);
        $media = $bundle->media()->where('id', $mediaId)->firstOrFail();
        $media->delete();
    }
}
