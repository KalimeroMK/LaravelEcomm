<?php

declare(strict_types=1);

namespace Modules\Bundle\Actions;

use Modules\Bundle\DTOs\BundleDTO;
use Modules\Bundle\Models\Bundle;
use Modules\Bundle\Repository\BundleRepository;

readonly class CreateBundleAction
{
    public function __construct(private BundleRepository $repository) {}

    public function execute(BundleDTO $dto): Bundle
    {
        return $this->repository->create([
            'name' => $dto->name,
            'description' => $dto->description,
            'price' => $dto->price,
            'extra' => $dto->extra,
        ]);
    }
}
