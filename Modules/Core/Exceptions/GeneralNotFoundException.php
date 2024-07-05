<?php

namespace Modules\Core\Exceptions;

class GeneralNotFoundException extends GeneralException
{
    /**
     * @var int
     */
    public $code = 404;

    public function message(): ?string
    {
        return 'The requested resource was not found in the database';
    }
}
