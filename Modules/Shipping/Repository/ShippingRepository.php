<?php

declare(strict_types=1);

namespace Modules\Shipping\Repository;

use Illuminate\Support\Collection;
use Modules\Core\Repositories\Repository;
use Modules\Shipping\Models\Shipping;

class ShippingRepository extends Repository
{
    public $model = Shipping::class;

    public function findAll(): Collection
    {
        return $this->model::orderBy('id', 'DESC')->get();
    }
}
