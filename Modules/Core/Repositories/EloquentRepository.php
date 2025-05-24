<?php

declare(strict_types=1);

namespace Modules\Core\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Modules\Core\Interfaces\EloquentRepositoryInterface;

class EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct(public string $modelClass)
    {
    }

    public function findAll(): Collection
    {
        return (new $this->modelClass)->all();
    }

    public function findById(int $id): ?Model
    {
        return $this->modelClass::findOrFail($id);
    }

    public function findBy(string $column, mixed $value): ?Model
    {
        return (new $this->modelClass)->where($column, $value)->first();
    }

    public function create(array $data): Model
    {
        return $this->modelClass::create($data)->fresh();
    }

    public function insert(array $data): bool
    {
        return $this->modelClass::insert($data);
    }

    public function update(int $id, array $data): Model
    {
        $item = $this->findById($id);
        $item->fill($data)->save();

        return $item->fresh();
    }

    public function destroy(int $id): void
    {
        $this->modelClass::destroy($id);
    }

    public function createOrUpdate(array $attributes, array $values = []): Model
    {
        return (new $this->modelClass)->updateOrCreate($attributes, $values);
    }

    public function restore(int $id): ?Model
    {
        if (!$this->usesSoftDeletes()) {
            return null;
        }

        $object = (new $this->modelClass)->withTrashed()->find($id);
        if (!$object) {
            return null;
        }

        $object->restore();

        return $object;
    }

    public function findByIdWithTrashed(int $id): ?Model
    {
        if (!$this->usesSoftDeletes()) {
            return null;
        }

        return (new $this->modelClass)->withTrashed()->find($id);
    }

    private function usesSoftDeletes(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this->modelClass));
    }
}
