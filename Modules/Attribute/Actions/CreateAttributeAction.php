<?php

declare(strict_types=1);

namespace Modules\Attribute\Actions;

use Modules\Attribute\DTOs\AttributeDTO;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Repository\AttributeRepository;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\FileAdder;

readonly class CreateAttributeAction
{
    private AttributeRepository $repository;

    public function __construct(AttributeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function execute(AttributeDTO $dto): Attribute
    {
        /** @var Attribute $attribute */
        $attribute = $this->repository->create([
            'name' => $dto->name,
            'code' => $dto->code,
            'type' => $dto->type,
            'display' => $dto->display,
            'is_filterable' => $dto->is_filterable,
            'is_configurable' => $dto->is_configurable,
            'is_required' => $dto->is_required,
            'status' => $dto->status ?? null,
        ]);

        if (!empty($dto->options) && method_exists($attribute, 'syncOptions')) {
            $attribute->syncOptions($dto->options);
        }

        if (!empty($dto->images)) {
            $attribute->addMultipleMediaFromRequest(['images'])
                ->each(fn(FileAdder $fileAdder) => $fileAdder->preservingOriginal()->toMediaCollection('attribute'));
        }

        return $attribute;
    }
}
