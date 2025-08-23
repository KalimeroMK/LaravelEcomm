<?php

declare(strict_types=1);

namespace Modules\Bundle\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Bundle\DTOs\BundleDTO;
use Modules\Bundle\Repository\BundleRepository;

readonly class UpdateBundleAction
{
    public function __construct(private BundleRepository $repository) {}

    public function execute(BundleDTO $dto): Model
    {
        $bundle = $this->repository->findById($dto->id);

        $bundle->update([
            'name' => $dto->name,
            'description' => $dto->description,
            'price' => $dto->price,
        ]);

        return $bundle;
    }
}
