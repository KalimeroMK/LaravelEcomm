<?php

declare(strict_types=1);

namespace Modules\Attribute\Actions\AttributeGroup;

use Modules\Attribute\DTOs\AttributeGroupDTO;
use Modules\Attribute\Models\AttributeGroup;

readonly class CreateAttributeGroupAction
{
    public function execute(AttributeGroupDTO $dto): AttributeGroup
    {
        $group = AttributeGroup::create([
            'name' => $dto->name,
        ]);
        if (! empty($dto->attributes)) {
            $group->attributes()->sync($dto->attributes);
        }

        return $group;
    }
}
