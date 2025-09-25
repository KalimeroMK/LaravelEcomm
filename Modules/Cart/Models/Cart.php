<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Cart\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Modules\Billing\Models\Wishlist;
use Modules\Cart\Database\Factories\CartFactory;
use Modules\Cart\Services\AbandonedCartService;
use Modules\Core\Models\Core;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;
use Modules\User\Models\User;

/**
 * Class Cart
 *
 * @property int $id
 * @property float $price
 * @property string $status
 * @property int $quantity
 * @property float $amount
 * @property int $product_id
 * @property int|null $order_id
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Order|null                 $order
 * @property-read Product                    $product
 * @property-read User|null                  $user
 * @property-read Collection<int, Wishlist>  $wishlists
 * @property-read int|null                   $wishlists_count
 *
 * @method static Builder<static>|Cart newModelQuery()
 * @method static Builder<static>|Cart newQuery()
 * @method static Builder<static>|Cart query()
 * @method static Builder<static>|Cart whereAmount($value)
 * @method static Builder<static>|Cart whereCreatedAt($value)
 * @method static Builder<static>|Cart whereId($value)
 * @method static Builder<static>|Cart whereOrderId($value)
 * @method static Builder<static>|Cart wherePrice($value)
 * @method static Builder<static>|Cart whereProductId($value)
 * @method static Builder<static>|Cart whereQuantity($value)
 * @method static Builder<static>|Cart whereStatus($value)
 * @method static Builder<static>|Cart whereUpdatedAt($value)
 * @method static Builder<static>|Cart whereUserId($value)
 *
 * @mixin Eloquent
 */
class Cart extends Core
{
    use HasFactory;

    protected $table = 'carts';

    protected $casts
        = [
            'product_id' => 'int',
            'order_id' => 'int',
            'user_id' => 'int',
            'price' => 'float',
            'quantity' => 'int',
            'amount' => 'float',
            'session_id' => 'string',
        ];

    protected $fillable
        = [
            'product_id',
            'order_id',
            'user_id',
            'price',
            'status',
            'quantity',
            'amount',
            'session_id',
        ];

    public static function Factory(): CartFactory
    {
        return CartFactory::new();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Boot method to add model events
     */
    protected static function boot(): void
    {
        parent::boot();

        // Track abandoned cart when cart is created
        static::created(function (Cart $cart): void {
            if ($cart->user_id || $cart->session_id) {
                app(AbandonedCartService::class)->trackAbandonedCart(
                    $cart->user,
                    $cart->session_id,
                    $cart->user?->email
                );
            }
        });

        // Update abandoned cart when cart is updated
        static::updated(function (Cart $cart): void {
            if ($cart->user_id || $cart->session_id) {
                app(AbandonedCartService::class)->trackAbandonedCart(
                    $cart->user,
                    $cart->session_id,
                    $cart->user?->email
                );
            }
        });

        // Mark as converted when cart is associated with an order
        static::updated(function (Cart $cart): void {
            if ($cart->order_id && $cart->wasChanged('order_id')) {
                app(AbandonedCartService::class)->markAsConverted(
                    $cart->user,
                    $cart->session_id
                );
            }
        });
    }
}
