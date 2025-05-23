<?php

declare(strict_types=1);

namespace Modules\Attribute\Actions\AttributeGroup;

use Modules\Attribute\DTOs\AttributeGroupDTO;
use Modules\Attribute\Models\AttributeGroup;

readonly class UpdateAttributeGroupAction
{
    public function execute(AttributeGroupDTO $dto): AttributeGroup
    {
        $group = AttributeGroup::findOrFail($dto->id);
        $group->update([
            'name' => $dto->name,
        ]);
        // Sync attributes for this group via pivot table
        $group->attributes()->sync($dto->attributes ?? []);

        return $group;
    }
}
