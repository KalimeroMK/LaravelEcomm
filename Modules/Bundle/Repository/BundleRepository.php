<?php

namespace Modules\Bundle\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\Bundle\Models\Bundle;
use Modules\Core\Models\Core;
use Modules\Core\Repositories\Repository;

class BundleRepository extends Repository
{
    /**
     * The model instance.
     *
     * @var string
     */
    public $model = Bundle::class;

    public function findAll(): Collection
    {
        return $this->model::with(['products', 'media'])->get();
    }

    public function findById(int $id): ?Model
    {
        return $this->model::with('media')->findOrFail($id);
    }

    public function findBySlug(string $slug): Core|Bundle
    {
        return $this->model::with('media')->where('slug', $slug)->first();
    }
}
