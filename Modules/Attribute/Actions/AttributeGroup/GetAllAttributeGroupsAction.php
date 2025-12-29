<?php

declare(strict_types=1);

namespace Modules\Attribute\Actions\AttributeGroup;

use Illuminate\Support\Collection;
use Modules\Attribute\Models\AttributeGroup;
use Modules\Attribute\Repository\AttributeGroupRepository;

readonly class GetAllAttributeGroupsAction
{
    public function __construct(private AttributeGroupRepository $repository) {}

    /**
     * @return Collection<int, AttributeGroup>
     */
    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
