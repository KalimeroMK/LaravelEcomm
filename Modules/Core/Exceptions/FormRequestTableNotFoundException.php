<?php

namespace Modules\Core\Exceptions;

class FormRequestTableNotFoundException extends GeneralException
{
    public $code = 404;
    
    /**
     * @return string
     */
    public function message(): string
    {
        return "Table not found in the form request";
    }
}
