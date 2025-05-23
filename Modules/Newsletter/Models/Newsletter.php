<?php

declare(strict_types=1);

namespace Modules\Newsletter\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\Core;
use Modules\Newsletter\Database\Factories\NewsletterFactory;

/**
 * Class Newsletter
 *
 * @property int $id
 * @property string $email
 * @property string|null $token
 * @property bool $is_validated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static Builder<static>|Newsletter newModelQuery()
 * @method static Builder<static>|Newsletter newQuery()
 * @method static Builder<static>|Newsletter query()
 * @method static Builder<static>|Newsletter whereCreatedAt($value)
 * @method static Builder<static>|Newsletter whereEmail($value)
 * @method static Builder<static>|Newsletter whereId($value)
 * @method static Builder<static>|Newsletter whereIsValidated($value)
 * @method static Builder<static>|Newsletter whereToken($value)
 * @method static Builder<static>|Newsletter whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Newsletter extends Core
{
    use HasFactory;

    protected $table = 'newsletters';

    protected $casts = [
        'is_validated' => 'bool',
    ];

    protected $hidden = [
        'token',
    ];

    protected $fillable = [
        'email',
        'token',
        'is_validated',
    ];

    public static function Factory(): NewsletterFactory
    {
        return NewsletterFactory::new();
    }
}
