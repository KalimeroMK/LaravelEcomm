<?php

declare(strict_types=1);

namespace Modules\Bundle\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Bundle\Repository\BundleRepository;

readonly class FindBundleAction
{
    private BundleRepository $repository;

    public function __construct(BundleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): Model
    {
        return $this->repository->findById($id);
    }
}
