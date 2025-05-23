<?php

declare(strict_types=1);

namespace Modules\Complaint\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Complaint\Database\Factories\ComplaintFactory;
use Modules\Core\Models\Core;
use Modules\Order\Models\Order;
use Modules\User\Models\User;

/**
 * Class Complaint
 *
 * @property int $id
 * @property int $user_id
 * @property int $order_id
 * @property string $status
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection<int, ComplaintReply> $complaint_replies
 * @property-read int|null                        $complaint_replies_count
 * @property-read Order                           $order
 * @property-read User                            $user
 *
 * @method static Builder<static>|Complaint newModelQuery()
 * @method static Builder<static>|Complaint newQuery()
 * @method static Builder<static>|Complaint query()
 * @method static Builder<static>|Complaint whereCreatedAt($value)
 * @method static Builder<static>|Complaint whereDescription($value)
 * @method static Builder<static>|Complaint whereId($value)
 * @method static Builder<static>|Complaint whereOrderId($value)
 * @method static Builder<static>|Complaint whereStatus($value)
 * @method static Builder<static>|Complaint whereUpdatedAt($value)
 * @method static Builder<static>|Complaint whereUserId($value)
 *
 * @mixin Eloquent
 */
class Complaint extends Core
{
    use HasFactory;

    protected $table = 'complaints';

    protected $casts
        = [
            'user_id' => 'int',
            'order_id' => 'int',
        ];

    protected $fillable
        = [
            'user_id',
            'order_id',
            'status',
            'description',
        ];

    public static function Factory(): ComplaintFactory
    {
        return ComplaintFactory::new();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function complaint_replies(): HasMany
    {
        return $this->hasMany(ComplaintReply::class);
    }
}
