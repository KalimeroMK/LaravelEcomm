<?php

namespace Modules\Size\Service;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\Core\Service\CoreService;
use Modules\Size\Repository\SizesRepository;

class SizesService extends CoreService
{
    public SizesRepository $sizes_repository;

    public function __construct(SizesRepository $sizes_repository)
    {
        parent::__construct($sizes_repository);
        $this->sizes_repository = $sizes_repository;
    }

    /**
     * Store a new size.
     *
     * @param  array<string, mixed>  $data
     */
    public function store(array $data): Model
    {
        return $this->sizes_repository->create($data);
    }

    /**
     * Edit an existing size by ID.
     */
    public function edit(int $id): ?Model
    {
        return $this->sizes_repository->findById($id);
    }

    /**
     * Show an existing size by ID.
     */
    public function show(int $id): Model
    {
        return $this->sizes_repository->findById($id);
    }

    /**
     * Update an existing size by ID.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data): Model
    {
        return $this->sizes_repository->update($id, $data);
    }

    /**
     * Destroy a size by ID.
     */
    public function destroy(int $id): void
    {
        $this->sizes_repository->delete($id);
    }

    /**
     * Get all sizes.
     */
    public function getAll(): Collection
    {
        return $this->sizes_repository->findAll();
    }
}
