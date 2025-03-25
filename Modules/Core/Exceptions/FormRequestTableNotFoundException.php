<?php

declare(strict_types=1);

namespace Modules\Core\Exceptions;

class FormRequestTableNotFoundException extends GeneralException
{
    /**
     * The HTTP status code for the exception.
     *
     * @var int
     */
    public $code = 404;

    /**
     * Returns the error message for the exception.
     */
    public function message(): string
    {
        return 'Table not found in the form request';
    }
}
