<?php

declare(strict_types=1);

namespace Modules\Core\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class Repository
{
    protected Model $model;

    /**
     * Initialize the repository.
     */
    public function __construct()
    {
        $this->model = $this->getModel();
    }

    /**
     * Get the model instance.
     */
    abstract public function getModel(): Model;

    /**
     * Get all records.
     */
    final public function all(array $columns = ['*']): Collection
    {
        return $this->model->all($columns);
    }

    /**
     * Find a record by ID.
     */
    final public function find(int $id, array $columns = ['*']): ?Model
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Find a record by ID or fail.
     */
    final public function findOrFail(int $id, array $columns = ['*']): Model
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * Find records by criteria.
     */
    final public function findBy(string $field, mixed $value, array $columns = ['*']): Collection
    {
        return $this->model->where($field, $value)->get($columns);
    }

    /**
     * Find first record by criteria.
     */
    final public function findOneBy(string $field, mixed $value, array $columns = ['*']): ?Model
    {
        return $this->model->where($field, $value)->first($columns);
    }

    /**
     * Create a new record.
     */
    final public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update a record.
     */
    final public function update(int $id, array $data): bool
    {
        $record = $this->findOrFail($id);

        return $record->update($data);
    }

    /**
     * Delete a record.
     */
    final public function delete(int $id): bool
    {
        $record = $this->findOrFail($id);

        return $record->delete();
    }

    /**
     * Get paginated results.
     */
    final public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * Get query builder.
     */
    final public function query(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Count records.
     */
    final public function count(): int
    {
        return $this->model->count();
    }

    /**
     * Check if record exists.
     */
    final public function exists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * Get records with relationships.
     */
    final public function with(array $relations): Builder
    {
        return $this->model->with($relations);
    }

    /**
     * Get records where field is in array.
     */
    final public function whereIn(string $field, array $values, array $columns = ['*']): Collection
    {
        return $this->model->whereIn($field, $values)->get($columns);
    }

    /**
     * Get records where field is not in array.
     */
    final public function whereNotIn(string $field, array $values, array $columns = ['*']): Collection
    {
        return $this->model->whereNotIn($field, $values)->get($columns);
    }

    /**
     * Get records between two values.
     */
    final public function whereBetween(string $field, array $values, array $columns = ['*']): Collection
    {
        return $this->model->whereBetween($field, $values)->get($columns);
    }

    /**
     * Get records ordered by field.
     */
    final public function orderBy(string $field, string $direction = 'asc'): Builder
    {
        return $this->model->orderBy($field, $direction);
    }
}
