<?php

declare(strict_types=1);

namespace Modules\Attribute\Actions;

use Modules\Attribute\DTO\AttributeDTO;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Repository\AttributeRepository;

readonly class CreateAttributeAction
{
    public function __construct(public AttributeRepository $repository)
    {
    }

    public function execute(AttributeDTO $dto): Attribute
    {
        $attribute = $this->repository->create([
            'name' => $dto->name,
            'code' => $dto->code,
            'type' => $dto->type,
            'display' => $dto->display,
            'filterable' => $dto->filterable,
            'configurable' => $dto->configurable,
        ]);

        // Optionally sync options if needed
        if (!empty($dto->options) && method_exists($attribute, 'syncOptions')) {
            $attribute->syncOptions($dto->options);
        }

        return $attribute;
    }
}
