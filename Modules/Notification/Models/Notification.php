<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Notification\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Models\Core;
use Modules\Notification\Database\Factories\NotificationFactory;

/**
 * Class Notification
 *
 * @property int $id
 * @property string $type
 * @property string $notifiable_type
 * @property int $notifiable_id
 * @property string $data
 * @property string|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static Builder<static>|Notification newModelQuery()
 * @method static Builder<static>|Notification newQuery()
 * @method static Builder<static>|Notification query()
 * @method static Builder<static>|Notification whereCreatedAt($value)
 * @method static Builder<static>|Notification whereData($value)
 * @method static Builder<static>|Notification whereId($value)
 * @method static Builder<static>|Notification whereNotifiableId($value)
 * @method static Builder<static>|Notification whereNotifiableType($value)
 * @method static Builder<static>|Notification whereReadAt($value)
 * @method static Builder<static>|Notification whereType($value)
 * @method static Builder<static>|Notification whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Notification extends Core
{
    protected $table = 'notifications';

    protected $casts
        = [
            'notifiable_id' => 'int',
        ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<int, string>
     */
    protected $dates
        = [
            'read_at',
            'created_at',
            'updated_at',
        ];

    protected $fillable
        = [
            'type',
            'notifiable_type',
            'notifiable_id',
            'data',
            'read_at',
        ];

    public static function Factory(): NotificationFactory
    {
        return NotificationFactory::new();
    }
}
