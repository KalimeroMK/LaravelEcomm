<?php

namespace Modules\Core\Service;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\Core\Interfaces\RepositoryInterface;

abstract class CoreService
{
    protected RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retrieve all records.
     */
    public function getAll(): Collection
    {
        return $this->repository->findAll();
    }

    /**
     * Retrieve a single entity by its ID.
     */
    public function findById(int $id): ?Model
    {
        return $this->repository->findById($id);
    }

    /**
     * Create a new record.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    /**
     * Update an existing record.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data): Model
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete a record by its ID.
     */
    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }
}
