<?php

declare(strict_types=1);

namespace Modules\Attribute\Actions\AttributeGroup;

use Modules\Attribute\Models\AttributeGroup;

readonly class DeleteAttributeGroupAction
{
    public function execute(int $id): bool
    {
        $group = AttributeGroup::findOrFail($id);
        // Optionally unassign attributes
        $group->attributes()->update(['attribute_group_id' => null]);
        return (bool)$group->delete();
    }
}
