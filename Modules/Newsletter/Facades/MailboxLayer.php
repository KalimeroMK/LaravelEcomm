<?php

declare(strict_types=1);

namespace Modules\Newsletter\Facades;

use Illuminate\Support\Facades\Facade;

class MailboxLayer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'mailboxlayer';
    }
}
