<?php

declare(strict_types=1);

namespace Modules\Bundle\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Bundle\DTOs\BundleDTO;
use Modules\Bundle\Models\Bundle;
use Modules\Bundle\Repository\BundleRepository;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Throwable;

class CreateBundleAction
{
    private BundleRepository $repository;

    public function __construct(BundleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws Throwable
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function execute(BundleDTO $dto): Bundle
    {
        return DB::transaction(function () use ($dto) {
            /** @var Bundle $bundle */
            $bundle = $this->repository->create([
                'name' => $dto->name,
                'description' => $dto->description,
                'price' => $dto->price,
                'extra' => $dto->extra,
            ]);

            if (! empty($dto->products)) {
                $bundle->products()->sync($dto->products);
            }

            if (! empty($dto->images)) {
                $bundle->addMultipleMediaFromRequest(['images'])
                    ->each(fn ($fileAdder) => $fileAdder->preservingOriginal()->toMediaCollection('bundle'));
            }

            return $bundle;
        });
    }
}
