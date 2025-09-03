<?php

declare(strict_types=1);

namespace Modules\Bundle\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\Bundle\Models\Bundle;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Models\Core;
use Modules\Core\Repositories\EloquentRepository;

class BundleRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Bundle::class);
    }

    /**
     * Get all bundles with related products and media.
     */
    public function findAll(): Collection
    {
        return (new $this->modelClass)->with(['products', 'media'])->get();
    }

    /**
     * Get bundle by ID with media relationship.
     */
    public function findById(int $id): ?Model
    {
        return (new $this->modelClass)->with('media')->findOrFail($id);
    }

    /**
     * Get bundle by slug with media and products.
     */
    public function findBySlug(string $slug): Core|Bundle
    {
        return (new $this->modelClass)->with(['media', 'products'])->where('slug', $slug)->first();
    }
}
