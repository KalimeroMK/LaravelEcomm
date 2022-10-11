<?php

namespace Modules\Core\Interfaces;

/**
 * Interface SearchInterface
 * @package App\Modules\Core
 */
interface SearchInterface
{
    /**
     * @param  array  $data
     *
     * @return mixed
     */
    public function search(array $data);
}
