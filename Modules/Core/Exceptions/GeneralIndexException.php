<?php

declare(strict_types=1);

namespace Modules\Core\Exceptions;

class GeneralIndexException extends GeneralException
{
    /**
     * @var int
     */
    public $code = 500;

    public function message(): ?string
    {
        return 'Something went wrong while getting data from database';
    }
}
