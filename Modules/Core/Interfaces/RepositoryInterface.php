<?php

namespace Modules\Core\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface RepositoryInterface
{
    public function findAll(): Collection;

    public function findById(int $id): ?Model;

    public function findBy(string $column, mixed $value): ?Model;

    /**
     * Creates a new entity with the provided data.
     *
     * @param  array<string, mixed>  $data  Key-value pairs representing the entity's attributes. For example:
     *                                      - 'name': string
     *                                      - 'email': string
     *                                      - 'age': int, etc.
     * @return Model|null Newly created entity instance.
     */
    public function create(array $data): ?Model;

    /**
     * Updates an existing entity identified by ID with the provided data.
     *
     * @param  int  $id  The entity's identifier.
     * @param  array<string, mixed>  $data  Key-value pairs representing the entity's attributes that need updating. For example:
     *                                      - 'name': string
     *                                      - 'email': string
     *                                      - 'age': int, etc.
     * @return Model|null Updated entity instance.
     */
    public function update(int $id, array $data): ?Model;

    public function delete(int $id): void;

    public function restore(int $id): ?Model;

    public function findByIdWithTrashed(int $id): ?Model;
}
