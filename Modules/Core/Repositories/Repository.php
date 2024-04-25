<?php

namespace Modules\Core\Repositories;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Interfaces\RepositoryInterface;

class Repository implements RepositoryInterface
{
    /**
     * The model class or instance used by the repository.
     * @var Model|string
     */
    public $model;

    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::all();
    }

    /**
     * Find a record by the given column and value.
     *
     * @param  string  $column  The column to query against.
     * @param  mixed  $value  The value to search for in the specified column.
     *
     * @return mixed
     */
    public function findBy(string $column, mixed $value): mixed
    {
        return $this->model::where($column, $value);
    }

    /**
     * Create a new record in the repository.
     *
     * @param  array<string, mixed>  $data  The data for creating the new record.
     *
     * @return mixed The newly created model instance.
     */
    public function create(array $data): mixed
    {
        return $this->model::create($data)->fresh();
    }

    /**
     * Insert a new record into the database.
     *
     * @param  array<string, mixed>  $data  Data to insert, keyed by column names.
     * @return mixed
     */
    public function insert(array $data): mixed
    {
        return $this->model::insert($data);
    }

    /**
     * Update an existing record in the repository.
     *
     * @param  int  $id  The ID of the model to update.
     * @param  array<string, mixed>  $data  The data to update in the model.
     *
     * @return mixed The updated model instance.
     */
    public function update(int $id, array $data): mixed
    {
        $item = $this->findById($id);
        $item->fill($data);
        $item->save();

        return $item->fresh();
    }

    /**
     * @param  int  $id
     *
     * @return mixed
     */
    public function findById(int $id): mixed
    {
        return $this->model::find($id);
    }

    /**
     * @param  int  $id
     *
     * @return void
     */
    public function delete(int $id): void
    {
        $this->model::destroy($id);
    }

    /**
     * @param  int  $id
     *
     * @return mixed
     */
    public function restore(int $id): mixed
    {
        if (!method_exists($this->model, 'isSoftDelete')) {
            return null;
        }

        $object = $this->model->withTrashed()->find($id);
        if (!$object) {
            return null;
        }

        $object->restore();

        return $object;
    }


    public function findByIdWithTrashed(int $id): mixed
    {
        if (!method_exists($this->model, 'isSoftDelete')) {
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
