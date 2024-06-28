<?php

namespace Modules\Core\Exceptions;

class GeneralNotFoundException extends GeneralException
{
    /**
     * @var int
     */
    public $code = 404;

    /**
     * @return string|null
     */
    public function message(): ?string
    {
        return "The requested resource was not found in the database";
    }
}
