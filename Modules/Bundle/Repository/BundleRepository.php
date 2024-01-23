<?php

namespace Modules\Bundle\Repository;

use Modules\Bundle\Models\Bundle;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\Repository;

class BundleRepository extends Repository implements SearchInterface
{
    public $model = Bundle::class;

    public function search(array $data)
    {
        // TODO: Implement search() method.
    }

    public function findAll(): mixed

    {
        return $this->model::with('products')->get();
    }
}