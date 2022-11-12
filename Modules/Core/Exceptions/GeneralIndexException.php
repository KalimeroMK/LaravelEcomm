<?php

namespace Modules\Core\Exceptions;

class GeneralIndexException extends GeneralException
{
    public $code = 500;
    
    /**
     * @return string|null
     */
    public function message(): ?string
    {
        return "Something went wrong while getting data from database";
    }
}
