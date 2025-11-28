<?php

declare(strict_types=1);

namespace Modules\Attribute\Actions;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Modules\Attribute\DTOs\AttributeDTO;
use Modules\Attribute\Repository\AttributeRepository;

readonly class UpdateAttributeAction
{
    public function __construct(private AttributeRepository $repository) {}

    public function execute(AttributeDTO $dto): Model
    {
        $attribute = $this->repository->findById($dto->id ?? 0);

        if ($attribute === null) {
            throw new InvalidArgumentException('Attribute not found');
        }

        $attribute->update([
            'name' => $dto->name,
            'code' => $dto->code,
            'type' => $dto->type,
            'display' => $dto->display,
            'is_filterable' => $dto->is_filterable,
            'is_configurable' => $dto->is_configurable,
            'is_required' => $dto->is_required,
        ]);

        return $attribute;
    }
}
