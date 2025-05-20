<?php

declare(strict_types=1);

namespace Modules\Attribute\Actions\AttributeGroup;

use Modules\Attribute\DTO\AttributeGroupDTO;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeGroup;

readonly class CreateAttributeGroupAction
{
    public function execute(AttributeGroupDTO $dto): AttributeGroup
    {
        $group = AttributeGroup::create([
            'name' => $dto->name,
        ]);
        if (!empty($dto->attributes)) {
            Attribute::whereIn('id', $dto->attributes)->update(['attribute_group_id' => $group->id]);
        }
        return $group;
    }
}
