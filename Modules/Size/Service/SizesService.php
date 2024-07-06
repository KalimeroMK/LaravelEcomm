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
     * @param array<string, mixed> $data
     * @return Model
     */
    public function store(array $data): Model
    {
        return $this->sizes_repository->create($data);
    }

    /**
     * Edit an existing size by ID.
     *
     * @param int $id
     * @return Model|null
     */
    public function edit(int $id): ?Model
    {
        return $this->sizes_repository->findById($id);
    }

    /**
     * Show an existing size by ID.
     *
     * @param int $id
     * @return Model
     */
    public function show(int $id): Model
    {
        return $this->sizes_repository->findById($id);
    }

    /**
     * Update an existing size by ID.
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        return $this->sizes_repository->update($id, $data);
    }

    /**
     * Destroy a size by ID.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->sizes_repository->delete($id);
    }

    /**
     * Get all sizes.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->sizes_repository->findAll();
    }
}
