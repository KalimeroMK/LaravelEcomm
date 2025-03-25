<?php

declare(strict_types=1);

namespace Modules\Core\Exceptions;

class GeneralUpdateException extends GeneralException
{
    /**
     * @var int
     */
    public $code = 422;

    public function message(): ?string
    {
        return 'Error while updating resource in the database';
    }
}
