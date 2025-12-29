<?php

declare(strict_types=1);

namespace Modules\Attribute\Actions\AttributeGroup;

use Modules\Attribute\Models\AttributeGroup;
use Modules\Attribute\Repository\AttributeGroupRepository;

readonly class FindAttributeGroupAction
{
    public function __construct(private AttributeGroupRepository $repository) {}

    public function execute(int $id): AttributeGroup
    {
        /** @var AttributeGroup */
        return $this->repository->findById($id);
    }
}
