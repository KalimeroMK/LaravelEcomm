<?php

declare(strict_types=1);

namespace Modules\Banner\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Banner\DTO\BannerDTO;
use Modules\Banner\Repository\BannerRepository;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\FileAdder;

readonly class UpdateBannerAction
{
    private BannerRepository $repository;

    public function __construct(BannerRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function execute(BannerDTO $dto): Model
    {
        $banner = $this->repository->findById($dto->id);

        $banner->update([
            'title' => $dto->title,
            'slug' => $dto->slug ?? $banner->slug,
            'description' => $dto->description,
            'status' => $dto->status,
        ]);

        if (!empty($dto->images)) {
            $banner->clearMediaCollection('banner');
            $banner->addMultipleMediaFromRequest(['images'])
                ->each(fn(FileAdder $fileAdder) => $fileAdder->preservingOriginal()->toMediaCollection('banner'));
        }

        return $banner;
    }
}
