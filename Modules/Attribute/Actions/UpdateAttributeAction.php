<?php

declare(strict_types=1);

namespace Modules\Attribute\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Attribute\DTO\AttributeDTO;
use Modules\Attribute\Repository\AttributeRepository;

readonly class UpdateAttributeAction
{
    public function __construct(private AttributeRepository $repository)
    {
    }

    public function execute(AttributeDTO $dto): Model
    {
        $attribute = $this->repository->findById($dto->id);
        $attribute->update([
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
