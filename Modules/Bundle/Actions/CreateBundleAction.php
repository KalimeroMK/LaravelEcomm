<?php

declare(strict_types=1);

namespace Modules\Bundle\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Bundle\DTOs\BundleDTO;
use Modules\Bundle\Models\Bundle;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Throwable;

readonly class CreateBundleAction
{
    /**
     * @throws Throwable
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function execute(BundleDTO $dto): Bundle
    {
        return DB::transaction(function () use ($dto) {
            $bundle = Bundle::create([
                'name' => $dto->name,
                'description' => $dto->description,
                'price' => $dto->price,
                'extra' => $dto->extra,
            ]);

            if (! empty($dto->products)) {
                $bundle->products()->sync($dto->products);
            }

            if (! empty($dto->images)) {
                foreach ($dto->images as $image) {
                    $bundle
                        ->addMedia($image)
                        ->preservingOriginal()
                        ->toMediaCollection('bundle');
                }
            }

            return $bundle;
        });
    }
}
