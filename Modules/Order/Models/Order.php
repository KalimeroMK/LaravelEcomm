<?php

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\Order\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property string $phone
 * @property string|null $post_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Shipping|null $shipping
 * @property User|null $user
 * @property Collection|Cart[] $carts
 * @property-read Collection|Cart[] $cart_info
 * @property-read int|null $cart_info_count
 * @property-read int|null $carts_count
 *
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order query()
 * @method static Builder|Order whereAddress1($value)
 * @method static Builder|Order whereAddress2($value)
 * @method static Builder|Order whereCountry($value)
 * @method static Builder|Order whereCoupon($value)
 * @method static Builder|Order whereCreatedAt($value)
 * @method static Builder|Order whereEmail($value)
 * @method static Builder|Order whereFirstName($value)
 * @method static Builder|Order whereId($value)
 * @method static Builder|Order whereLastName($value)
 * @method static Builder|Order whereOrderNumber($value)
 * @method static Builder|Order wherePaymentMethod($value)
 * @method static Builder|Order wherePaymentStatus($value)
 * @method static Builder|Order wherePhone($value)
 * @method static Builder|Order wherePostCode($value)
 * @method static Builder|Order whereQuantity($value)
 * @method static Builder|Order whereShippingId($value)
 * @method static Builder|Order whereStatus($value)
 * @method static Builder|Order whereSubTotal($value)
 * @method static Builder|Order whereTotalAmount($value)
 * @method static Builder|Order whereUpdatedAt($value)
 * @method static Builder|Order whereUserId($value)
 *
 * @mixin Eloquent
 */
class Order extends Core
{
    use Filterable;
    use HasFactory;

    protected $table = 'orders';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'user_id' => 'int',
        'sub_total' => 'float',
        'shipping_id' => 'int',
        'total_amount' => 'float',
        'quantity' => 'int',
    ];

    protected $fillable = [
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

    public const likeRows = [
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

    public static function Factory(): OrderFactory
    {
        return OrderFactory::new();
    }

    /**
     * @return Builder|Builder[]|Collection|Model|null
     */
    public static function getAllOrder(int $id): Model|Collection|Builder|array|null
    {
        return Order::with('cart_info')->find($id);
    }

    public static function countActiveOrder(): int
    {
        $data = Order::count();
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
