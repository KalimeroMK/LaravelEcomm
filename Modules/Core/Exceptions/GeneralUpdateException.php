<?php

namespace Modules\Core\Exceptions;

class GeneralUpdateException extends GeneralException
{
    /**
     * @var int
     */
    public $code = 422;

    /**
     * @return string|null
     */
    public function message(): ?string
    {
        return "Error while updating resource in the database";
    }
}
