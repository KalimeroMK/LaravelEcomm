<?php

declare(strict_types=1);

namespace Modules\Banner\Actions;

use Illuminate\Http\JsonResponse;
use Modules\Banner\Repository\BannerRepository;

readonly class DeleteBannerAction
{
    private BannerRepository $repository;

    public function __construct(BannerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): JsonResponse
    {
        $this->repository->destroy($id);

        return response()->json();
    }
}
