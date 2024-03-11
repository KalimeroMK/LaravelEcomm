<?php

namespace Modules\Core\Repositories;

use Modules\Core\Interfaces\RepositoryInterface;

class Repository implements RepositoryInterface
{
    /**
     * Model::class
     */
    public $model;

    /**
     * @var array
     */
    public array $filters = [];

    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::all();
    }

    /**
     * @param  string  $column
     * @param $value
     *
     * @return mixed
     */
    public function findBy(string $column, $value): mixed
    {
        return $this->model::where($column, $value);
    }

    /**
     * @param  array  $data
     *
     * @return mixed
     */
    public function create(array $data): mixed
    {
        return $this->model::create($data)->fresh();
    }

    /**
     * @param  array  $data
     *
     * @return mixed
     */
    public function insert(array $data): mixed
    {
        return $this->model::insert($data);
    }

    /**
     * @param  $id
     * @param  array  $data
     *
     * @return mixed
     */
    public function update($id, array $data): mixed
    {
        $item = $this->findById($id);
        $item->fill($data);
        $item->save();
        $item->products()->sync($data['product_id']);

        return $item->fresh();
    }

    /**
     * @param  $id
     *
     * @return mixed
     */
    public function findById($id): mixed
    {
        return $this->model::find($id);
    }

    /**
     * @param  $id
     *
     * @return void
     */
    public function delete($id): void
    {
        $this->model::destroy($id);
    }

    /**
     * @param  $id
     *
     * @return mixed
     */
    public function restore($id): mixed
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

    /**
     * @param  $id
     *
     * @return mixed
     */
    public function findByIdWithTrashed($id): mixed
    {
        if (!method_exists($this->model, 'isSoftDelete')) {
            return null;
        }

        return $this->model->withTrashed()->find($id);
    }
}
