<?php

declare(strict_types=1);

namespace Modules\Attribute\Actions;

use Modules\Attribute\DTOs\AttributeDTO;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Repository\AttributeRepository;

readonly class CreateAttributeAction
{
    public function __construct(private AttributeRepository $repository) {}

    public function execute(AttributeDTO $dto): Attribute
    {
        return $this->repository->create([
            'name' => $dto->name,
            'code' => $dto->code,
            'type' => $dto->type,
            'display' => $dto->display,
            'is_filterable' => $dto->is_filterable,
            'is_configurable' => $dto->is_configurable,
            'is_required' => $dto->is_required,
        ]);
    }
}
