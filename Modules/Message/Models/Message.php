<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Message\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Modules\Core\Models\Core;
use Modules\Message\Database\Factories\MessageFactory;

/**
 * Class Message
 *
 * @property int $id
 * @property string $name
 * @property string $subject
 * @property string $email
 * @property string|null $photo
 * @property string|null $phone
 * @property string $message
 * @property string|null $read_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|Message newModelQuery()
 * @method static Builder<static>|Message newQuery()
 * @method static Builder<static>|Message query()
 * @method static Builder<static>|Message whereCreatedAt($value)
 * @method static Builder<static>|Message whereEmail($value)
 * @method static Builder<static>|Message whereId($value)
 * @method static Builder<static>|Message whereMessage($value)
 * @method static Builder<static>|Message whereName($value)
 * @method static Builder<static>|Message wherePhone($value)
 * @method static Builder<static>|Message wherePhoto($value)
 * @method static Builder<static>|Message whereReadAt($value)
 * @method static Builder<static>|Message whereSubject($value)
 * @method static Builder<static>|Message whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Message extends Core
{
    use HasFactory;

    protected $table = 'messages';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var string[]
     */
    protected array $dates
        = [
            'read_at',
        ];

    protected $fillable
        = [
            'name',
            'subject',
            'email',
            'photo',
            'phone',
            'message',
            'read_at',
        ];

    public static function Factory(): MessageFactory
    {
        return MessageFactory::new();
    }
}
