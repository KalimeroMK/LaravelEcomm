<?php

declare(strict_types=1);

namespace Modules\Core\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface EloquentRepositoryInterface
{
    public function findAll(): Collection;

    public function findById(int $id): ?Model;

    public function findBy(string $column, mixed $value): ?Model;

    public function create(array $data): ?Model;

    public function update(int $id, array $data): ?Model;

    public function destroy(int $id): void;

    public function restore(int $id): ?Model;

    public function findByIdWithTrashed(int $id): ?Model;

    public function createOrUpdate(array $attributes, array $values = []): Model;
}
