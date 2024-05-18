<?php

namespace Modules\Core\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface RepositoryInterface
{
    /**
     * @return Collection
     */
    public function findAll(): Collection;

    /**
     * @param  int  $id
     *
     * @return Model|null
     */
    public function findById(int $id): ?Model;

    /**
     * @param  string  $column
     * @param  mixed  $value
     * @return Model|null
     */
    public function findBy(string $column, mixed $value): ?Model;

    /**
     * Creates a new entity with the provided data.
     *
     * @param  array<string, mixed>  $data  Key-value pairs representing the entity's attributes. For example:
     *                                   - 'name': string
     *                                   - 'email': string
     *                                   - 'age': int, etc.
     *
     * @return Model|null Newly created entity instance.
     */
    public function create(array $data): ?Model;

    /**
     * Updates an existing entity identified by ID with the provided data.
     *
     * @param  int  $id  The entity's identifier.
     * @param  array<string, mixed>  $data  Key-value pairs representing the entity's attributes that need updating. For example:
     *                                   - 'name': string
     *                                   - 'email': string
     *                                   - 'age': int, etc.
     *
     * @return Model|null Updated entity instance.
     */
    public function update(int $id, array $data): ?Model;

    /**
     * @param  int  $id
     *
     * @return void
     */
    public function delete(int $id): void;

    /**
     * @param  int  $id
     *
     * @return Model|null
     */
    public function restore(int $id): ?Model;

    /**
     * @param  int  $id
     *
     * @return Model|null
     */
    public function findByIdWithTrashed(int $id): ?Model;
}
