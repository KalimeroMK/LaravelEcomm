<?php

declare(strict_types=1);

namespace Modules\Attribute\Actions\AttributeGroup;

use Illuminate\Database\Eloquent\Model;
use Modules\Attribute\DTOs\AttributeGroupDTO;
use Modules\Attribute\Repository\AttributeGroupRepository;

readonly class UpdateAttributeGroupAction
{
    private AttributeGroupRepository $repository;

    public function __construct(AttributeGroupRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(AttributeGroupDTO $dto): Model
    {
        $group = $this->repository->findById($dto->id);
        $group->update([
            'name' => $dto->name,
        ]);
        // Sync attributes for this group via pivot table
        $group->attributes()->sync($dto->attributes ?? []);

        return $group;
    }
}
