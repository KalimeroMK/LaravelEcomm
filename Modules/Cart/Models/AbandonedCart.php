<?php

declare(strict_types=1);

namespace Modules\Cart\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Modules\Core\Models\Core;
use Modules\User\Models\User;

/**
 * Class AbandonedCart
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $session_id
 * @property string|null $email
 * @property array $cart_data
 * @property float $total_amount
 * @property int $total_items
 * @property Carbon $last_activity
 * @property Carbon|null $first_email_sent
 * @property Carbon|null $second_email_sent
 * @property Carbon|null $third_email_sent
 * @property bool $converted
 * @property Carbon|null $converted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 *
 * @method static Builder<static>|AbandonedCart newModelQuery()
 * @method static Builder<static>|AbandonedCart newQuery()
 * @method static Builder<static>|AbandonedCart query()
 * @method static Builder<static>|AbandonedCart whereId($value)
 * @method static Builder<static>|AbandonedCart whereUserId($value)
 * @method static Builder<static>|AbandonedCart whereSessionId($value)
 * @method static Builder<static>|AbandonedCart whereEmail($value)
 * @method static Builder<static>|AbandonedCart whereCartData($value)
 * @method static Builder<static>|AbandonedCart whereTotalAmount($value)
 * @method static Builder<static>|AbandonedCart whereTotalItems($value)
 * @method static Builder<static>|AbandonedCart whereLastActivity($value)
 * @method static Builder<static>|AbandonedCart whereFirstEmailSent($value)
 * @method static Builder<static>|AbandonedCart whereSecondEmailSent($value)
 * @method static Builder<static>|AbandonedCart whereThirdEmailSent($value)
 * @method static Builder<static>|AbandonedCart whereConverted($value)
 * @method static Builder<static>|AbandonedCart whereConvertedAt($value)
 * @method static Builder<static>|AbandonedCart whereCreatedAt($value)
 * @method static Builder<static>|AbandonedCart whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class AbandonedCart extends Core
{
    use HasFactory;

    protected $table = 'abandoned_carts';

    protected $casts = [
        'user_id' => 'int',
        'cart_data' => 'array',
        'total_amount' => 'float',
        'total_items' => 'int',
        'last_activity' => 'datetime',
        'first_email_sent' => 'datetime',
        'second_email_sent' => 'datetime',
        'third_email_sent' => 'datetime',
        'converted' => 'bool',
        'converted_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'session_id',
        'email',
        'cart_data',
        'total_amount',
        'total_items',
        'last_activity',
        'first_email_sent',
        'second_email_sent',
        'third_email_sent',
        'converted',
        'converted_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get cart items with product details
     */
    public function getCartItemsAttribute(): array
    {
        return collect($this->cart_data)->map(function ($item) {
            $product = \Modules\Product\Models\Product::find($item['product_id']);
            return [
                'product' => $product,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'amount' => $item['amount'],
            ];
        })->toArray();
    }

    /**
     * Check if this abandoned cart should receive first email
     */
    public function shouldSendFirstEmail(): bool
    {
        return $this->first_email_sent === null
            && $this->last_activity->diffInHours(now()) >= 1;
    }

    /**
     * Check if this abandoned cart should receive second email
     */
    public function shouldSendSecondEmail(): bool
    {
        return $this->first_email_sent !== null
            && $this->second_email_sent === null
            && $this->first_email_sent->diffInHours(now()) >= 24;
    }

    /**
     * Check if this abandoned cart should receive third email
     */
    public function shouldSendThirdEmail(): bool
    {
        return $this->second_email_sent !== null
            && $this->third_email_sent === null
            && $this->second_email_sent->diffInHours(now()) >= 72;
    }

    /**
     * Mark as converted
     */
    public function markAsConverted(): void
    {
        $this->update([
            'converted' => true,
            'converted_at' => now(),
        ]);
    }

    /**
     * Scope for carts that need first email
     */
    public function scopeNeedsFirstEmail(Builder $query): Builder
    {
        return $query->whereNull('first_email_sent')
            ->where('last_activity', '<=', now()->subHour())
            ->where('converted', false);
    }

    /**
     * Scope for carts that need second email
     */
    public function scopeNeedsSecondEmail(Builder $query): Builder
    {
        return $query->whereNotNull('first_email_sent')
            ->whereNull('second_email_sent')
            ->where('first_email_sent', '<=', now()->subDay())
            ->where('converted', false);
    }

    /**
     * Scope for carts that need third email
     */
    public function scopeNeedsThirdEmail(Builder $query): Builder
    {
        return $query->whereNotNull('second_email_sent')
            ->whereNull('third_email_sent')
            ->where('second_email_sent', '<=', now()->subDays(3))
            ->where('converted', false);
    }
}
