<?php

declare(strict_types=1);

namespace Modules\Attribute\Repository;

use Modules\Attribute\Models\AttributeGroup;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Repositories\EloquentRepository;

class AttributeGroupRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(AttributeGroup::class);
    }

}
