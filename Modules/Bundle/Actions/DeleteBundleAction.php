<?php

declare(strict_types=1);

namespace Modules\Bundle\Actions;

use Modules\Bundle\Repository\BundleRepository;

readonly class DeleteBundleAction
{
    public function __construct(private BundleRepository $repository) {}

    public function execute(int $id)
    {
        $this->repository->destroy($id);

        return response()->json();
    }
}
