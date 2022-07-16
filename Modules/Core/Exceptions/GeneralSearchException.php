<?php

namespace App\Modules\Core\Exceptions;

class GeneralSearchException extends GeneralException
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
