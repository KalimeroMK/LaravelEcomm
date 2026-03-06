<?php

declare(strict_types=1);

namespace Modules\Coupon\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\Core;
use Modules\Order\Models\Order;
use Modules\User\Models\User;

/**
 * Class CouponUsage
 *
 * Tracks individual coupon usage per user/order
 *
 * @property int $id
 * @property int $coupon_id
 * @property int|null $user_id
 * @property int|null $order_id
 * @property string|null $session_id
 * @property float $discount_amount
 * @property \Illuminate\Support\Carbon $used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Coupon $coupon
 * @property-read User|null $user
 * @property-read Order|null $order
 *
 * @method static Builder|CouponUsage forCoupon(int $couponId)
 * @method static Builder|CouponUsage forUser(int $userId)
 * @method static Builder|CouponUsage forSession(string $sessionId)
 * @method static int countUsageForCoupon(int $couponId)
 * @method static int countUsageForUser(int $couponId, int $userId)
 */
class CouponUsage extends Core
{
    use HasFactory;

    protected $table = 'coupon_usage';

    protected $casts = [
        'discount_amount' => 'float',
        'used_at' => 'datetime',
    ];

    protected $fillable = [
        'coupon_id',
        'user_id',
        'order_id',
        'session_id',
        'discount_amount',
        'used_at',
    ];

    /**
     * Get the coupon associated with this usage.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the user who used the coupon.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order where coupon was applied.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope: Filter by coupon ID
     */
    public function scopeForCoupon(Builder $query, int $couponId): Builder
    {
        return $query->where('coupon_id', $couponId);
    }

    /**
     * Scope: Filter by user ID
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Filter by session ID
     */
    public function scopeForSession(Builder $query, string $sessionId): Builder
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Count total usage for a coupon
     */
    public static function countUsageForCoupon(int $couponId): int
    {
        return static::forCoupon($couponId)->count();
    }

    /**
     * Count usage for a specific user and coupon
     */
    public static function countUsageForUser(int $couponId, int $userId): int
    {
        return static::forCoupon($couponId)->forUser($userId)->count();
    }

    /**
     * Count usage for a session
     */
    public static function countUsageForSession(int $couponId, string $sessionId): int
    {
        return static::forCoupon($couponId)->forSession($sessionId)->count();
    }

    /**
     * Record coupon usage
     */
    public static function recordUsage(
        int $couponId,
        ?int $userId,
        ?int $orderId,
        ?string $sessionId,
        float $discountAmount
    ): self {
        return static::create([
            'coupon_id' => $couponId,
            'user_id' => $userId,
            'order_id' => $orderId,
            'session_id' => $sessionId,
            'discount_amount' => $discountAmount,
            'used_at' => now(),
        ]);
    }
}
