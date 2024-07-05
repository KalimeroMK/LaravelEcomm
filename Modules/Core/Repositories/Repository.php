<?php

namespace Modules\Core\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\Core\Interfaces\RepositoryInterface;

class Repository implements RepositoryInterface
{
    /**
     * The model class or instance used by the repository.
     *
     * @var Model|string
     */
    public $model;

    public function findAll(): Collection
    {
        return $this->model::all();
    }

    /**
     * Find a single model by column value.
     *
     * @param  string  $column  Column to filter by.
     * @param  mixed  $value  Value to match in the specified column.
     */
    public function findBy(string $column, mixed $value): ?Model
    {
        return $this->model::where($column, $value)->first();
    }

    /**
     * Create a new record in the repository.
     *
     * @param  array<string, mixed>  $data  The data for creating the new record.
     * @return Model The newly created model instance.
     */
    public function create(array $data): Model
    {
        return $this->model::create($data)->fresh();
    }

    /**
     * Insert a new record into the database.
     *
     * @param  array<string, mixed>  $data  Data to insert, keyed by column names.
     */
    public function insert(array $data): bool
    {
        return $this->model::insert($data);
    }

    /**
     * Update an existing record in the repository.
     *
     * @param  int  $id  The ID of the model to update.
     * @param  array<string, mixed>  $data  The data to update in the model.
     * @return Model The updated model instance.
     */
    public function update(int $id, array $data): Model
    {
        $item = $this->findById($id);
        $item->fill($data);
        $item->save();

        return $item->fresh();
    }

    public function findById(int $id): ?Model
    {
        return $this->model::findOrFail($id);
    }

    public function delete(int $id): void
    {
        $this->model::destroy($id);
    }

    public function restore(int $id): ?Model
    {
        if (! method_exists($this->model, 'isSoftDelete')) {
            return null;
        }

        $object = $this->model->withTrashed()->find($id);
        if (! $object) {
            return null;
        }

        $object->restore();

        return $object;
    }

    public function findByIdWithTrashed(int $id): ?Model
    {
        if (! method_exists($this->model, 'isSoftDelete')) {
            return null;
        }

        if (is_string($this->model)) {
            $modelInstance = new $this->model;
        } else {
            $modelInstance = $this->model;
        }

        return $modelInstance->withTrashed()->find($id);
    }
}
