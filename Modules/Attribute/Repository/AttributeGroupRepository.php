<?php

declare(strict_types=1);

namespace Modules\Attribute\Repository;

use Modules\Attribute\Models\AttributeGroup;
use Modules\Core\Repositories\Repository;

class AttributeGroupRepository extends Repository
{
    /**
     * The model instance.
     *
     * @var string
     */
    public $model = AttributeGroup::class;
}
