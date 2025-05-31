<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Order\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Kalimeromk\Filterable\app\Traits\Filterable;
use Modules\Cart\Models\Cart;
use Modules\Core\Models\Core;
use Modules\Order\Database\Factories\OrderFactory;
use Modules\Shipping\Models\Shipping;
use Modules\User\Models\User;

/**
 * Class Order
 *
 * @property int $id
 * @property string|null $order_number
 * @property int|null $user_id
 * @property float $sub_total
 * @property int|null $shipping_id
 * @property float $total_amount
 * @property int $quantity
 * @property string $payment_method
 * @property string $payment_status
 * @property string $status
 * @property int|null $payer_id
 * @property string|null $transaction_reference
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Cart> $cart_info
 * @property-read int|null              $cart_info_count
 * @property-read Collection<int, Cart> $carts
 * @property-read int|null              $carts_count
 * @property-read Shipping|null         $shipping
 * @property-read User|null             $user
 *
 * @method static Builder<static>|Order filter(array $filters = [])
 * @method static Builder<static>|Order newModelQuery()
 * @method static Builder<static>|Order newQuery()
 * @method static Builder<static>|Order query()
 * @method static Builder<static>|Order whereCreatedAt($value)
 * @method static Builder<static>|Order whereId($value)
 * @method static Builder<static>|Order whereOrderNumber($value)
 * @method static Builder<static>|Order wherePayerId($value)
 * @method static Builder<static>|Order wherePaymentMethod($value)
 * @method static Builder<static>|Order wherePaymentStatus($value)
 * @method static Builder<static>|Order whereQuantity($value)
 * @method static Builder<static>|Order whereShippingId($value)
 * @method static Builder<static>|Order whereStatus($value)
 * @method static Builder<static>|Order whereSubTotal($value)
 * @method static Builder<static>|Order whereTotalAmount($value)
 * @method static Builder<static>|Order whereTransactionReference($value)
 * @method static Builder<static>|Order whereUpdatedAt($value)
 * @method static Builder<static>|Order whereUserId($value)
 *
 * @mixin Eloquent
 */
class Order extends Core
{
    use Filterable;
    use HasFactory;

    public const likeRows
        = [
            'user.name',
            'user.email',
            'order_number',
            'payment_method',
            'payment_status',
            'status',
            'email',
            'phone',
            'country',
            'post_code',
            'total_amount',

        ];

    protected $table = 'orders';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string>
     */
    protected array $dates
        = [
            'created_at',
            'updated_at',
        ];

    protected $casts
        = [
            'user_id' => 'int',
            'sub_total' => 'float',
            'shipping_id' => 'int',
            'total_amount' => 'float',
            'quantity' => 'int',
            'payer_id' => 'int',
        ];

    protected $fillable
        = [
            'order_number',
            'user_id',
            'sub_total',
            'shipping_id',
            'total_amount',
            'quantity',
            'payment_method',
            'payment_status',
            'status',
            'payer_id',
            'transaction_reference',
            'post_code',
        ];

    public static function Factory(): OrderFactory
    {
        return OrderFactory::new();
    }

    /**
     * @return Builder|Builder[]|Collection|Model|null
     */
    public static function getAllOrder(int $id): Model|Collection|Builder|array|null
    {
        return self::with('cart_info')->find($id);
    }

    public static function countActiveOrder(): int
    {
        $data = self::count();
        if ($data) {
            return $data;
        }

        return 0;
    }

    public function shipping(): BelongsTo
    {
        return $this->belongsTo(Shipping::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function cart_info(): HasMany
    {
        return $this->hasMany(Cart::class, 'order_id', 'id');
    }
}
