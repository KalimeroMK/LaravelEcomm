<?php

declare(strict_types=1);

namespace Modules\Attribute\Actions\AttributeGroup;

use Modules\Attribute\DTOs\AttributeGroupDTO;
use Modules\Attribute\Models\AttributeGroup;
use Modules\Attribute\Repository\AttributeGroupRepository;

readonly class CreateAttributeGroupAction
{
    private AttributeGroupRepository $repository;

    public function __construct(AttributeGroupRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(AttributeGroupDTO $dto): AttributeGroup
    {
        $group = $this->repository->create([
            'name' => $dto->name,
        ]);
        if ($dto->attributes !== null && $dto->attributes !== []) {
            $group->attributes()->sync($dto->attributes);
        }

        return $group;
    }
}
