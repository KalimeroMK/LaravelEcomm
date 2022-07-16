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
    public function findBy(string $column, $value): mixed;
    
    /**
     * @param  array  $data
     *
     * @return mixed
     */
    public function create(array $data): mixed;
    
    /**
     * @param  int  $id
     * @param  array  $data
     *
     * @return mixed
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
