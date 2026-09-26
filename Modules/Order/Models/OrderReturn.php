<?php

declare(strict_types=1);

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Modules\User\Models\User;

/**
 * A customer's return/refund request for one order (RMA).
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $user_id
 * @property string $reason
 * @property string $status
 * @property string|null $admin_note
 * @property float|null $refund_amount
 * @property string|null $refund_reference
 * @property Carbon|null $refunded_at
 */
class OrderReturn extends Model
{
    public const STATUS_REQUESTED = 'requested';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'order_id',
        'user_id',
        'reason',
        'status',
        'admin_note',
        'refund_amount',
        'refund_reference',
        'refunded_at',
    ];

    protected $casts = [
        'refund_amount' => 'float',
        'refunded_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOpen(): bool
    {
        return in_array($this->status, [self::STATUS_REQUESTED, self::STATUS_APPROVED], true);
    }
}
