<?php

declare(strict_types=1);

namespace Modules\Attribute\Actions\AttributeGroup;

use Modules\Attribute\DTO\AttributeGroupDTO;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeGroup;

readonly class UpdateAttributeGroupAction
{
    public function execute(AttributeGroupDTO $dto): AttributeGroup
    {
        $group = AttributeGroup::findOrFail($dto->id);
        $group->update([
            'name' => $dto->name,
        ]);
        // Unassign all attributes from this group
        Attribute::where('attribute_group_id', $group->id)->update(['attribute_group_id' => null]);
        // Assign selected attributes
        if (!empty($dto->attributes)) {
            Attribute::whereIn('id', $dto->attributes)->update(['attribute_group_id' => $group->id]);
        }
        return $group;
    }
}
