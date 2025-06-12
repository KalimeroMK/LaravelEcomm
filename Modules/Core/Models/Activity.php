<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use jeremykenedy\LaravelLogger\App\Models\Activity as BaseActivity;

class Activity extends BaseActivity
{
    protected $table = 'laravel_logger_activity';

    protected $fillable = [
        'description',
        'subject_id',
        'subject_type',
        'user_id',
        'route',
        'ip_address',
        'method_type',
        'user_agent',
        'locale',
        'referer',
        'updated_at',
        'created_at',
        'properties',
        'type',
        'event',
        'batch_uuid',
        'details',
        'userType',
        'userId',
        'ipAddress',
        'userAgent',
        'methodType',
        'relId',
        'relModel',
    ];
}
