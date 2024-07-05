<?php

namespace Modules\Core\Exceptions;

class GeneralStoreException extends GeneralException
{
    /**
     * @var int
     */
    public $code = 422;

    public function message(): ?string
    {
        return 'Error while creating resource in the database';
    }
}
