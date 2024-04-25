<?php

namespace Modules\Core\Interfaces;

interface RepositoryInterface
{
    /**
     * @return mixed
     */
    public function findAll(): mixed;

    /**
     * @param  int  $id
     *
     * @return mixed
     */
    public function findById(int $id): mixed;

    /**
     * @param  string  $column
     * @param $value
     *
     * @return mixed
     */
    public function findBy(string $column, mixed $value): mixed;

    /**
     * Creates a new entity with the provided data.
     *
     * @param  array<string, mixed>  $data  Key-value pairs representing the entity's attributes. For example:
     *                                   - 'name': string
     *                                   - 'email': string
     *                                   - 'age': int, etc.
     *
     * @return mixed Newly created entity instance.
     */
    public function create(array $data): mixed;

    /**
     * Updates an existing entity identified by ID with the provided data.
     *
     * @param  int  $id  The entity's identifier.
     * @param  array<string, mixed>  $data  Key-value pairs representing the entity's attributes that need updating. For example:
     *                                   - 'name': string
     *                                   - 'email': string
     *                                   - 'age': int, etc.
     *
     * @return mixed Updated entity instance.
     */
    public function update(int $id, array $data): mixed;

    /**
     * @param  int  $id
     *
     * @return void
     */
    public function delete(int $id): void;

    /**
     * @param  int  $id
     *
     * @return mixed
     */
    public function restore(int $id): mixed;

    /**
     * @param  int  $id
     *
     * @return mixed
     */
    public function findByIdWithTrashed(int $id): mixed;
}
